<?php

namespace App\Http\Controllers\Masyarakat;

use App\Ai\Agents\ReportClassifier;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Report;
use Illuminate\Http\Request;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Files\Image;

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
            'statusHistories',
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

        $photos = $request->file('photos');

        /* FOTO PERTAMA */

        $firstPhotoPath = $photos[0]->store(
            'reports',
            'public'
        );

        $report = Report::create([
            'user_id' => $request->user()->id,
            'category_id' => $validated['category_id'],

            'district_id' => null,
            'village_id' => null,

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

        /* KLASIFIKASI FOTO DENGAN GEMINI */

        $image = Image::fromPath(
            storage_path('app/public/' . $firstPhotoPath)
        );

        $response = (new ReportClassifier)->prompt(
            'Classify this image according to the UrbanEye categories.',
            attachments: [$image],
            provider: Lab::Gemini,
        );

        $report->update([
            'ai_category' => $response['category'],
            'ai_confidence' => $response['confidence'],
            'ai_response' => $response['reason'],
            'classified_at' => now(),
        ]);

        /* STATUS HISTORY */

        $report->statusHistories()->create([
            'changed_by' => $request->user()->id,
            'old_status' => 'menunggu',
            'new_status' => 'menunggu',
            'note' => 'Laporan dibuat oleh masyarakat.',
        ]);

        return redirect()
            ->route('masyarakat.reports.index')
            ->with(
                'success',
                'Laporan berhasil dikirim.'
            );
    }
}