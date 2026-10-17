<?php

namespace App\Http\Controllers\Masyarakat;

use App\Ai\Agents\ReportClassifier;
use App\Ai\Agents\ReportUrgencyAnalyzer;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\District;
use App\Models\Village;
use App\Models\Report;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Files\Image;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReportController extends Controller
{
    public function index(Request $request)
{
    $validated = $request->validate([
        'search' => [
            'nullable',
            'string',
            'max:100',
        ],

        'status' => [
            'nullable',
            'in:menunggu,diproses,selesai,ditolak',
        ],

        'category_id' => [
            'nullable',
            'exists:categories,id',
        ],

        'per_page' => [
            'nullable',
            'in:5,10,15,25',
        ],
    ]);

    $perPage = (int) ($validated['per_page'] ?? 5);

    $paginator = Report::with('category')
        ->where('user_id', auth()->id())

        ->when(
            $validated['search'] ?? null,
            function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }
        )

        ->when(
            $validated['status'] ?? null,
            function ($query, $status) {
                $query->where('status', $status);
            }
        )

        ->when(
            $validated['category_id'] ?? null,
            function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            }
        )

        ->latest()
        ->paginate($perPage)
        ->withQueryString();

    return inertia('Masyarakat/Reports', [
        'reports' => $paginator->items(),

        'categories' => Category::orderBy('name')->get(),

        'filters' => [
            'search' => $validated['search'] ?? '',
            'status' => $validated['status'] ?? '',
            'category_id' => $validated['category_id'] ?? '',
            'per_page' => $perPage,
        ],

        'pagination' => [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
        ],
    ]);
}

    
public function create()
{
    return inertia('Masyarakat/CreateReport', [
        'categories' => Category::orderBy('name')->get(),
        'districts' => District::orderBy('name')->get(),
        'villages' => Village::orderBy('name')->get(),
    ]);
}


    public function show(Report $report)
    {
        abort_unless(
            $report->user_id === auth()->id(),
            403
        );

        $report->load([
            'category',
            'district',
            'village',
            'images',
            'statusHistories' => function ($query) {
                $query->orderBy('changed_at', 'asc')
                    ->orderBy('id', 'asc');
            },
        ]);

        return inertia('Masyarakat/ReportDetail', [
            'report' => $report,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'district_id' => [
                'required',
                'exists:districts,id',
            ],

            'village_id' => [
                'required',
                'exists:villages,id',
            ],

            'description' => [
                'required',
                'string',
                'max:500',
            ],

            'photos' => [
                'required',
                'array',
                'min:1',
                'max:5',
            ],

            'photos.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:5120',
            ],

            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],

            'address' => [
                'required',
                'string',
                'max:500',
            ],
        ]);

        if (
            ! Village::where('id', $validated['village_id'])
                ->where('district_id', $validated['district_id'])
                ->exists()
        ) {
            return back()
                ->withErrors([
                    'village_id' => 'Kelurahan tidak sesuai dengan kecamatan yang dipilih.',
                ])
                ->withInput();
        }


        $photos = $request->file('photos');

        /* FOTO PERTAMA */

        $firstPhotoPath = $photos[0]->store(
            'reports',
            'public'
        );

        $report = Report::create([
            'user_id' => $request->user()->id,
            'category_id' => $validated['category_id'],

            'district_id' => $validated['district_id'],
            'village_id' => $validated['village_id'],
            
            'title' => $validated['title'],
            'description' => $validated['description'],

            'photo' => $firstPhotoPath,

            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'address' => $validated['address'],

            'verification_status' => 'pending',
            'status' => 'menunggu',
        ]);

        /* SIMPAN SEMUA FOTO */

        foreach ($photos as $index => $photo) {
            if ($index === 0) {
                $photoPath = $firstPhotoPath;
            } else {
                $photoPath = $photo->store(
                    'reports',
                    'public'
                );
            }

            $report->images()->create([
                'image' => $photoPath,
                'uploaded_at' => now(),
            ]);
        }

        
        /* SIAPKAN STATUS ANALISIS AI */

        $aiAnalysisFailed = false;

        $image = Image::fromPath(
            storage_path('app/public/' . $firstPhotoPath)
        );

        
        /* KLASIFIKASI FOTO DENGAN GEMINI */

        $response = null;

        try {
            $response = (new ReportClassifier)->prompt(
                'Klasifikasikan foto ini berdasarkan salah satu kategori UrbanEye:
                Sampah, Drainase, Lampu Jalan, Fasilitas Umum, atau Jalan Rusak.

                Berikan hasil klasifikasi dan alasan dalam Bahasa Indonesia.
                Nama kategori harus sama persis dengan salah satu kategori tersebut.',
                attachments: [$image],
                provider: [
                    Lab::Gemini->value => config('ai.providers.gemini.model'),
                ],
            );
        } catch (Throwable $primaryError) {
            Log::warning('UrbanEye primary classification model failed.', [
                'report_id' => $report->id,
                'error' => $primaryError->getMessage(),
            ]);

            // Coba model Gemini cadangan.
            try {
                $response = (new ReportClassifier)->prompt(
                    'Klasifikasikan foto ini berdasarkan salah satu kategori UrbanEye:
                    Sampah, Drainase, Lampu Jalan, Fasilitas Umum, atau Jalan Rusak.

                    Berikan hasil klasifikasi dan alasan dalam Bahasa Indonesia.
                    Nama kategori harus sama persis dengan salah satu kategori tersebut.',
                    attachments: [$image],
                    provider: [
                        Lab::Gemini->value => config('ai.providers.gemini.model'),
                    ],
                );
            } catch (Throwable $fallbackError) {
                $aiAnalysisFailed = true;

                Log::warning('UrbanEye classification fallback failed.', [
                    'report_id' => $report->id,
                    'primary_error' => $primaryError->getMessage(),
                    'fallback_error' => $fallbackError->getMessage(),
                ]);
            }
        }

        if ($response !== null) {
            $report->update([
                'ai_category' => $response['category'] ?? null,
                'ai_confidence' => $response['confidence'] ?? null,
                'ai_response' => $response['reason'] ?? null,
                'classified_at' => now(),
            ]);
        }

        /* ANALISIS URGENSI LAPORAN */

        try {
            $urgencyResponse = (new ReportUrgencyAnalyzer)->prompt(
                "Assess the urgency of this UrbanEye report.

                Title: {$report->title}
                Description: {$report->description}
                Category: {$report->category?->name}
                Address: {$report->address}
                Latitude: {$report->latitude}
                Longitude: {$report->longitude}",
                attachments: [$image],
                provider: [
                    Lab::Gemini->value => config('ai.providers.gemini.model'),
                ],
            );

            $report->update([
                'urgency_level' => $urgencyResponse['urgency_level'] ?? null,
                'urgency_reason' => $urgencyResponse['reason'] ?? null,
                'urgency_analyzed_at' => now(),
            ]);
        } catch (Throwable $e) {
            $aiAnalysisFailed = true;

            Log::warning('UrbanEye AI urgency analysis failed.', [
                'report_id' => $report->id,
                'exception' => get_class($e),
                'error' => $e->getMessage(),
            ]);
        }

        /* STATUS HISTORY */

        $report->statusHistories()->create([
            'changed_by' => $request->user()->id,
            'old_status' => 'menunggu',
            'new_status' => 'menunggu',
            'note' => 'Laporan dibuat oleh masyarakat.',
            'changed_at' => now(),
        ]);

        // Kirim notifikasi kepada semua Admin ketika ada laporan baru.
        $admins = User::role('admin')->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'report_id' => $report->id,
                'title' => 'Laporan Baru',
                'message' => 'Ada laporan baru yang perlu diperiksa.',
                'is_read' => false,
            ]);
        }

        return redirect()
        ->route('masyarakat.reports.index')
        ->with(
            'success',
            $aiAnalysisFailed
                ? 'Laporan berhasil dikirim, tetapi sebagian analisis AI belum berhasil. Laporan tetap menunggu pemeriksaan Admin.'
                : 'Laporan berhasil dikirim.'
        );
    }
}