<?php

namespace App\Http\Controllers;

use App\Exports\TeacherTemplateExport;
use App\Models\AuditLog;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Spatie\Permission\Models\Role;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $role = $request->query('role');

        $users = User::when($search, function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%");
        })
            ->when($role, fn ($q) => $q->role($role))
            ->with(['roles', 'teacherProfile'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $roles = Role::all();

        return view('admin.teachers.index', compact('users', 'search', 'role', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|exists:roles,name',
            'nip' => 'nullable|string|max:50',
            'title_degree' => 'nullable|string|max:50',
            'level' => 'required|in:SD,SMP,KEDUANYA',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        $user->assignRole($request->role);

        TeacherProfile::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
            'title_degree' => $request->title_degree,
            'position' => $request->role,
            'level' => $request->level,
            'is_verified' => false,
        ]);

        AuditLog::log('CREATE_USER', "Admin membuat akun baru: {$user->name} ({$request->role})");

        return back()->with('success', "Akun {$user->name} berhasil dibuat dengan role {$request->role}.");
    }

    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|exists:roles,name',
            'nip' => 'nullable|string|max:50',
            'title_degree' => 'nullable|string|max:50',
            'level' => 'required|in:SD,SMP,KEDUANYA',
            'is_active' => 'required|boolean',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => (bool) $request->is_active,
        ]);

        $user->syncRoles([$request->role]);

        TeacherProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nip' => $request->nip,
                'title_degree' => $request->title_degree,
                'level' => $request->level,
            ]
        );

        AuditLog::log('UPDATE_USER', "Admin memperbarui data akun {$user->name}");

        return back()->with('success', "Data akun {$user->name} berhasil diperbarui.");
    }

    public function resetPassword(Request $request, int $id)
    {
        $request->validate([
            'new_password' => 'required|string|min:6',
        ]);

        $user = User::findOrFail($id);
        $user->update(['password' => Hash::make($request->new_password)]);

        AuditLog::log('RESET_PASSWORD', "Admin me-reset password akun {$user->name}");

        return back()->with('success', "Password akun {$user->name} berhasil di-reset.");
    }

    public function downloadTemplate()
    {
        return Excel::download(
            new TeacherTemplateExport,
            'Template_Import_Guru.xlsx'
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray();

        if (count($rows) <= 1) {
            return back()->with('error', 'File Excel kosong atau hanya berisi baris judul.');
        }

        array_shift($rows); // Remove header
        $importedCount = 0;
        $updatedCount = 0;

        foreach ($rows as $row) {
            $name = trim((string) ($row[0] ?? ''));
            $email = trim((string) ($row[1] ?? ''));
            $rawPassword = trim((string) ($row[2] ?? ''));
            $role = strtoupper(trim((string) ($row[3] ?? 'GURU')));
            $nip = trim((string) ($row[4] ?? ''));
            $nik = trim((string) ($row[5] ?? ''));
            $degree = trim((string) ($row[6] ?? ''));
            $position = trim((string) ($row[7] ?? ''));
            $level = strtoupper(trim((string) ($row[8] ?? 'SD')));
            $phone = trim((string) ($row[9] ?? ''));

            if ($name === '' || $email === '') {
                continue;
            }

            if (! in_array($role, ['GURU', 'WALI KELAS', 'KEPALA SEKOLAH', 'SUPER ADMIN'])) {
                $role = 'GURU';
            }
            if (! in_array($level, ['SD', 'SMP', 'KEDUANYA'])) {
                $level = 'SD';
            }

            $password = $rawPassword !== '' ? $rawPassword : 'password';

            $user = User::where('email', $email)->first();
            if ($user) {
                $user->update([
                    'name' => $name,
                ]);
                $user->syncRoles([$role]);

                TeacherProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nip' => $nip ?: null,
                        'nik' => $nik ?: null,
                        'title_degree' => $degree ?: null,
                        'position' => $position ?: $role,
                        'level' => $level,
                        'phone' => $phone ?: null,
                    ]
                );
                $updatedCount++;
            } else {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'is_active' => true,
                ]);
                $user->assignRole($role);

                TeacherProfile::create([
                    'user_id' => $user->id,
                    'nip' => $nip ?: null,
                    'nik' => $nik ?: null,
                    'title_degree' => $degree ?: null,
                    'position' => $position ?: $role,
                    'level' => $level,
                    'phone' => $phone ?: null,
                    'is_verified' => false,
                ]);
                $importedCount++;
            }
        }

        AuditLog::log('IMPORT_TEACHERS_EXCEL', "Admin mengimpor akun guru via Excel: {$importedCount} baru, {$updatedCount} diperbarui.");

        return back()->with('success', "Berhasil memproses Excel: {$importedCount} akun baru dibuat, {$updatedCount} akun diperbarui.");
    }
}
