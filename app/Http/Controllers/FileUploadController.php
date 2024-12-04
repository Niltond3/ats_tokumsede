<?php

// app/Http/Controllers/FileUploadController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        // Validate the incoming file
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,gif|max:5120' // 5MB max
        ]);


        // Verifica se o arquivo foi enviado e é válido
        if ($request->hasFile('image') && $request->file('image')->isValid()) {

            // Generate a unique filename
            $filename = Str::uuid() . '.' . $request->file('image')->getClientOriginalExtension();

            // Store the file in public/images/uploads
            $path = $request->file('image')->move(public_path('images/uploads'), $filename);

            // Return response
            return response()->json([
                'message' => 'Image uploaded successfully',
                'path' => '/images/uploads/' . $filename,
                'fineName' => $filename
            ], 200);
        }

        return response()->json(['error' => 'Arquivo inválido'], 400);
    }
}
