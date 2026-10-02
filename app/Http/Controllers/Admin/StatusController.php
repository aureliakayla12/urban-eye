<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class StatusController extends Controller
{
    public function index()
    {
        $statuses = [
            [
                'value' => 'menunggu',
                'label' => 'Menunggu',
            ],
            [
                'value' => 'diproses',
                'label' => 'Diproses',
            ],
            [
                'value' => 'selesai',
                'label' => 'Selesai',
            ],
            [
                'value' => 'ditolak',
                'label' => 'Ditolak',
            ],
        ];

        return Inertia::render('Admin/Statuses/Index', [
            'statuses' => $statuses,
        ]);
    }
}