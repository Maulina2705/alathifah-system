<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $profile = $user->teacherProfile;

        return view('profile.show', compact('user', 'profile'));
    }

    public function showVerification()
    {
        $user = Auth::user();
        $profile = $user->teacherProfile ?? new TeacherProfile(['user_id' => $user->id]);

        return view('profile.verify', compact('user', 'profile'));
    }

    public function processVerification(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nip' => 'nullable|string|max:50',
            'nik' => 'nullable|string|max:30',
            'title_degree' => 'nullable|string|max:50',
            'position' => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
            'level' => 'required|in:SD,SMP,KEDUANYA',
            'is_confirmed' => 'accepted',
        ], [
            'is_confirmed.accepted' => 'Anda wajib mencentang pernyataan bahwa Anda telah memeriksa dan memastikan biodata Anda benar.',
        ]);

        $profile = TeacherProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nip' => $request->nip,
                'nik' => $request->nik,
                'title_degree' => $request->title_degree,
                'position' => $request->position,
                'phone' => $request->phone,
                'level' => $request->level,
                'is_verified' => true,
                'verified_at' => now(),
            ]
        );

        AuditLog::log(
            'VERIFY_PROFILE',
            "Guru {$user->name} telah memverifikasi biodata dirinya.",
            null,
            null,
            ['profile_id' => $profile->id, 'nip' => $profile->nip, 'degree' => $profile->title_degree]
        );

        return redirect()->route('dashboard')->with('success', 'Biodata Anda berhasil diverifikasi. Nama dan gelar ini akan dicantumkan secara resmi pada tanda tangan raport.');
    }
}
