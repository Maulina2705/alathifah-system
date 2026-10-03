<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ReportSetting;
use Illuminate\Http\Request;

class ReportSettingController extends Controller
{
    public function index()
    {
        $sdSetting = ReportSetting::firstOrCreate(
            ['level' => 'SD'],
            [
                'school_name' => 'SD ISLAM RIAU GLOBAL TERPADU',
                'header_title' => 'LAPORAN PERKEMBANGAN TAHFIZH AL ATHIFA',
                'city' => 'Pekanbaru',
                'principal_name' => 'Era Mutia, S.Pd, Gr.',
                'principal_nip' => '198506242010012015',
                'paper_size' => 'A4',
            ]
        );

        $smpSetting = ReportSetting::firstOrCreate(
            ['level' => 'SMP'],
            [
                'school_name' => 'SMP ISLAM RIAU GLOBAL TERPADU',
                'header_title' => 'LAPORAN PERKEMBANGAN TAHFIZH AL ATHIFA',
                'city' => 'Pekanbaru',
                'principal_name' => 'Drs. H. M. Syukri, M.Pd.',
                'principal_nip' => '197904152005011004',
                'paper_size' => 'A4',
            ]
        );

        return view('admin.settings.index', compact('sdSetting', 'smpSetting'));
    }

    public function update(Request $request, string $level)
    {
        $setting = ReportSetting::where('level', $level)->firstOrFail();

        $data = $request->validate([
            'school_name' => 'required|string|max:150',
            'header_title' => 'required|string|max:150',
            'school_address' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'principal_name' => 'required|string|max:150',
            'principal_nip' => 'nullable|string|max:50',
            'homeroom_label' => 'required|string|max:50',
            'teacher_label' => 'required|string|max:50',
            'principal_label' => 'required|string|max:50',
            'default_report_date' => 'nullable|date',
            'paper_size' => 'required|string|max:20',
            'margin_top' => 'required|integer|min:5|max:50',
            'margin_bottom' => 'required|integer|min:5|max:50',
            'margin_left' => 'required|integer|min:5|max:50',
            'margin_right' => 'required|integer|min:5|max:50',
        ]);

        $setting->update($data);

        AuditLog::log('UPDATE_SETTING', "Admin memperbarui konfigurasi raport jenjang {$level}");

        return back()->with('success', "Pengaturan raport jenjang {$level} berhasil diperbarui.");
    }
}
