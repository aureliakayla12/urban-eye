<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\District;
use App\Models\Report;
use App\Models\Village;
use Inertia\Inertia;

class MapController extends Controller
{
    public function index()
    {
        $reports = Report::with([
            'category:id,name',
            'district:id,name',
            'village:id,name',
        ])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest()
            ->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'title' => $report->title,
                    'description' => $report->description,
                    'latitude' => (float) $report->latitude,
                    'longitude' => (float) $report->longitude,
                    'address' => $report->address,
                    'status' => $report->status,
                    'verification_status' => $report->verification_status,
                    'category' => $report->category
                        ? [
                            'id' => $report->category->id,
                            'name' => $report->category->name,
                        ]
                        : null,
                    'district' => $report->district
                        ? [
                            'id' => $report->district->id,
                            'name' => $report->district->name,
                        ]
                        : null,
                    'village' => $report->village
                        ? [
                            'id' => $report->village->id,
                            'name' => $report->village->name,
                        ]
                        : null,
                    'created_at' => $report->created_at?->format('d M Y H:i'),
                ];
            });

        $categories = Category::orderBy('name')
            ->get(['id', 'name']);

        $districts = District::orderBy('name')
            ->get(['id', 'name']);

        $villages = Village::orderBy('name')
            ->get(['id', 'district_id', 'name']);

        return Inertia::render('Admin/Map/Index', [
            'reports' => $reports,
            'categories' => $categories,
            'districts' => $districts,
            'villages' => $villages,
        ]);
    }
}