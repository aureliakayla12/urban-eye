<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Category;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $totalReports = Report::count();
        $waitingReports = Report::where(
            'status',
            'menunggu'
        )->count();
        $processingReports = Report::where(
            'status',
            'diproses'
        )->count();
        $completedReports = Report::where(
            'status',
            'selesai'
        )->count();

        $categoryStatistics = Category::withCount('reports')
            ->orderByDesc('reports_count')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'total' => $category->reports_count,
                ];
            });

        $weeklyStatistics = collect();

        for ($i = 6; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()
                ->subWeeks($i)
                ->startOfWeek();

            $endOfWeek = Carbon::now()
                ->subWeeks($i)
                ->endOfWeek();

            $total = Report::whereBetween(
                'created_at',
                [$startOfWeek, $endOfWeek]
            )->count();

            $weeklyStatistics->push([
                'label' => $startOfWeek->format('d M'),
                'total' => $total,
            ]);
        }

        $districtStatistics = District::withCount('reports')
            ->orderByDesc('reports_count')
            ->get()
            ->map(function ($district) {
                return [
                    'id' => $district->id,
                    'name' => $district->name,
                    'total' => $district->reports_count,
                ];
            });

        $villageStatistics = Village::withCount('reports')
            ->orderByDesc('reports_count')
            ->get()
            ->map(function ($village) {
                return [
                    'id' => $village->id,
                    'name' => $village->name,
                    'total' => $village->reports_count,
                ];
            });

        return Inertia::render('Admin/Statistics/Index', [
            'summary' => [
                'total' => $totalReports,
                'waiting' => $waitingReports,
                'processing' => $processingReports,
                'completed' => $completedReports,
            ],

            'categoryStatistics' => $categoryStatistics,
            'weeklyStatistics' => $weeklyStatistics,
            'districtStatistics' => $districtStatistics,
            'villageStatistics' => $villageStatistics,
        ]);
    }
}