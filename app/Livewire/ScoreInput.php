<?php

namespace App\Livewire;

use App\Models\AuditLog;
use App\Models\StudentAssessment;
use App\Models\StudentAssessmentItem;
use App\Models\StudentAssessmentSection;
use App\Services\AssessmentService;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ScoreInput extends Component
{
    public int $assessmentId;

    public ?string $newSectionName = '';

    public ?string $newItemName = '';

    public ?int $selectedSectionId = null;

    public bool $showAddItemModal = false;

    public bool $showAddSectionModal = false;

    public bool $showSubmitModal = false;

    public ?string $submissionNotes = '';

    public ?string $feedbackMessage = null;

    public ?string $errorMessage = null;

    public function mount(int $assessmentId)
    {
        $this->assessmentId = $assessmentId;
    }

    public function getAssessmentProperty(): StudentAssessment
    {
        return StudentAssessment::with([
            'student',
            'academicYear',
            'semester',
            'teacher.teacherProfile',
            'sections.items',
        ])->findOrFail($this->assessmentId);
    }

    public function updateScore(int $itemId, $score)
    {
        $assessment = $this->assessment;
        $user = Auth::user();

        if (! $assessment->canEdit() && ! $user->isSuperAdmin()) {
            $this->errorMessage = "Raport ini berstatus {$assessment->status} dan tidak dapat diedit.";

            return;
        }

        $item = StudentAssessmentItem::findOrFail($itemId);
        $cleanScore = null;

        if ($score !== '' && $score !== null) {
            $num = (int) $score;
            if ($num > 0 && $num <= 100) {
                $cleanScore = $num;
            } elseif ($num === 0) {
                $cleanScore = null; // 0 dianggap belum diisi
            } else {
                $this->errorMessage = 'Nilai harus berupa angka antara 1 sampai 100.';

                return;
            }
        }

        app(AssessmentService::class)->updateScore($item, $cleanScore);
        $this->feedbackMessage = "Nilai {$item->name} berhasil disimpan.";
        $this->errorMessage = null;
    }

    public function openAddItemModal(int $sectionId)
    {
        $this->selectedSectionId = $sectionId;
        $this->newItemName = '';
        $this->showAddItemModal = true;
    }

    public function saveItem()
    {
        $this->validate([
            'newItemName' => 'required|string|max:150',
            'selectedSectionId' => 'required|exists:student_assessment_sections,id',
        ]);

        $section = StudentAssessmentSection::findOrFail($this->selectedSectionId);
        app(AssessmentService::class)->addItem($section, $this->newItemName);

        $this->newItemName = '';
        $this->showAddItemModal = false;
        $this->feedbackMessage = 'Indikator baru berhasil ditambahkan.';
    }

    public function deleteItem(int $itemId)
    {
        $assessment = $this->assessment;
        if (! $assessment->canEdit() && ! Auth::user()->isSuperAdmin()) {
            $this->errorMessage = "Raport tidak dapat diedit pada status {$assessment->status}.";

            return;
        }

        $item = StudentAssessmentItem::findOrFail($itemId);
        app(AssessmentService::class)->deleteItem($item);
        $this->feedbackMessage = 'Indikator berhasil dihapus.';
    }

    public function openAddSectionModal()
    {
        $this->newSectionName = '';
        $this->showAddSectionModal = true;
    }

    public function saveSection()
    {
        $this->validate([
            'newSectionName' => 'required|string|max:150',
        ]);

        $assessment = $this->assessment;
        app(AssessmentService::class)->addSection($assessment, $this->newSectionName);

        $this->newSectionName = '';
        $this->showAddSectionModal = false;
        $this->feedbackMessage = 'Materi baru berhasil ditambahkan.';
    }

    public function deleteSection(int $sectionId)
    {
        $assessment = $this->assessment;
        if (! $assessment->canEdit() && ! Auth::user()->isSuperAdmin()) {
            $this->errorMessage = "Raport tidak dapat diedit pada status {$assessment->status}.";

            return;
        }

        $section = StudentAssessmentSection::findOrFail($sectionId);
        $name = $section->name;
        $section->delete();

        AuditLog::log(
            'DELETE_SECTION',
            "Menghapus materi '{$name}' dari siswa {$assessment->student->name}",
            $assessment->student_id
        );

        $this->feedbackMessage = 'Materi berhasil dihapus.';
    }

    public function submitReport()
    {
        try {
            $assessment = $this->assessment;
            app(WorkflowService::class)->submit($assessment, Auth::user(), $this->submissionNotes);
            $this->showSubmitModal = false;
            $this->feedbackMessage = 'Raport berhasil disubmit untuk direview oleh Wali Kelas.';
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->showSubmitModal = false;
        }
    }

    public function unlockReport()
    {
        if (! Auth::user()->isSuperAdmin()) {
            $this->errorMessage = 'Hanya Super Admin yang dapat membuka kunci (unlock) raport.';

            return;
        }

        $assessment = $this->assessment;
        app(WorkflowService::class)->unlock($assessment, Auth::user(), 'Dibuka kunci oleh Super Admin.');
        $this->feedbackMessage = 'Raport berhasil di-unlock dan kembali ke status DRAFT.';
    }

    public function render()
    {
        return view('livewire.score-input', [
            'assessment' => $this->assessment,
        ]);
    }
}
