<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandingPagePelamarController extends Controller
{
    /**
     * Tampilkan landing page pelamar (Upload CV + Riwayat CV)
     * Bisa diakses tanpa login. Riwayat CV hanya terisi kalau sudah login.
     */
    public function index()
    {
        $riwayatCv = [];

        if (Auth::check()) {
            // Nanti ganti dengan query database:
            // $riwayatCv = auth()->user()->cvs()->latest()->paginate(5);
        }

        return view('pages.landing_page_pelamar', compact('riwayatCv'));
    }

    /**
     * Simpan CV yang diupload (wajib login, dijaga middleware auth di route)
     */
    public function store(Request $request)
    {
        $request->validate([
            'job_title' => 'required|string|max:255',
            'cv_file'   => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB
        ]);

        $path = $request->file('cv_file')->store('cv_uploads', 'public');

        // auth()->user()->cvs()->create([
        //     'job_title' => $request->job_title,
        //     'file_path' => $path,
        //     'file_name' => $request->file('cv_file')->getClientOriginalName(),
        // ]);

        return back()->with('success', 'CV berhasil diupload!');
    }

    /**
     * Hapus CV dari riwayat (wajib login, dijaga middleware auth di route)
     */
    public function destroy($id)
    {
        // $cv = auth()->user()->cvs()->findOrFail($id);
        // Storage::disk('public')->delete($cv->file_path);
        // $cv->delete();

        return back()->with('success', 'CV berhasil dihapus!');
    }
}