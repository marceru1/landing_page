<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240', // 10MB max
            'description' => 'nullable|string|max:1000',
        ]);

        $path = $request->file('image')->store('photos', 'public');

        Photo::create([
            'user_id' => auth()->id(),
            'image_path' => $path,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Foto adicionada ao feed com sucesso!');
    }

    public function destroy(Photo $photo)
    {
        if ($photo->user_id !== auth()->id()) {
            abort(403);
        }

        if (Storage::disk('public')->exists($photo->image_path)) {
            Storage::disk('public')->delete($photo->image_path);
        }
        
        $photo->delete();

        return redirect()->back()->with('success', 'Foto excluída com sucesso!');
    }
}
