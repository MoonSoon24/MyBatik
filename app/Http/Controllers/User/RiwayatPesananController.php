<?php

namespace App\Http\Controllers\User;

use App\Models\RiwayatPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Http\Controllers\Controller;

class RiwayatPesananController extends Controller
{

    public function index(): View
    {
        $userId = Auth::id();

        $riwayatPesanan = RiwayatPesanan::where('user_id', $userId)
            ->with('order.review')
            ->latest()
            ->paginate(10);

        return view('user.history', compact('riwayatPesanan'));
    }
}