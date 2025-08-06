<?php

namespace App\Http\Controllers;

use App\Models\Design;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        $designs = Design::where('is_public', true)
                         ->with(['user', 'comments.user', 'likes'])
                         ->latest()
                         ->paginate(9);

        return view('gallery', compact('designs'));
    }
}