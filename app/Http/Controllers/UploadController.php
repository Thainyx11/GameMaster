<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Upload une image envoyée par l'utilisateur
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        try {
            $file = $request->file('image');
            $filename = 'uploads/' . Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Stocker dans storage/app/public/uploads/
            Storage::disk('public')->put($filename, file_get_contents($file));

            return response()->json([
                'success' => true,
                'path' => $filename,
                'url' => asset('storage/' . $filename),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de l\'upload : ' . $e->getMessage(),
            ], 500);
        }
    }
}
