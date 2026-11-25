import { Head, Link, useForm } from '@inertiajs/react';
import { useState, type FormEvent } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { FieldError } from '@/components/ui/field-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import {
    buildAutoInitials,
    DAFTAR_AGAMA,
    DAFTAR_FAKULTAS_PER_KAMPUS,
    DAFTAR_JENJANG_PENDIDIKAN,
} from '@/constants/akademik';
import AuthLayout from '@/layouts/AuthLayout';
import { cn } from '@/utils';

export default function Register() {
    const [showPassword, setShowPassword] = useState(false);
    const [initialsEdited, setInitialsEdited] = useState(false);

    const form = useForm({
        name: '',
        initials: '',
        usia: '18',
        gender: 'P' as 'P' | 'L',
        menarche_age: '',
        agama: 'Islam',
        pendidikan_terakhir: 'SMA/SMK/MA/Sederajat',
        fakultas: '',
        prodi: '',
        nim: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const handleNameChange = (value: string) => {
        form.setData((prev) => ({
            ...prev,
            name: value,
            initials: initialsEdited ? prev.initials : buildAutoInitials(value),
        }));
    };

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        form.post('/register', {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    };

    return (
        <AuthLayout width="xl">
            <Head title="Formulir Pendaftaran Mahasiswa" />

            <div className="grid grid-cols-1 items-start gap-6 lg:grid-cols-12 lg:gap-8">
                <aside aria-label="Panduan pendaftaran responden" className="flex flex-col gap-5 lg:sticky lg:top-6 lg:col-span-4">
                    <Card>
                        <CardContent className="flex flex-col gap-4">
                            <div>
                                <p className="text-xs font-semibold text-teal-700 dark:text-teal-300">Pendaftaran Responden Baru</p>
                                <h1 className="mt-1 text-xl font-bold tracking-tight text-slate-900 sm:text-2xl dark:text-white">
                                    Formulir Biodata Mahasiswa
                                </h1>
                                <p className="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                                    Lengkapi data demografi dan afiliasi studi untuk mengakses modul flipbook PDF serta evaluasi bertahap di SIKERA.
                                </p>
                            </div>

                            <nav aria-label="Bagian formulir pendaftaran" className="border-t border-slate-200 pt-4 dark:border-slate-700">
                                <p className="mb-2 text-xs font-semibold text-slate-700 dark:text-slate-200">Struktur Pengisian</p>
                                <ol className="flex flex-col gap-1 text-sm">
                                    <li>
                                        <a href="#bagian-identitas" className="inline-flex min-h-11 w-full items-center rounded-lg px-2.5 font-medium text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">
                                            01 &middot; Identitas &amp; Demografi
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#bagian-akademik" className="inline-flex min-h-11 w-full items-center rounded-lg px-2.5 font-medium text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">
                                            02 &middot; Afiliasi Akademik
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#bagian-kredensial" className="inline-flex min-h-11 w-full items-center rounded-lg px-2.5 font-medium text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">
                                            03 &middot; Kredensial Masuk
                                        </a>
                                    </li>
                                </ol>
                            </nav>

                            <div className="border-t border-slate-200 pt-4 dark:border-slate-700">
                                <a
                                    href="/auth/google"
                                    className="flex min-h-11 w-full items-center justify-center gap-2 rounded-lg border border-slate-300 bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-800 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700"
                                >
                                    Daftar Cepat dengan Google
                                </a>
                                <p className="mt-3 text-center text-xs text-slate-600 dark:text-slate-300">
                                    Sudah punya akun?{' '}
                                    <Link href="/login" className="font-semibold text-teal-700 hover:underline dark:text-teal-300">
                                        Masuk di sini
                                    </Link>
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </aside>

                <Card className="lg:col-span-8">
                    <CardContent>
                        <form onSubmit={handleSubmit} className="flex flex-col gap-8">
                            <fieldset id="bagian-identitas" className="scroll-mt-6 flex flex-col gap-4">
                                <div className="border-b border-slate-200 pb-3 dark:border-slate-700">
                                    <legend className="text-base font-bold text-slate-900 dark:text-white">
                                        01 &middot; Identitas &amp; Demografi
                                    </legend>
                                    <p className="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                                        Data responden dijaga kerahasiaannya untuk keperluan evaluasi akademik.
                                    </p>
                                </div>

                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-12">
                                    <div className="flex flex-col gap-1.5 sm:col-span-8">
                                        <Label htmlFor="name">Nama Lengkap *</Label>
                                        <Input
                                            id="name"
                                            name="name"
                                            required
                                            value={form.data.name}
                                            onChange={(e) => handleNameChange(e.target.value)}
                                            aria-invalid={Boolean(form.errors.name)}
                                            placeholder="Contoh: Aisyah Putri Maharani"
                                        />
                                        <FieldError message={form.errors.name} />
                                    </div>

                                    <div className="flex flex-col gap-1.5 sm:col-span-4">
                                        <Label htmlFor="initials">Inisial Responden</Label>
                                        <Input
                                            id="initials"
                                            name="initials"
                                            value={form.data.initials}
                                            onChange={(e) => {
                                                setInitialsEdited(true);
                                                form.setData('initials', e.target.value);
                                            }}
                                            placeholder="APM"
                                        />
                                        <FieldError message={form.errors.initials} />
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-12">
                                    <div className="flex flex-col gap-1.5 sm:col-span-4">
                                        <Label htmlFor="usia">Usia (Tahun) *</Label>
                                        <Input
                                            id="usia"
                                            name="usia"
                                            type="number"
                                            min={15}
                                            max={40}
                                            required
                                            value={form.data.usia}
                                            onChange={(e) => form.setData('usia', e.target.value)}
                                            aria-invalid={Boolean(form.errors.usia)}
                                        />
                                        <FieldError message={form.errors.usia} />
                                    </div>

                                    <div className="flex flex-col gap-1.5 sm:col-span-8">
                                        <span className="text-xs font-semibold text-slate-700 dark:text-slate-200">Jenis Kelamin *</span>
                                        <div className="grid grid-cols-2 gap-3">
                                            {([
                                                { value: 'P', label: 'Perempuan' },
                                                { value: 'L', label: 'Laki-laki' },
                                            ] as const).map((item) => {
                                                const active = form.data.gender === item.value;
                                                return (
                                                    <label
                                                        key={item.value}
                                                        className={cn(
                                                            'flex min-h-11 cursor-pointer items-center gap-2.5 rounded-lg border px-3.5 py-2.5 text-sm font-medium transition-colors focus-within:outline-2 focus-within:outline-offset-2 focus-within:outline-teal-700 dark:focus-within:outline-teal-400',
                                                            active
                                                                ? 'border-teal-700 bg-teal-50 text-slate-900 dark:border-teal-400 dark:bg-teal-950/50 dark:text-white'
                                                                : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200',
                                                        )}
                                                    >
                                                        <input
                                                            type="radio"
                                                            name="gender"
                                                            value={item.value}
                                                            checked={active}
                                                            onChange={() => form.setData('gender', item.value)}
                                                            className="size-4 border-slate-300 text-teal-700"
                                                        />
                                                        <span>{item.label}</span>
                                                    </label>
                                                );
                                            })}
                                        </div>
                                        <FieldError message={form.errors.gender} />
                                    </div>
                                </div>

                                {form.data.gender === 'P' && (
                                    <div className="flex flex-col gap-1.5 sm:max-w-xs">
                                        <Label htmlFor="menarche_age">Usia Haid Pertama / Menarche (Opsional)</Label>
                                        <Input
                                            id="menarche_age"
                                            name="menarche_age"
                                            type="number"
                                            min={8}
                                            max={25}
                                            value={form.data.menarche_age}
                                            onChange={(e) => form.setData('menarche_age', e.target.value)}
                                            placeholder="Contoh: 12"
                                        />
                                        <FieldError message={form.errors.menarche_age} />
                                    </div>
                                )}

                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div className="flex flex-col gap-1.5">
                                        <Label htmlFor="agama">Agama *</Label>
                                        <Select
                                            id="agama"
                                            name="agama"
                                            required
                                            value={form.data.agama}
                                            onChange={(e) => form.setData('agama', e.target.value)}
                                        >
                                            {DAFTAR_AGAMA.map((agama) => (
                                                <option key={agama} value={agama}>
                                                    {agama}
                                                </option>
                                            ))}
                                        </Select>
                                        <FieldError message={form.errors.agama} />
                                    </div>

                                    <div className="flex flex-col gap-1.5">
                                        <Label htmlFor="pendidikan_terakhir">Pendidikan Terakhir *</Label>
                                        <Select
                                            id="pendidikan_terakhir"
                                            name="pendidikan_terakhir"
                                            required
                                            value={form.data.pendidikan_terakhir}
                                            onChange={(e) => form.setData('pendidikan_terakhir', e.target.value)}
                                        >
                                            {DAFTAR_JENJANG_PENDIDIKAN.map((item) => (
                                                <option key={item.value} value={item.value}>
                                                    {item.label}
                                                </option>
                                            ))}
                                        </Select>
                                        <FieldError message={form.errors.pendidikan_terakhir} />
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset id="bagian-akademik" className="scroll-mt-6 flex flex-col gap-4">
                                <div className="border-b border-slate-200 pb-3 dark:border-slate-700">
                                    <legend className="text-base font-bold text-slate-900 dark:text-white">
                                        02 &middot; Afiliasi Akademik
                                    </legend>
                                    <p className="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                                        Pilih fakultas sesuai universitas (UNIB, UINFAS, atau DEHASEN) dan tuliskan program studi Anda.
                                    </p>
                                </div>

                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="fakultas">Fakultas</Label>
                                    <Select
                                        id="fakultas"
                                        name="fakultas"
                                        value={form.data.fakultas}
                                        onChange={(e) => form.setData('fakultas', e.target.value)}
                                    >
                                        <option value="">Pilih fakultas sesuai kampus</option>
                                        {DAFTAR_FAKULTAS_PER_KAMPUS.map((group) => (
                                            <optgroup key={group.kampus} label={group.kampus}>
                                                {group.options.map((opt) => (
                                                    <option key={opt.value} value={opt.value}>
                                                        {opt.label}
                                                    </option>
                                                ))}
                                            </optgroup>
                                        ))}
                                    </Select>
                                    <FieldError message={form.errors.fakultas} />
                                </div>

                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-12">
                                    <div className="flex flex-col gap-1.5 sm:col-span-7">
                                        <Label htmlFor="prodi">Program Studi *</Label>
                                        <Input
                                            id="prodi"
                                            name="prodi"
                                            required
                                            value={form.data.prodi}
                                            onChange={(e) => form.setData('prodi', e.target.value)}
                                            aria-invalid={Boolean(form.errors.prodi)}
                                            placeholder="Contoh: Pendidikan Biologi / Informatika"
                                        />
                                        <FieldError message={form.errors.prodi} />
                                    </div>

                                    <div className="flex flex-col gap-1.5 sm:col-span-5">
                                        <Label htmlFor="nim">NIM Mahasiswa (Opsional)</Label>
                                        <Input
                                            id="nim"
                                            name="nim"
                                            value={form.data.nim}
                                            onChange={(e) => form.setData('nim', e.target.value)}
                                            placeholder="Contoh: A1D026045"
                                        />
                                        <FieldError message={form.errors.nim} />
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset id="bagian-kredensial" className="scroll-mt-6 flex flex-col gap-4">
                                <div className="border-b border-slate-200 pb-3 dark:border-slate-700">
                                    <legend className="text-base font-bold text-slate-900 dark:text-white">
                                        03 &middot; Kredensial Masuk
                                    </legend>
                                    <p className="mt-0.5 text-xs text-slate-600 dark:text-slate-300">
                                        Kata sandi minimal 6 karakter untuk mengamankan riwayat progres dan evaluasi Anda.
                                    </p>
                                </div>

                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="email">Alamat Email Aktif *</Label>
                                    <Input
                                        id="email"
                                        name="email"
                                        type="email"
                                        autoComplete="email"
                                        required
                                        value={form.data.email}
                                        onChange={(e) => form.setData('email', e.target.value)}
                                        aria-invalid={Boolean(form.errors.email)}
                                        placeholder="nama@unib.ac.id"
                                    />
                                    <FieldError message={form.errors.email} />
                                </div>

                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div className="flex flex-col gap-1.5">
                                        <Label htmlFor="password">Kata Sandi *</Label>
                                        <Input
                                            id="password"
                                            name="password"
                                            type={showPassword ? 'text' : 'password'}
                                            autoComplete="new-password"
                                            required
                                            minLength={6}
                                            value={form.data.password}
                                            onChange={(e) => form.setData('password', e.target.value)}
                                            aria-invalid={Boolean(form.errors.password)}
                                            placeholder="Minimal 6 karakter"
                                        />
                                        <FieldError message={form.errors.password} />
                                    </div>

                                    <div className="flex flex-col gap-1.5">
                                        <Label htmlFor="password_confirmation">Ulangi Kata Sandi *</Label>
                                        <Input
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            type={showPassword ? 'text' : 'password'}
                                            autoComplete="new-password"
                                            required
                                            minLength={6}
                                            value={form.data.password_confirmation}
                                            onChange={(e) => form.setData('password_confirmation', e.target.value)}
                                            placeholder="Ketik ulang kata sandi"
                                        />
                                    </div>
                                </div>

                                <label className="inline-flex min-h-11 items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-200">
                                    <input
                                        type="checkbox"
                                        checked={showPassword}
                                        onChange={(e) => setShowPassword(e.target.checked)}
                                        className="size-4 rounded border-slate-300 text-teal-700"
                                    />
                                    <span>Tampilkan kata sandi</span>
                                </label>
                            </fieldset>

                            <div className="flex flex-col-reverse items-stretch justify-between gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:items-center dark:border-slate-700">
                                <Link href="/login" className="text-sm font-semibold text-slate-700 hover:underline dark:text-slate-200">
                                    Sudah punya akun? Masuk
                                </Link>
                                <Button type="submit" size="lg" disabled={form.processing}>
                                    {form.processing ? 'Mendaftarkan akun...' : 'Buat Akun & Masuk Ruang Belajar'}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AuthLayout>
    );
}
