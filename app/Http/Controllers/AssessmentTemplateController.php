<?php

namespace App\Http\Controllers;

use App\Models\AssessmentTemplate;
use App\Models\AssessmentTemplateItem;
use App\Models\AssessmentTemplateSection;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssessmentTemplateController extends Controller
{
    public function index()
    {
        $templates = AssessmentTemplate::with(['sections.items', 'creator'])
            ->latest()
            ->get();

        return view('templates.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'level' => 'required|in:SD,SMP,SEMUA',
            'description' => 'nullable|string',
        ]);

        $template = AssessmentTemplate::create([
            'name' => $request->name,
            'level' => $request->level,
            'description' => $request->description,
            'created_by_user_id' => Auth::id(),
            'is_active' => true,
        ]);

        AuditLog::log('CREATE_TEMPLATE', "Membuat template penilaian '{$template->name}'");

        return redirect()->route('templates.show', $template->id)
            ->with('success', "Template '{$template->name}' berhasil dibuat. Silakan tambahkan materi & indikator.");
    }

    public function show(int $id)
    {
        $template = AssessmentTemplate::with(['sections.items'])->findOrFail($id);

        return view('templates.show', compact('template'));
    }

    public function addSection(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:150',
        ]);

        $template = AssessmentTemplate::findOrFail($id);
        $order = $template->sections()->max('order') ?? 0;

        AssessmentTemplateSection::create([
            'assessment_template_id' => $template->id,
            'name' => $request->name,
            'order' => $order + 1,
        ]);

        return back()->with('success', "Materi '{$request->name}' berhasil ditambahkan ke template.");
    }

    public function deleteSection(int $sectionId)
    {
        $section = AssessmentTemplateSection::findOrFail($sectionId);
        $name = $section->name;
        $section->delete();

        return back()->with('success', "Materi '{$name}' berhasil dihapus.");
    }

    public function addItem(Request $request, int $sectionId)
    {
        $request->validate([
            'name' => 'required|string|max:150',
        ]);

        $section = AssessmentTemplateSection::findOrFail($sectionId);
        $order = $section->items()->max('order') ?? 0;

        AssessmentTemplateItem::create([
            'assessment_template_section_id' => $section->id,
            'name' => $request->name,
            'order' => $order + 1,
        ]);

        return back()->with('success', "Indikator '{$request->name}' berhasil ditambahkan.");
    }

    public function deleteItem(int $itemId)
    {
        $item = AssessmentTemplateItem::findOrFail($itemId);
        $name = $item->name;
        $item->delete();

        return back()->with('success', "Indikator '{$name}' berhasil dihapus.");
    }

    public function duplicate(int $id)
    {
        $template = AssessmentTemplate::with('sections.items')->findOrFail($id);

        DB::transaction(function () use ($template) {
            $newTpl = AssessmentTemplate::create([
                'name' => $template->name.' (Salinan)',
                'level' => $template->level,
                'description' => $template->description,
                'created_by_user_id' => Auth::id(),
                'is_active' => true,
            ]);

            foreach ($template->sections as $sec) {
                $newSec = AssessmentTemplateSection::create([
                    'assessment_template_id' => $newTpl->id,
                    'name' => $sec->name,
                    'order' => $sec->order,
                ]);

                foreach ($sec->items as $item) {
                    AssessmentTemplateItem::create([
                        'assessment_template_section_id' => $newSec->id,
                        'name' => $item->name,
                        'order' => $item->order,
                    ]);
                }
            }

            AuditLog::log('DUPLICATE_TEMPLATE', "Menduplikasi template {$template->name} menjadi {$newTpl->name}");
        });

        return back()->with('success', "Template '{$template->name}' berhasil diduplikasi.");
    }
}
