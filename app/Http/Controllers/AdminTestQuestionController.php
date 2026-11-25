<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePosttestQuestionRequest;
use App\Http\Requests\StorePretestQuestionRequest;
use App\Models\EducationalModule;
use App\Models\TestQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AdminTestQuestionController extends Controller
{
    private function authorizeAdmin(): void
    {
        if (! Auth::check() || ! Auth::user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman pengelolaan soal evaluasi.');
        }
    }

    public function indexPretest(Request $request): Response
    {
        $this->authorizeAdmin();

        $modules = EducationalModule::orderBy('module_number')->get();
        $countsByModule = TestQuestion::preTest()
            ->selectRaw('module_target, COUNT(*) as aggregate')
            ->groupBy('module_target')
            ->pluck('aggregate', 'module_target');

        $defaultModule = $modules->first()?->module_number ?? 1;
        $selectedModule = (int) $request->query('module', $defaultModule);
        $searchQuery = trim((string) $request->query('q', ''));

        $questions = TestQuestion::preTest()
            ->where('module_target', $selectedModule)
            ->when($searchQuery !== '', function ($query) use ($searchQuery): void {
                $query->where('question_text', 'like', "%{$searchQuery}%");
            })
            ->orderBy('id')
            ->get()
            ->map(fn (TestQuestion $q): array => [
                'id' => $q->id,
                'module_target' => $q->module_target,
                'question_text' => $q->question_text,
                'options' => $q->options,
                'correct_answer' => strtoupper($q->correct_answer),
                'explanation' => $q->explanation,
            ]);

        $moduleItems = $modules->map(fn (EducationalModule $m): array => [
            'id' => $m->id,
            'module_number' => $m->module_number,
            'title' => $m->title,
            'questions_count' => (int) ($countsByModule[$m->module_number] ?? 0),
        ]);

        return Inertia::render('Admin/PretestQuestions/Index', [
            'modules' => $moduleItems,
            'selectedModule' => $selectedModule,
            'searchQuery' => $searchQuery,
            'questions' => $questions,
            'totalPretestQuestions' => (int) $countsByModule->sum(),
        ]);
    }

    public function storePretest(StorePretestQuestionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        TestQuestion::create([
            'question_type' => TestQuestion::TYPE_PRETEST,
            'module_target' => (int) $validated['module_target'],
            'question_text' => $validated['question_text'],
            'options' => $validated['options'],
            'correct_answer' => $validated['correct_answer'],
            'explanation' => $validated['explanation'] ?? null,
        ]);

        return redirect()
            ->route('admin.pretest-questions.index', ['module' => $validated['module_target']])
            ->with('success', 'Butir soal Pre-Test Modul '.$validated['module_target'].' berhasil ditambahkan.');
    }

    public function updatePretest(StorePretestQuestionRequest $request, int $id): RedirectResponse
    {
        $question = TestQuestion::preTest()->findOrFail($id);
        $validated = $request->validated();

        $question->update([
            'module_target' => (int) $validated['module_target'],
            'question_text' => $validated['question_text'],
            'options' => $validated['options'],
            'correct_answer' => $validated['correct_answer'],
            'explanation' => $validated['explanation'] ?? null,
        ]);

        return redirect()
            ->route('admin.pretest-questions.index', ['module' => $validated['module_target']])
            ->with('success', 'Butir soal Pre-Test Modul '.$validated['module_target'].' berhasil diperbarui.');
    }

    public function destroyPretest(int $id): RedirectResponse
    {
        $this->authorizeAdmin();

        $question = TestQuestion::preTest()->findOrFail($id);
        $moduleTarget = $question->module_target;
        $question->delete();

        return redirect()
            ->route('admin.pretest-questions.index', ['module' => $moduleTarget])
            ->with('success', 'Butir soal Pre-Test berhasil dihapus.');
    }

    public function indexPosttest(Request $request): Response
    {
        $this->authorizeAdmin();

        $searchQuery = trim((string) $request->query('q', ''));

        $questions = TestQuestion::postTest()
            ->when($searchQuery !== '', function ($query) use ($searchQuery): void {
                $query->where('question_text', 'like', "%{$searchQuery}%");
            })
            ->orderBy('id')
            ->get()
            ->map(fn (TestQuestion $q): array => [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'options' => $q->options,
                'correct_answer' => strtoupper($q->correct_answer),
                'explanation' => $q->explanation,
            ]);

        return Inertia::render('Admin/PosttestQuestions/Index', [
            'searchQuery' => $searchQuery,
            'questions' => $questions,
            'totalPosttestQuestions' => TestQuestion::postTest()->count(),
        ]);
    }

    public function storePosttest(StorePosttestQuestionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        TestQuestion::create([
            'question_type' => TestQuestion::TYPE_POSTTEST,
            'module_target' => null,
            'question_text' => $validated['question_text'],
            'options' => $validated['options'],
            'correct_answer' => $validated['correct_answer'],
            'explanation' => $validated['explanation'] ?? null,
        ]);

        return redirect()
            ->route('admin.posttest-questions.index')
            ->with('success', 'Butir soal Post-Test umum berhasil ditambahkan.');
    }

    public function updatePosttest(StorePosttestQuestionRequest $request, int $id): RedirectResponse
    {
        $question = TestQuestion::postTest()->findOrFail($id);
        $validated = $request->validated();

        $question->update([
            'module_target' => null,
            'question_text' => $validated['question_text'],
            'options' => $validated['options'],
            'correct_answer' => $validated['correct_answer'],
            'explanation' => $validated['explanation'] ?? null,
        ]);

        return redirect()
            ->route('admin.posttest-questions.index')
            ->with('success', 'Butir soal Post-Test umum berhasil diperbarui.');
    }

    public function destroyPosttest(int $id): RedirectResponse
    {
        $this->authorizeAdmin();

        $question = TestQuestion::postTest()->findOrFail($id);
        $question->delete();

        return redirect()
            ->route('admin.posttest-questions.index')
            ->with('success', 'Butir soal Post-Test umum berhasil dihapus.');
    }
}
