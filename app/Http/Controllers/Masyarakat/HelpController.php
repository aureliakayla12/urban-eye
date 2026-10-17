<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class HelpController extends Controller
{
    public function index()
    {
        $whatsappNumber = preg_replace(
            '/\D+/',
            '',
            (string) env('URBANEYE_ADMIN_WHATSAPP', '')
        );

        $message = 'Halo Admin UrbanEye, saya membutuhkan bantuan terkait penggunaan UrbanEye.';

        $whatsappUrl = $whatsappNumber
            ? 'https://wa.me/' . $whatsappNumber
                . '?text=' . urlencode($message)
            : null;

        return Inertia::render('Masyarakat/Help', [
            'whatsappUrl' => $whatsappUrl,
        ]);
    }
}