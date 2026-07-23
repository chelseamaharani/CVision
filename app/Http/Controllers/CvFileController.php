<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Support\Facades\Storage;

class CvFileController extends Controller
{
    /**
     * Menampilkan file CV dari storage tanpa bergantung pada symlink public/storage.
     * Berguna untuk deployment di Railway yang tidak memiliki symlink storage.
     */
    public function show($id)
    {
        $cv = Cv::findOrFail($id);
        
        $filePath = $cv->file_path;
        
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'CV file not found.');
        }
        
        return Storage::disk('public')->response($filePath, $cv->file_name);
    }
}