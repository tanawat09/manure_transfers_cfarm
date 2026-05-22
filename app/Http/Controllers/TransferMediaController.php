<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class TransferMediaController extends Controller
{
    public function show(string $path)
    {
        $path = ltrim($path, '/');

        abort_if($path === '' || str_contains($path, '..'), 404);
        abort_unless(Storage::disk('public')->exists($path), 404);

        return response()->file(Storage::disk('public')->path($path));
    }
}
