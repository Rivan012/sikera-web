<?php

namespace Database\Seeders;

use App\Models\CaseStudy;
use App\Models\EducationalModule;
use App\Models\EmergencyHotline;
use App\Models\EvaluationResponse;
use App\Models\KesproFeed;
use App\Models\ModuleTopic;
use App\Models\MythFactCard;
use App\Models\TestQuestion;
use App\Models\TriviaQuestion;
use App\Models\User;
use App\Services\GoogleSheetsSyncService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Awal (Admin, Dosen PA, Mahasiswa)
        $admin = User::create([
            'name' => 'Tim Peneliti Super Admin',
            'initials' => 'TP-Admin',
            'email' => 'admin@sikera.id',
            'nim' => 'ADMIN01',
            'role' => 'admin',
            'usia' => 32,
            'agama' => 'Islam',
            'pendidikan_terakhir' => 'S2 / Magister Kesehatan',
            'fakultas' => 'Universitas Bengkulu',
            'prodi' => 'Magister Kesehatan / Tim Peneliti',
            'gender' => 'P',
            'is_biodata_filled' => true,
            'pretest_completed' => true,
            'posttest_completed' => true,
            'password' => Hash::make('password'),
        ]);

        $dosen = User::create([
            'name' => 'Dr. Rina Novita, M.Kes (Dosen PA)',
            'initials' => 'RN-Dosen',
            'email' => 'dosen@unib.ac.id',
            'nim' => '198503122010122001',
            'role' => 'dosen_pa',
            'usia' => 39,
            'agama' => 'Islam',
            'pendidikan_terakhir' => 'S3 / Doktor Kesehatan',
            'fakultas' => 'Fakultas Keguruan dan Ilmu Pendidikan (FKIP)',
            'prodi' => 'Pendidikan Biologi',
            'gender' => 'P',
            'is_biodata_filled' => true,
            'pretest_completed' => true,
            'posttest_completed' => true,
            'password' => Hash::make('password'),
        ]);

        $mhs1 = User::create([
            'name' => 'Tester 1',
            'initials' => 'APM',
            'email' => 'mhs@unib.ac.id',
            'nim' => 'A1D026045',
            'role' => 'mahasiswa',
            'usia' => 18,
            'agama' => 'Islam',
            'pendidikan_terakhir' => 'SMA/SMK Sederajat',
            'fakultas' => 'Fakultas Keguruan dan Ilmu Pendidikan (FKIP)',
            'prodi' => 'Pendidikan Bahasa Inggris',
            'gender' => 'P',
            'is_biodata_filled' => true,
            'pretest_completed' => false,
            'posttest_completed' => false,
            'points' => 50,
            'streak_days' => 3,
            'password' => Hash::make('password'),
        ]);

        // Responden Sampel UNIB untuk Riset Simulasi N-Gain & Google Sheets Sync
        $fakultasSampel = [
            ['nama' => 'Fakultas Keguruan dan Ilmu Pendidikan (FKIP)', 'prodi' => 'Pendidikan Bahasa Indonesia', 'gender' => 'P', 'usia' => 18, 'agama' => 'Islam', 'pre' => 45, 'post' => 85],
            ['nama' => 'Fakultas Hukum (FH)', 'prodi' => 'Ilmu Hukum', 'gender' => 'L', 'usia' => 19, 'agama' => 'Kristen Protestan', 'pre' => 40, 'post' => 80],
            ['nama' => 'Fakultas Ekonomi dan Bisnis (FEB)', 'prodi' => 'Manajemen', 'gender' => 'P', 'usia' => 18, 'agama' => 'Islam', 'pre' => 50, 'post' => 90],
            ['nama' => 'Fakultas Ilmu Sosial dan Ilmu Politik (FISIP)', 'prodi' => 'Ilmu Komunikasi', 'gender' => 'P', 'usia' => 19, 'agama' => 'Katolik', 'pre' => 55, 'post' => 95],
            ['nama' => 'Fakultas Pertanian (FP)', 'prodi' => 'Agribisnis', 'gender' => 'L', 'usia' => 18, 'agama' => 'Islam', 'pre' => 35, 'post' => 75],
            ['nama' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam (FMIPA)', 'prodi' => 'Farmasi', 'gender' => 'P', 'usia' => 18, 'agama' => 'Hindu', 'pre' => 60, 'post' => 95],
            ['nama' => 'Fakultas Teknik (FT)', 'prodi' => 'Informatika', 'gender' => 'L', 'usia' => 19, 'agama' => 'Islam', 'pre' => 40, 'post' => 85],
        ];

        foreach ($fakultasSampel as $index => $data) {
            $userMhs = User::create([
                'name' => 'Responden Sample #'.($index + 1),
                'initials' => 'RS#'.($index + 1),
                'email' => 'sample'.($index + 1).'@unib.ac.id',
                'nim' => 'G1A0260'.str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                'role' => 'mahasiswa',
                'usia' => $data['usia'],
                'agama' => $data['agama'],
                'pendidikan_terakhir' => 'SMA/SMK Sederajat',
                'fakultas' => $data['nama'],
                'prodi' => $data['prodi'],
                'gender' => $data['gender'],
                'is_biodata_filled' => true,
                'pretest_completed' => true,
                'posttest_completed' => true,
                'points' => rand(60, 200),
                'streak_days' => rand(1, 7),
                'password' => Hash::make('password'),
            ]);

            // Hitung N-Gain: (Post - Pre) / (100 - Pre)
            $pre = $data['pre'];
            $post = $data['post'];
            $nGain = ($post - $pre) / (100 - $pre);

            $preEval = EvaluationResponse::create([
                'user_id' => $userMhs->id,
                'type' => 'pre_test',
                'raw_answers' => ['q1' => 'A', 'q2' => 'B', 'q3' => 'A', 'q4' => 'C'],
                'scores_per_module' => ['Modul 1' => $pre, 'Modul 2' => $pre, 'Modul 3' => $pre, 'Modul 4' => $pre],
                'total_score' => $pre,
                'submitted_at' => now()->subDays(5),
            ]);

            $postEval = EvaluationResponse::create([
                'user_id' => $userMhs->id,
                'type' => 'post_test',
                'raw_answers' => ['q1' => 'B', 'q2' => 'B', 'q3' => 'C', 'q4' => 'D'],
                'scores_per_module' => ['Modul 1' => $post, 'Modul 2' => $post, 'Modul 3' => $post, 'Modul 4' => $post],
                'total_score' => $post,
                'n_gain_score' => round($nGain, 3),
                'submitted_at' => now()->subDay(),
            ]);

            // Log Google Sheets Sync
            GoogleSheetsSyncService::syncPretestSubmission($userMhs, $preEval);
            GoogleSheetsSyncService::syncPosttestSubmission($userMhs, $postEval, round($nGain, 3));
        }

        // 2. Modul Pembelajaran (4 Modul SIKERA dengan Banner Visual)
        $modul1 = EducationalModule::create([
            'module_number' => 1,
            'title' => 'Anatomi, Fisiologi, & Higienitas Reproduksi',
            'subtitle' => 'Pemahaman dasar struktur biologis, proses pubertas, siklus menstruasi, dan kebersihan diri.',
            'description' => 'Membahas sistem reproduksi pria dan wanita secara ilmiah tanpa vulgaritas, variasi warna darah haid yang normal vs abnormal, serta penanganan mandiri nyeri haid (dismenore).',
            'badge_icon' => 'anatomy',
            'banner_image' => '/images/banners/banner-module-1.svg',
            'estimated_time' => '15 Menit',
        ]);

        ModuleTopic::create([
            'educational_module_id' => $modul1->id,
            'topic_code' => '1.1',
            'title' => 'Pengenalan Anatomi Organ Reproduksi Pria & Wanita',
            'order_index' => 1,
            'youtube_video_id' => 'v3F4QW_h32M',
            'content_html' => '<p class="mb-4">Sistem reproduksi manusia dirancang dengan fungsi biologis yang saling melengkapi. Memahami organ reproduksi internal dan eksternal secara anatomis adalah langkah pertama dalam menjaga kesehatan organ vital secara mandiri.</p><h4 class="font-bold text-slate-800 dark:text-white mt-4 mb-2">1. Organ Reproduksi Wanita</h4><ul class="list-disc pl-5 space-y-2 mb-4"><li><strong>Vulva & Labia:</strong> Bagian terluar yang melindungi saluran uretra dan vagina.</li><li><strong>Vagina:</strong> Saluran berotot elastis yang menghubungkan leher rahim (serviks) dengan bagian luar tubuh.</li><li><strong>Uterus (Rahim):</strong> Organ berongga tempat perkembangan janin, yang dinding dalamnya (endometrium) meluruh secara berkala saat menstruasi.</li><li><strong>Tuba Fallopi & Ovarium:</strong> Pabrik sel telur (ovum) serta hormon estrogen dan progesteron.</li></ul><h4 class="font-bold text-slate-800 dark:text-white mt-4 mb-2">2. Organ Reproduksi Pria</h4><ul class="list-disc pl-5 space-y-2"><li><strong>Testis:</strong> Penghasil sperma dan hormon testosteron yang berada di dalam kantung skrotum.</li><li><strong>Epididimis & Vas Deferens:</strong> Saluran pematangan dan pengangkutan sperma.</li><li><strong>Kelenjar Prostat & Vesikula Seminalis:</strong> Penghasil cairan semen yang melindungi sel sperma.</li></ul>',
        ]);

        ModuleTopic::create([
            'educational_module_id' => $modul1->id,
            'topic_code' => '1.2',
            'title' => 'Pubertas & Mekanisme Hormonal (Mimpi Basah & Siklus Haid)',
            'order_index' => 2,
            'youtube_video_id' => 'e7zP-b-YnS0',
            'content_html' => '<p class="mb-4">Pubertas ditandai oleh kematangan aksis Hipotalamus-Hipofisis-Gonad yang memicu pelepasan hormon gonadotropin (GnRH, LH, FSH).</p><p class="mb-4">Pada pria, produksi sperma yang telah aktif akan dikeluarkan secara alami melalui proses emisi nokturnal (mimpi basah). Pada wanita, fluktuasi estrogen dan progesteron mengatur siklus menstruasi rata-rata 21-35 hari, dengan fase folikuler, ovulasi (pelepasan sel telur), dan fase luteal.</p>',
        ]);

        ModuleTopic::create([
            'educational_module_id' => $modul1->id,
            'topic_code' => '1.3',
            'title' => 'Variasi Karakteristik Darah Menstruasi',
            'order_index' => 3,
            'content_html' => '<p class="mb-4">Warna dan tekstur darah haid merupakan indikator penting keseimbangan hormonal serta sirkulasi darah di endometrium:</p><ul class="list-disc pl-5 space-y-2 mb-4"><li><span class="text-rose-600 font-semibold">Merah Terang:</span> Darah segar dengan aliran lancar, biasanya terjadi di hari ke 1-3 haid. Kondisi sangat normal.</li><li><span class="text-amber-800 font-semibold">Cokelat Gelap / Kehitaman:</span> Darah sisa di akhir siklus yang mengalami proses oksidasi saat keluar perlahan. Fisiologis/normal.</li><li><span class="text-pink-500 font-semibold">Merah Muda Pucat:</span> Dapat terjadi akibat kadar estrogen yang relatif rendah atau awal bercak flek (spotting).</li><li><span class="text-red-700 font-semibold">Gumpalan Tebal & Berbau Busuk:</span> Jika gumpalan lebih besar dari koin koin atau disertai demam dan aroma menyengat, segera lakukan pemeriksaan medis ke fasilitas kesehatan.</li></ul>',
        ]);

        ModuleTopic::create([
            'educational_module_id' => $modul1->id,
            'topic_code' => '1.4',
            'title' => 'Higienitas Genitalia & Penanganan Dismenore (Skala NRS)',
            'order_index' => 4,
            'content_html' => '<p class="mb-4">Perawatan higienitas genitalia yang benar:</p><ol class="list-decimal pl-5 space-y-2 mb-4"><li>Membasuh organ kewanitaan selalu dari arah <strong>depan ke belakang</strong> (dari vagina menuju anus), bukan sebaliknya, untuk mencegah perpindahan bakteri E. Coli.</li><li>Mengganti pembalut minimal setiap 3-4 jam sekali saat aliran haid sedang banyak.</li><li>Menggunakan pakaian dalam berbahan katun berpori yang menyerap keringat dan tidak ketat.</li><li>Hindari penggunaan sabun antiseptik pewangi atau douching secara rutin karena dapat merusak flora normal (Lactobacillus) dan mengganggu pH alami vagina.</li></ol><h4 class="font-bold text-slate-800 dark:text-white mt-4 mb-2">Penanganan Nyeri Haid (Dismenore):</h4><p class="mb-2">Nyeri haid dipicu oleh produksi prostaglandin yang memicu kontraksi otot rahim. Penanganan mandiri mencakup:</p><ul class="list-disc pl-5 space-y-2"><li>Kompres hangat pada perut bagian bawah selama 15-20 menit.</li><li>Latihan relaksasi pernapasan dalam dan minum air hangat yang cukup.</li><li>Jika skala nyeri NRS mencapai 7-10 dan mengganggu aktivitas perkuliahan harian, konsultasikan dengan dokter untuk evaluasi kemungkinan endometriosis atau kista.</li></ul>',
        ]);

        $modul2 = EducationalModule::create([
            'module_number' => 2,
            'title' => 'Batasan Pergaulan, Pacaran Sehat, & Dinamika Kampus',
            'subtitle' => 'Membangun relasi yang saling menghargai, komunikasi asertif, dan pencegahan kekerasan relasional.',
            'description' => 'Membekali mahasiswa baru dengan pemahaman batasan fisik/emosional (*boundaries*), analisis risiko kos-kosan (*living together*), serta keterampilan berkata TIDAK secara tegas.',
            'badge_icon' => 'boundaries',
            'banner_image' => '/images/banners/banner-module-2.svg',
            'estimated_time' => '12 Menit',
        ]);

        ModuleTopic::create([
            'educational_module_id' => $modul2->id,
            'topic_code' => '2.1',
            'title' => 'Batasan Fisik & Emosional (Healthy Dating vs Relasi Toksik)',
            'order_index' => 1,
            'content_html' => '<p class="mb-4">Relasi yang sehat berlandaskan pada <em>Mutual Respect</em>, <em>Trust</em>, dan <em>Consent</em> (persetujuan sadar tanpa paksaan). Kenali tanda bahaya (red flags) dalam relasi seperti manipulasi emosional (gaslighting), posesif berlebihan, ancaman penyebaran foto pribadi, dan isolasi dari lingkungan pertemanan.</p>',
        ]);

        ModuleTopic::create([
            'educational_module_id' => $modul2->id,
            'topic_code' => '2.2',
            'title' => 'Bedah Kasus Lingkungan Mahasiswa (Living Together & Kos-kosan)',
            'order_index' => 2,
            'content_html' => '<p class="mb-4">Kondisi indekos yang minim pengawasan sosial seringkali menjadi lingkungan rentan terjadinya kohabitasi tanpa ikatan hukum resmi (living together). Dampak yang kerap terjadi mencakup ketergantungan finansial, kehamilan tidak diinginkan (KTD), putus kuliah, serta kerentanan hukum bila terjadi perselisihan.</p>',
        ]);

        ModuleTopic::create([
            'educational_module_id' => $modul2->id,
            'topic_code' => '2.3',
            'title' => 'Keterampilan Asertif Remaja (Teknik Berkata "TIDAK")',
            'order_index' => 3,
            'content_html' => '<p class="mb-4">Keterampilan asertif adalah kemampuan mengekspresikan batasan pribadi secara jujur, lugas, dan tenang tanpa bersikap agresif maupun pasif. Gunakan teknik <strong>CLEAR</strong>: Cermati ajakan, Langsung tolak intinya ("Aku tidak nyaman melakukan ini"), Evaluasi alasan bila perlu, Alihkan topik/aktivitas positif, dan Rencanakan keluar dari situasi mendesak.</p>',
        ]);

        $modul3 = EducationalModule::create([
            'module_number' => 3,
            'title' => 'Risiko Medis Seks Bebas & Perlindungan Diri',
            'subtitle' => 'Edukasi pencegahan IMS, HIV/AIDS, bahaya aborsi ilegal, dan perlindungan keamanan digital.',
            'description' => 'Tinjauan ilmiah mengenai transmisi patogen seksual, konsekuensi medis aborsi tanpa pengawasan tenaga kesehatan, serta mitigasi Kekerasan Berbasis Gender Online (KBGO).',
            'badge_icon' => 'medical_risk',
            'banner_image' => '/images/banners/banner-module-3.svg',
            'estimated_time' => '15 Menit',
        ]);

        ModuleTopic::create([
            'educational_module_id' => $modul3->id,
            'topic_code' => '3.1',
            'title' => 'Infeksi Menular Seksual (IMS) & HIV/AIDS',
            'order_index' => 1,
            'content_html' => '<p class="mb-4">Infeksi Menular Seksual (IMS) seperti Gonore, Sifilis (Raja Singa), Herpes Genitalis, HPV, dan HIV ditularkan melalui kontak cairan tubuh dan hubungan seksual tanpa proteksi. Banyak IMS bersifat asimtomatik (tanpa gejala di fase awal) namun berdampak jangka panjang berupa infertilitas, radang panggul kronis, dan risiko kanker serviks.</p>',
        ]);

        ModuleTopic::create([
            'educational_module_id' => $modul3->id,
            'topic_code' => '3.2',
            'title' => 'Kehamilan Tidak Diinginkan (KTD) & Bahaya Aborsi Tidak Aman',
            'order_index' => 2,
            'content_html' => '<p class="mb-4">Aborsi tidak aman (menggunakan obat ilegal tanpa resep medis, jamu keras, atau manipulasi fisik tajam) menyebabkan komplikasi fatal seperti perdarahan masif, perforasi dinding rahim, sepsis infeksi berat, hingga kematian maternal. SIKERA menyediakan rujukan konseling aman bagi mahasiswa yang menghadapi krisis reproduksi.</p>',
        ]);

        ModuleTopic::create([
            'educational_module_id' => $modul3->id,
            'topic_code' => '3.3',
            'title' => 'Keamanan Digital Remaja (Cybersexuality, Sexting, & KBGO)',
            'order_index' => 3,
            'content_html' => '<p class="mb-4">Kekerasan Berbasis Gender Online (KBGO) mencakup <em>Non-Consensual Intimate Image Sharing</em> (NCII), doxxing, dan sextortion (pemerasan menggunakan foto pribadi). Jangan pernah mengirim konten visual intim melalui media digital, dan segera hubungi Satgas PPKS kampus jika mengalami ancaman digital.</p>',
        ]);

        $modul4 = EducationalModule::create([
            'module_number' => 4,
            'title' => 'Mitigasi Pernikahan Dini & Kesiapan Berkeluarga',
            'subtitle' => 'Empat pilar kesiapan menikah BKKBN, risiko kehamilan remaja, dan pencegahan stunting.',
            'description' => 'Mempersiapkan masa depan generasi muda melalui pematangan usia perkawinan (21 tahun wanita, 25 tahun pria), mitigasi risiko panggul sempit (CPD), serta pemenuhan gizi remaja.',
            'badge_icon' => 'family_planning',
            'banner_image' => '/images/banners/banner-module-4.svg',
            'estimated_time' => '10 Menit',
        ]);

        ModuleTopic::create([
            'educational_module_id' => $modul4->id,
            'topic_code' => '4.1',
            'title' => 'Empat Pilar Kesiapan Menikah (BKKBN: Usia 21 Wanita & 25 Pria)',
            'order_index' => 1,
            'content_html' => '<p class="mb-4">BKKBN menetapkan batas usia ideal menikah yaitu minimal 21 tahun untuk perempuan dan 25 tahun untuk laki-laki. Empat pilar kesiapan keluarga meliputi: Kesiapan Fisik/Biologis, Kesiapan Mental/Psikologis, Kesiapan Finansial/Ekonomi, dan Kesiapan Sosial/Keluarga.</p>',
        ]);

        ModuleTopic::create([
            'educational_module_id' => $modul4->id,
            'topic_code' => '4.2',
            'title' => 'Dampak Medis Kehamilan Usia Dini (CPD, Preeklamsia, Kematian Maternal)',
            'order_index' => 2,
            'content_html' => '<p class="mb-4">Kehamilan pada usia di bawah 20 tahun memiliki risiko medis tinggi karena tulang panggul belum berkembang sempurna, memicu <em>Cephalopelvic Disproportion</em> (CPD atau disproporsi kepala panggul) yang menyebabkan persalinan macet. Risiko lain meliputi preeklamsia/eklamsia, anemia berat, dan risiko kelahiran prematur dengan berat badan lahir rendah (BBLR).</p>',
        ]);

        ModuleTopic::create([
            'educational_module_id' => $modul4->id,
            'topic_code' => '4.3',
            'title' => 'Pencegahan Stunting sejak Usia Remaja (Gizi & Anemia)',
            'order_index' => 3,
            'content_html' => '<p class="mb-4">Pencegahan stunting dimulai jauh sebelum pernikahan. Remaja putri yang menderita anemia dan Kekurangan Energi Kronis (KEK) memiliki risiko melahirkan anak stunting di kemudian hari. Konsumsi makanan bergizi seimbang tinggi protein hewani, zat besi, dan tablet tambah darah (TTD) teratur adalah investasi generasi sehat masa depan.</p>',
        ]);

        // 3. Bank Soal Evaluasi Pre-Test & Post-Test (Untuk instrumen riset N-Gain)
        $questions = [
            [
                'module_target' => 1,
                'question_text' => 'Arah yang benar saat membersihkan area organ kewanitaan setelah buang air adalah...',
                'options' => [
                    ['label' => 'A', 'text' => 'Dari belakang (anus) ke arah depan (vagina)'],
                    ['label' => 'B', 'text' => 'Dari depan (vagina) ke arah belakang (anus)'],
                    ['label' => 'C', 'text' => 'Bebas dari arah mana saja yang penting menggunakan sabun wangi'],
                    ['label' => 'D', 'text' => 'Cukup disiram tanpa perlu dibersihkan dengan tangan'],
                ],
                'correct_answer' => 'B',
                'explanation' => 'Membasuh dari depan ke belakang mencegah perpindahan bakteri usus (seperti E. Coli) dari anus ke saluran kemih dan vagina.',
            ],
            [
                'module_target' => 1,
                'question_text' => 'Darah menstruasi berwarna cokelat gelap atau kehitaman pada hari-hari terakhir siklus menunjukkan...',
                'options' => [
                    ['label' => 'A', 'text' => 'Tanda infeksi menular seksual berbahaya'],
                    ['label' => 'B', 'text' => 'Darah yang mengalami oksidasi normal saat keluar perlahan'],
                    ['label' => 'C', 'text' => 'Kerusakan permanen pada dinding rahim'],
                    ['label' => 'D', 'text' => 'Kekurangan vitamin dan mineral akut'],
                ],
                'correct_answer' => 'B',
                'explanation' => 'Warna cokelat atau kehitaman adalah darah sisa yang telah teroksidasi oleh oksigen karena membutuhkan waktu lebih lama untuk keluar dari rahim, kondisi ini fisiologis/normal.',
            ],
            [
                'module_target' => 2,
                'question_text' => 'Tindakan memanipulasi pasangan hingga membuat pasangan meragukan ingatan, kewarasan, atau perasaannya sendiri disebut...',
                'options' => [
                    ['label' => 'A', 'text' => 'Love Language'],
                    ['label' => 'B', 'text' => 'Assertive Communication'],
                    ['label' => 'C', 'text' => 'Gaslighting (Manipulasi Emosional)'],
                    ['label' => 'D', 'text' => 'Time Management'],
                ],
                'correct_answer' => 'C',
                'explanation' => 'Gaslighting adalah bentuk pelecehan emosional di mana pelaku membuat korban mempertanyakan realitas atau perasaan mereka sendiri.',
            ],
            [
                'module_target' => 2,
                'question_text' => 'Pernyataan yang tepat mengenai konsep "Consent" (persetujuan) dalam relasi adalah...',
                'options' => [
                    ['label' => 'A', 'text' => 'Persetujuan sekali berlaku selamanya untuk semua aktivitas'],
                    ['label' => 'B', 'text' => 'Diam berarti setuju terhadap ajakan pasangan'],
                    ['label' => 'C', 'text' => 'Persetujuan harus diberikan secara sadar, bebas tanpa paksaan atau manipulasi, dan dapat ditarik kapan saja'],
                    ['label' => 'D', 'text' => 'Persetujuan hanya diperlukan bagi pasangan yang belum pacaran'],
                ],
                'correct_answer' => 'C',
                'explanation' => 'Consent sejati bersifat sadar, sukarela, tanpa paksaan (informed and freely given), serta dapat dibatalkan sewaktu-waktu.',
            ],
            [
                'module_target' => 3,
                'question_text' => 'Berikut ini yang merupakan komplikasi medis fatal akibat praktik aborsi tidak aman (unsafe abortion) adalah...',
                'options' => [
                    ['label' => 'A', 'text' => 'Sepsis (infeksi berat sistemik) dan perforasi dinding rahim'],
                    ['label' => 'B', 'text' => 'Peningkatan daya tahan tubuh terhadap bakteri'],
                    ['label' => 'C', 'text' => 'Regenerasi sel sel telur lebih cepat'],
                    ['label' => 'D', 'text' => 'Penurunan risiko anemia di masa depan'],
                ],
                'correct_answer' => 'A',
                'explanation' => 'Aborsi ilegal tanpa prosedur medis steril sering menyebabkan robekan rahim (perforasi), perdarahan masif, infeksi darah mematikan (sepsis), dan infertilitas.',
            ],
            [
                'module_target' => 3,
                'question_text' => 'Infeksi Menular Seksual (IMS) yang disebabkan oleh virus dan belum memiliki obat pembasmi tuntas namun dapat ditekan dengan terapi ARV adalah...',
                'options' => [
                    ['label' => 'A', 'text' => 'Sifilis'],
                    ['label' => 'B', 'text' => 'Gonore'],
                    ['label' => 'C', 'text' => 'HIV / AIDS'],
                    ['label' => 'D', 'text' => 'Klamidia'],
                ],
                'correct_answer' => 'C',
                'explanation' => 'HIV adalah virus yang menyerang sistem imun (sel CD4). Terapi ARV (Antiretroviral) berfungsi menekan replikasi virus hingga tak terdeteksi (undetectable).',
            ],
            [
                'module_target' => 4,
                'question_text' => 'Menurut BKKBN, batas usia minimal yang direkomendasikan untuk menikah agar organ reproduksi dan psikososial matang adalah...',
                'options' => [
                    ['label' => 'A', 'text' => '17 tahun wanita & 19 tahun pria'],
                    ['label' => 'B', 'text' => '21 tahun wanita & 25 tahun pria'],
                    ['label' => 'C', 'text' => '18 tahun wanita & 20 tahun pria'],
                    ['label' => 'D', 'text' => '15 tahun wanita & 18 tahun pria'],
                ],
                'correct_answer' => 'B',
                'explanation' => 'BKKBN menganjurkan usia minimal 21 tahun untuk perempuan dan 25 tahun untuk laki-laki guna memastikan kesiapan biologis, kognitif, dan kemandirian finansial.',
            ],
            [
                'module_target' => 4,
                'question_text' => 'Kondisi ketidaksesuaian ukuran antara kepala bayi dengan ukuran rongga panggul ibu pada kehamilan usia terlalu muda dinamakan...',
                'options' => [
                    ['label' => 'A', 'text' => 'Cephalopelvic Disproportion (CPD)'],
                    ['label' => 'B', 'text' => 'Pre-eclampsia gravidarum'],
                    ['label' => 'C', 'text' => 'Hyperemesis'],
                    ['label' => 'D', 'text' => 'Dismenore sekunder'],
                ],
                'correct_answer' => 'A',
                'explanation' => 'CPD terjadi karena panggul perempuan usia remaja belum mencapai ukuran maksimal pertumbuhan tulang, sehingga berisiko tinggi menyebabkan persalinan macet.',
            ],
        ];

        foreach ($questions as $q) {
            TestQuestion::create($q);
        }

        // 4. Case Studies Kampus
        CaseStudy::create([
            'title' => 'Dilema Relasi Kos-Kosan & Tekanan Tinggal Bersama',
            'category' => 'Living Together',
            'narrative' => 'Rina (19 tahun) dan pacarnya Bayu (20 tahun) berkuliah di Bengkulu. Karena alasan ingin menghemat biaya sewa kos dan merasa saling menyayangi, Bayu mengajak Rina tinggal bersama di kosan bebas tanpa ikatan pernikahan. Awalnya terasa menyenangkan, namun setelah 6 bulan, Bayu mulai membebankan seluruh pekerjaan rumah dan uang bulanan kepada Rina, serta melarang Rina bergaul dengan teman sekelasnya.',
            'legal_analysis' => 'Tinggal bersama tanpa ikatan perkawinan tidak memiliki perlindungan hukum keperdataan bila terjadi wanprestasi keuangan atau tindak kekerasan fisik/psikis. Korban rentan mengalami kerugian tanpa ada dasar hukum perlindungan keluarga.',
            'medical_analysis' => 'Kohabitasi tanpa perencanaan matang meningkatkan risiko kehamilan tidak diinginkan (KTD) serta penularan infeksi bila tidak ada transparansi kesehatan reproduksi pasangan.',
            'solution_tips' => 'Rina berhak menetapkan batasan tempat tinggal mandiri. Segera pisah tempat tinggal, komunikasikan secara tegas batasan privasi, dan laporkan ke konselor mahasiswa jika terjadi intimidasi.',
        ]);

        CaseStudy::create([
            'title' => 'Ancaman Penyebaran Foto Pribadi (Sextortion & KBGO)',
            'category' => 'Gaslighting & KBGO',
            'narrative' => 'Dina diminta pacarnya mengirimkan foto tanpa busana dengan dalih bukti cinta dan saling percaya. Saat hubungan mereka merenggang dan Dina ingin mengakhiri hubungan, mantan pacarnya mengancam akan menyebarkan foto tersebut ke grup WhatsApp angkatan kampus jika Dina tidak menuruti keinginannya.',
            'legal_analysis' => 'Tindakan mengancam penyebaran konten intim tanpa persetujuan melanggar UU Tindak Pidana Kekerasan Seksual (UU TPKS No. 12 Tahun 2022) Pasal 14 mengenai Kekerasan Seksual Berbasis Elektronik dengan ancaman pidana penjara hingga 4 tahun.',
            'medical_analysis' => 'Korban sextortion mengalami stres akut, kecemasan berlebih (anxiety), depresi berat, hingga trauma psikis yang membutuhkan pendampingan profesional.',
            'solution_tips' => 'Jangan turuti tuntutan pelaku. Kumpulkan tangkapan layar bukti ancaman, jangan hapus pesan, dan segera laporkan ke Satgas PPKS Universitas Bengkulu untuk pendampingan hukum dan psikologis gratis.',
        ]);

        // 5. KesproFeed Posters
        KesproFeed::create([
            'title' => '4 Panduan Utama Higienitas Organ Kewanitaan',
            'category' => 'Higienitas',
            'image_path' => '/images/feed-hygiene.svg',
            'caption' => 'Jaga kebersihan genitalia setiap hari: basuh dari depan ke belakang, ganti pembalut teratur, dan kenakan celana katun yang sejuk!',
            'share_text' => 'Yuk jaga kesehatan reproduksi kita bareng SIKERA UNIB!',
            'download_count' => 45,
            'share_count' => 28,
        ]);

        KesproFeed::create([
            'title' => 'Mengenal Kode Warna Darah Menstruasi',
            'category' => 'Kesehatan Medis',
            'image_path' => '/images/feed-blood.svg',
            'caption' => 'Warna darah haid berubah seiring waktu karena proses oksidasi dan hormon. Ketahui kapan kamu harus waspada!',
            'share_text' => 'Pahami tubuhmu lebih baik dengan panduan warna darah haid SIKERA.',
            'download_count' => 62,
            'share_count' => 39,
        ]);

        KesproFeed::create([
            'title' => 'Tanda-Tanda Relasi Sehat vs Relasi Beracun (Toxic)',
            'category' => 'Relasi Sehat',
            'image_path' => '/images/feed-relationship.svg',
            'caption' => 'Cinta sejati saling mendukung, bukan mengontrol dan memanipulasi emosimu. Kenali perbedaannya!',
            'share_text' => 'Bangun relasi sehat di kampus bersama SIKERA UNIB.',
            'download_count' => 51,
            'share_count' => 33,
        ]);

        // 6. Hotline Darurat Bengkulu
        EmergencyHotline::create([
            'name' => 'Satgas PPKS Universitas Bengkulu',
            'category' => 'PPKS Kampus',
            'phone_number' => '0812-7890-1234',
            'whatsapp_number' => '6281278901234',
            'address' => 'Gedung Rektorat UNIB Lt. 2, Jl. WR Supratman, Kandang Limun, Bengkulu',
            'operating_hours' => 'Senin - Jumat (08.00 - 16.00 WIB) & Hotline Darurat 24 Jam',
            'description' => 'Layanan resmi pelaporan dan penanganan kasus kekerasan seksual di lingkungan civitas akademika UNIB. Identitas pelapor dilindungi kerahasiaannya.',
        ]);

        EmergencyHotline::create([
            'name' => 'Unit Layanan Bimbingan & Konseling Mahasiswa UNIB',
            'category' => 'Konseling Psikologi',
            'phone_number' => '0736-21170',
            'whatsapp_number' => '6285267894321',
            'address' => 'Pusat Layanan Mahasiswa UNIB, Kampus Utama',
            'operating_hours' => 'Senin - Jumat (08.30 - 15.30 WIB)',
            'description' => 'Konseling tatap muka maupun daring bersama psikolog dan konselor profesional untuk masalah akademik, relasi, dan kesehatan mental.',
        ]);

        EmergencyHotline::create([
            'name' => 'IGD RSUD M. Yunus Bengkulu (Rujukan Medis)',
            'category' => 'Faskes & Rumah Sakit',
            'phone_number' => '0736-52004',
            'whatsapp_number' => '628117365200',
            'address' => 'Jl. Bhayangkara, Sidomulyo, Kec. Gading Cempaka, Kota Bengkulu',
            'operating_hours' => '24 Jam Setiap Hari',
            'description' => 'Fasilitas gawat darurat medis terpadu dan layanan visum et repertum rujukan resmi kepolisian dan kampus.',
        ]);

        // 7. Trivia & Myth-Fact Cards
        TriviaQuestion::create([
            'question' => 'Berapa durasi rata-rata siklus menstruasi yang dianggap normal pada perempuan usia reproduktif?',
            'choices' => ['10 - 14 hari', '21 - 35 hari', '40 - 60 hari', '7 - 10 hari'],
            'answer_key' => '21 - 35 hari',
            'scientific_explanation' => 'Siklus dihitung dari hari pertama haid hingga hari pertama haid berikutnya. Rentang 21 hingga 35 hari dengan rata-rata 28 hari adalah standar fisiologis normal.',
        ]);

        TriviaQuestion::create([
            'question' => 'Kapan waktu terjadinya masa subur (ovulasi) dalam siklus haid 28 hari?',
            'choices' => ['Hari ke-1 saat mulai haid', 'Sekitar hari ke-14 sebelum haid berikutnya', 'Hari ke-28 saat haid selesai', 'Setiap hari tanpa jeda'],
            'answer_key' => 'Sekitar hari ke-14 sebelum haid berikutnya',
            'scientific_explanation' => 'Ovulasi umumnya terjadi sekitar 14 hari sebelum hari pertama siklus menstruasi berikutnya.',
        ]);

        MythFactCard::create([
            'statement' => 'Minum air dingin atau es saat menstruasi dapat membekukan darah kotor di dalam rahim.',
            'is_fact' => false,
            'scientific_fact' => 'MITOS. Minuman yang dikonsumsi masuk ke saluran pencernaan (lambung), sedangkan darah menstruasi berada di saluran reproduksi (rahim). Keduanya adalah sistem organ yang terpisah. Suhu air minum tidak mempengaruhi kelancaran peluruhan dinding rahim.',
            'category' => 'Haid & Mitos',
        ]);

        MythFactCard::create([
            'statement' => 'Mencuci vagina dengan sabun antiseptik pewangi setiap hari dapat mematikan bakteri baik dan memicu infeksi jamur.',
            'is_fact' => true,
            'scientific_fact' => 'FAKTA. Vagina memiliki mekanisme pembersihan mandiri dengan bakteri baik (Lactobacillus) yang menjaga pH asam (3.8-4.5). Sabun pewangi dapat merusak keseimbangan flora normal dan memicu keputihan patologis.',
            'category' => 'Higienitas',
        ]);

        MythFactCard::create([
            'statement' => 'Kehamilan dapat terjadi meskipun hubungan seksual dilakukan dengan metode senggama terputus (pull-out).',
            'is_fact' => true,
            'scientific_fact' => 'FAKTA. Cairan pra-ejakulasi (pre-cum) yang keluar sebelum ejakulasi puncak dapat mengandung sel sperma aktif yang mampu membuahi sel telur.',
            'category' => 'Reproduksi Medis',
        ]);
    }
}
