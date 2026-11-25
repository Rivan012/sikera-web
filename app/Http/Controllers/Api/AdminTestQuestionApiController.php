<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TestQuestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminTestQuestionApiController extends Controller
{
    private function ensureAdmin(Request $request): ?JsonResponse
    {
        $user = $request->user();
        if (! $user || ! $user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang berhak mengelola bank soal evaluasi.',
            ], 403);
        }

        return null;
    }

    /**
     * @return array<int, array{label: string, text: string}>|null
     */
    private function normalizePayloadOptions(Request $request): ?array
    {
        if (is_array($request->input('options'))) {
            return TestQuestion::normalizeOptions($request->input('options'));
        }

        if ($request->hasAny(['option_a', 'option_b', 'option_c', 'option_d'])) {
            return [
                ['label' => 'A', 'text' => trim((string) $request->input('option_a', ''))],
                ['label' => 'B', 'text' => trim((string) $request->input('option_b', ''))],
                ['label' => 'C', 'text' => trim((string) $request->input('option_c', ''))],
                ['label' => 'D', 'text' => trim((string) $request->input('option_d', ''))],
            ];
        }

        return null;
    }

    public function indexPretest(Request $request): JsonResponse
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $moduleNumber = $request->query('module_number');
        $questions = TestQuestion::preTest()
            ->when($moduleNumber !== null && $moduleNumber !== '', fn ($q) => $q->where('module_target', (int) $moduleNumber))
            ->orderBy('module_target')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar soal Pre-Test berhasil dimuat.',
            'data' => [
                'questions' => $questions,
            ],
        ]);
    }

    public function storePretest(Request $request): JsonResponse
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $options = $this->normalizePayloadOptions($request);
        $data = array_merge($request->all(), ['options' => $options]);

        $validator = Validator::make($data, [
            'module_target' => ['required', 'integer', 'exists:educational_modules,module_number'],
            'question_text' => ['required', 'string', 'max:2000'],
            'options' => ['required', 'array', 'size:4'],
            'options.*.label' => ['required', Rule::in(['A', 'B', 'C', 'D'])],
            'options.*.text' => ['required', 'string', 'max:1000'],
            'correct_answer' => ['required', Rule::in(['A', 'B', 'C', 'D', 'a', 'b', 'c', 'd'])],
            'explanation' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi soal Pre-Test gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $question = TestQuestion::create([
            'question_type' => TestQuestion::TYPE_PRETEST,
            'module_target' => (int) $validated['module_target'],
            'question_text' => $validated['question_text'],
            'options' => $validated['options'],
            'correct_answer' => strtoupper($validated['correct_answer']),
            'explanation' => $validated['explanation'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Butir soal Pre-Test berhasil ditambahkan.',
            'data' => ['question' => $question],
        ], 201);
    }

    public function updatePretest(Request $request, int $id): JsonResponse
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $question = TestQuestion::preTest()->find($id);
        if (! $question) {
            return response()->json(['success' => false, 'message' => 'Soal Pre-Test tidak ditemukan.'], 404);
        }

        $options = $this->normalizePayloadOptions($request) ?? $question->options;
        $data = array_merge($request->all(), ['options' => $options]);

        $validator = Validator::make($data, [
            'module_target' => ['required', 'integer', 'exists:educational_modules,module_number'],
            'question_text' => ['required', 'string', 'max:2000'],
            'options' => ['required', 'array', 'size:4'],
            'options.*.label' => ['required', Rule::in(['A', 'B', 'C', 'D'])],
            'options.*.text' => ['required', 'string', 'max:1000'],
            'correct_answer' => ['required', Rule::in(['A', 'B', 'C', 'D', 'a', 'b', 'c', 'd'])],
            'explanation' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi soal Pre-Test gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $question->update([
            'module_target' => (int) $validated['module_target'],
            'question_text' => $validated['question_text'],
            'options' => $validated['options'],
            'correct_answer' => strtoupper($validated['correct_answer']),
            'explanation' => $validated['explanation'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Butir soal Pre-Test berhasil diperbarui.',
            'data' => ['question' => $question->fresh()],
        ]);
    }

    public function destroyPretest(Request $request, int $id): JsonResponse
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $question = TestQuestion::preTest()->find($id);
        if (! $question) {
            return response()->json(['success' => false, 'message' => 'Soal Pre-Test tidak ditemukan.'], 404);
        }

        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'Butir soal Pre-Test berhasil dihapus.',
        ]);
    }

    public function indexPosttest(Request $request): JsonResponse
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $questions = TestQuestion::postTest()->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar soal Post-Test berhasil dimuat.',
            'data' => [
                'questions' => $questions,
            ],
        ]);
    }

    public function storePosttest(Request $request): JsonResponse
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $options = $this->normalizePayloadOptions($request);
        $data = array_merge($request->all(), ['options' => $options]);

        $validator = Validator::make($data, [
            'question_text' => ['required', 'string', 'max:2000'],
            'options' => ['required', 'array', 'size:4'],
            'options.*.label' => ['required', Rule::in(['A', 'B', 'C', 'D'])],
            'options.*.text' => ['required', 'string', 'max:1000'],
            'correct_answer' => ['required', Rule::in(['A', 'B', 'C', 'D', 'a', 'b', 'c', 'd'])],
            'explanation' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi soal Post-Test gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $question = TestQuestion::create([
            'question_type' => TestQuestion::TYPE_POSTTEST,
            'module_target' => null,
            'question_text' => $validated['question_text'],
            'options' => $validated['options'],
            'correct_answer' => strtoupper($validated['correct_answer']),
            'explanation' => $validated['explanation'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Butir soal Post-Test berhasil ditambahkan.',
            'data' => ['question' => $question],
        ], 201);
    }

    public function updatePosttest(Request $request, int $id): JsonResponse
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $question = TestQuestion::postTest()->find($id);
        if (! $question) {
            return response()->json(['success' => false, 'message' => 'Soal Post-Test tidak ditemukan.'], 404);
        }

        $options = $this->normalizePayloadOptions($request) ?? $question->options;
        $data = array_merge($request->all(), ['options' => $options]);

        $validator = Validator::make($data, [
            'question_text' => ['required', 'string', 'max:2000'],
            'options' => ['required', 'array', 'size:4'],
            'options.*.label' => ['required', Rule::in(['A', 'B', 'C', 'D'])],
            'options.*.text' => ['required', 'string', 'max:1000'],
            'correct_answer' => ['required', Rule::in(['A', 'B', 'C', 'D', 'a', 'b', 'c', 'd'])],
            'explanation' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi soal Post-Test gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $question->update([
            'module_target' => null,
            'question_text' => $validated['question_text'],
            'options' => $validated['options'],
            'correct_answer' => strtoupper($validated['correct_answer']),
            'explanation' => $validated['explanation'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Butir soal Post-Test berhasil diperbarui.',
            'data' => ['question' => $question->fresh()],
        ]);
    }

    public function destroyPosttest(Request $request, int $id): JsonResponse
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $question = TestQuestion::postTest()->find($id);
        if (! $question) {
            return response()->json(['success' => false, 'message' => 'Soal Post-Test tidak ditemukan.'], 404);
        }

        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'Butir soal Post-Test berhasil dihapus.',
        ]);
    }
}
