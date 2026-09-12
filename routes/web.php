<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminModuleController;
use App\Http\Controllers\AdminPosterController;
use App\Http\Controllers\AdminSheetController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaseStudyController;
use App\Http\Controllers\DosenDashboardController;
use App\Http\Controllers\EducationalModuleController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\GamificationController;
use App\Http\Controllers\MahasiswaDashboardController;
use App\Http\Controllers\PosterController;
use App\Http\Controllers\SelfCareController;
use Illuminate\Support\Facades\Route;

// 1. Akses Awal
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/force-logout', [AuthController::class, 'forceLogout'])->name('force.logout');

Route::middleware('auth')->group(function () {
    // Pengalihan umum otomatis sesuai role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isMahasiswa()) {
            if (! $user->is_biodata_filled) {
                return redirect()->route('biodata.show');
            }
            if (! $user->pretest_completed) {
                return redirect()->route('pretest.show');
            }

            return redirect()->route('mahasiswa.dashboard');
        } elseif ($user->isDosen()) {
            return redirect()->route('dosen.dashboard');
        } else {
            return redirect()->route('admin.dashboard');
        }
    })->name('dashboard');

    // Biodata Mahasiswa
    Route::get('/biodata', [AuthController::class, 'showBiodata'])->name('biodata.show');
    Route::post('/biodata', [AuthController::class, 'saveBiodata'])->name('biodata.save');

    // Research Gating (Pre-Test & Post-Test)
    Route::get('/pretest', [EvaluationController::class, 'showPretest'])->name('pretest.show');
    Route::post('/pretest', [EvaluationController::class, 'submitPretest'])->name('pretest.submit');
    Route::get('/posttest', [EvaluationController::class, 'showPosttest'])->name('posttest.show');
    Route::post('/posttest', [EvaluationController::class, 'submitPosttest'])->name('posttest.submit');

    // Mahasiswa Menu Utama (Fitur Edukasi & Pemantauan)
    Route::get('/mahasiswa/dashboard', [MahasiswaDashboardController::class, 'index'])->name('mahasiswa.dashboard');

    // I1: Modul Edukasi & Video YouTube (Per-Module Pre-Test & Post-Test)
    Route::get('/modules', [EducationalModuleController::class, 'index'])->name('modules.index');
    Route::get('/modules/{id}', [EducationalModuleController::class, 'show'])->name('modules.show');
    Route::get('/modules/{id}/pretest', [EvaluationController::class, 'showModulePretest'])->name('modules.pretest.show');
    Route::post('/modules/{id}/pretest', [EvaluationController::class, 'submitModulePretest'])->name('modules.pretest.submit');
    Route::get('/modules/{id}/posttest', [EvaluationController::class, 'showModulePosttest'])->name('modules.posttest.show');
    Route::post('/modules/{id}/posttest', [EvaluationController::class, 'submitModulePosttest'])->name('modules.posttest.submit');
    Route::get('/modules/{moduleId}/topic/{topicId}', [EducationalModuleController::class, 'showTopic'])->name('modules.topic');

    // I2: Projek Visual Poster (Kaspro / KesproFeed)
    Route::get('/posters', [PosterController::class, 'index'])->name('posters.index');
    Route::post('/posters/{id}/download', [PosterController::class, 'download'])->name('posters.download');
    Route::post('/posters/{id}/share', [PosterController::class, 'share'])->name('posters.share');

    // I3: Kalender Haid, Macam Darah & IMT (Self-Care Tools)
    Route::get('/selfcare', [SelfCareController::class, 'index'])->name('selfcare.index');
    Route::post('/selfcare/period', [SelfCareController::class, 'storePeriod'])->name('selfcare.period.store');
    Route::post('/selfcare/bmi', [SelfCareController::class, 'storeBmi'])->name('selfcare.bmi.store');
    Route::get('/selfcare/blood-guide', [SelfCareController::class, 'bloodGuide'])->name('selfcare.blood_guide');
    Route::get('/selfcare/hygiene-guide', [SelfCareController::class, 'hygieneGuide'])->name('selfcare.hygiene_guide');

    // I4: Diskusi Anonim & Studi Kasus Mahasiswa
    Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
    Route::get('/forum/create', [ForumController::class, 'create'])->name('forum.create');
    Route::post('/forum', [ForumController::class, 'store'])->name('forum.store');
    Route::get('/forum/{id}', [ForumController::class, 'show'])->name('forum.show');
    Route::post('/forum/{id}/reply', [ForumController::class, 'reply'])->name('forum.reply');

    Route::get('/cases', [CaseStudyController::class, 'index'])->name('cases.index');
    Route::get('/cases/{id}', [CaseStudyController::class, 'show'])->name('cases.show');

    // Gamifikasi Harian & Retensi
    Route::get('/trivia', [GamificationController::class, 'trivia'])->name('gamification.trivia');
    Route::post('/trivia', [GamificationController::class, 'submitTrivia'])->name('gamification.trivia.submit');
    Route::get('/myth-fact', [GamificationController::class, 'mythFact'])->name('gamification.myth_fact');
    Route::post('/myth-fact', [GamificationController::class, 'submitMythFact'])->name('gamification.myth_fact.submit');
    Route::get('/diary', [GamificationController::class, 'diary'])->name('gamification.diary');
    Route::post('/diary', [GamificationController::class, 'storeDiary'])->name('gamification.diary.store');

    // 2. Alur Dosen PA
    Route::get('/dosen/dashboard', [DosenDashboardController::class, 'index'])->name('dosen.dashboard');

    // 3. Alur Super Admin / Pengelola (Terpisah Mandiri)
    // 3.1 Dashboard Grafik & Metrik Utama
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // 3.2 Daftar Pengguna Terdaftar (CRUD)
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    // 3.3 Kelola & Unggah Modul Edukasi
    Route::get('/admin/modules/template', [AdminModuleController::class, 'downloadTemplate'])->name('admin.modules.template');
    Route::post('/admin/modules/import', [AdminModuleController::class, 'importExcel'])->name('admin.modules.import');
    Route::get('/admin/modules', [AdminModuleController::class, 'index'])->name('admin.modules.index');
    Route::post('/admin/modules', [AdminModuleController::class, 'store'])->name('admin.modules.store');
    Route::post('/admin/modules/{id}/topics', [AdminModuleController::class, 'storeTopic'])->name('admin.modules.topics.store');
    Route::delete('/admin/modules/{id}', [AdminModuleController::class, 'destroy'])->name('admin.modules.destroy');
    Route::delete('/admin/topics/{id}', [AdminModuleController::class, 'destroyTopic'])->name('admin.topics.destroy');

    // 3.4 Lembar Data Google Sheets
    Route::get('/admin/sheets', [AdminSheetController::class, 'index'])->name('admin.sheets.index');

    // 3.5 Unggah & Kelola Poster Kaspro
    Route::get('/admin/posters', [AdminPosterController::class, 'index'])->name('admin.posters.index');
    Route::post('/admin/posters', [AdminPosterController::class, 'store'])->name('admin.posters.store');
    Route::delete('/admin/posters/{id}', [AdminPosterController::class, 'destroy'])->name('admin.posters.destroy');
});
