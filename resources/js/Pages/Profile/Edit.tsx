import { Head, useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { FieldError } from '@/components/ui/field-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import {
    DAFTAR_AGAMA,
    DAFTAR_FAKULTAS_PER_KAMPUS,
    DAFTAR_JENJANG_PENDIDIKAN,
} from '@/constants/akademik';
import AppLayout from '@/layouts/AppLayout';
import type { AuthUser, SharedProps } from '@/types/sikera';

interface ProfileEditProps {
    user: AuthUser;
}

export default function Edit({ user }: ProfileEditProps) {
    const { auth } = usePage<SharedProps>().props;
    const googleLinked = Boolean(auth.user?.google_linked);
    const isMahasiswa = user.role === 'mahasiswa';

    const form = useForm({
        name: user.name ?? '',
        initials: user.initials ?? '',
        email: user.email ?? '',
        current_password: '',
        avatar: user.avatar ?? '',
        usia: user.usia ? String(user.usia) : '',
        gender: (user.gender ?? 'P') as 'P' | 'L',
        agama: user.agama ?? 'Islam',
        menarche_age: user.menarche_age ? String(user.menarche_age) : '',
        nim: user.nim ?? '',
        fakultas: user.fakultas ?? '',
        prodi: user.prodi ?? '',
        pendidikan_terakhir: user.pendidikan_terakhir ?? 'SMA/SMK/MA/Sederajat',
    });

    const emailChanged = !googleLinked && form.data.email.trim() !== user.email;

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        form.transform((data) => {
            const payload: Record<string, unknown> = {
                name: data.name,
                initials: data.initials || null,
                email: data.email,
                usia: data.usia ? Number(data.usia) : null,
                gender: data.gender || null,
                agama: data.agama || null,
                menarche_age: data.menarche_age ? Number(data.menarche_age) : null,
                nim: data.nim || null,
                fakultas: data.fakultas || null,
                prodi: data.prodi || null,
                pendidikan_terakhir: data.pendidikan_terakhir || null,
            };

            // UpdateProfileRequest menetapkan aturan 'missing' untuk avatar pada akun yang tertaut Google.
            if (!googleLinked) {
                payload.avatar = data.avatar || null;
            }
            if (emailChanged) {
                payload.current_password = data.current_password;
            }
            return payload;
        });

        form.put('/profil', {
            preserveScroll: true,
            onSuccess: () => form.setData('current_password', ''),
        });
    };

    return (
        <AppLayout title="Profil & Biodata Akun">
            <Head title="Profil Saya" />

            <div className="mx-auto max-w-3xl">
                <Card>
                    <CardHeader>
                        <div className="flex flex-wrap items-center justify-between gap-2">
                            <CardTitle>Pengaturan Profil &amp; Biodata</CardTitle>
                            {googleLinked && <Badge color="info">Tertaut Akun Google</Badge>}
                        </div>
                        <CardDescription>
                            Perbarui identitas, afiliasi studi, serta pengaturan email akun Anda.
                        </CardDescription>
                    </CardHeader>

                    <CardContent>
                        <form onSubmit={handleSubmit} className="flex flex-col gap-6">
                            <fieldset className="flex flex-col gap-4">
                                <legend className="text-sm font-bold text-slate-900 dark:text-white">Akun Utama</legend>

                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-12">
                                    <div className="flex flex-col gap-1.5 sm:col-span-8">
                                        <Label htmlFor="prof-name">Nama Lengkap *</Label>
                                        <Input
                                            id="prof-name"
                                            name="name"
                                            required
                                            value={form.data.name}
                                            onChange={(e) => form.setData('name', e.target.value)}
                                            aria-invalid={Boolean(form.errors.name)}
                                        />
                                        <FieldError message={form.errors.name} />
                                    </div>

                                    <div className="flex flex-col gap-1.5 sm:col-span-4">
                                        <Label htmlFor="prof-initials">Inisial</Label>
                                        <Input
                                            id="prof-initials"
                                            name="initials"
                                            value={form.data.initials}
                                            onChange={(e) => form.setData('initials', e.target.value)}
                                        />
                                        <FieldError message={form.errors.initials} />
                                    </div>
                                </div>

                                <div className="flex flex-col gap-1.5">
                                    <Label htmlFor="prof-email">Alamat Email *</Label>
                                    <Input
                                        id="prof-email"
                                        name="email"
                                        type="email"
                                        required
                                        disabled={googleLinked}
                                        value={form.data.email}
                                        onChange={(e) => form.setData('email', e.target.value)}
                                        aria-invalid={Boolean(form.errors.email)}
                                    />
                                    {googleLinked && (
                                        <p className="text-xs text-slate-600 dark:text-slate-300">
                                            Email dan foto profil dikelola langsung oleh akun Google Anda.
                                        </p>
                                    )}
                                    <FieldError message={form.errors.email} />
                                </div>

                                {emailChanged && (
                                    <div className="flex flex-col gap-1.5">
                                        <Label htmlFor="prof-current-password">Kata Sandi Saat Ini (Wajib untuk Ubah Email) *</Label>
                                        <Input
                                            id="prof-current-password"
                                            name="current_password"
                                            type="password"
                                            required
                                            value={form.data.current_password}
                                            onChange={(e) => form.setData('current_password', e.target.value)}
                                            aria-invalid={Boolean(form.errors.current_password)}
                                        />
                                        <FieldError message={form.errors.current_password} />
                                    </div>
                                )}

                                {!googleLinked && (
                                    <div className="flex flex-col gap-1.5">
                                        <Label htmlFor="prof-avatar">Tautan Foto Profil HTTPS (Opsional)</Label>
                                        <Input
                                            id="prof-avatar"
                                            name="avatar"
                                            type="url"
                                            value={form.data.avatar}
                                            onChange={(e) => form.setData('avatar', e.target.value)}
                                            aria-invalid={Boolean(form.errors.avatar)}
                                            placeholder="https://..."
                                        />
                                        <FieldError message={form.errors.avatar} />
                                    </div>
                                )}
                            </fieldset>

                            <fieldset className="flex flex-col gap-4 border-t border-slate-200 pt-5 dark:border-slate-700">
                                <legend className="pr-3 text-sm font-bold text-slate-900 dark:text-white">Biodata Pribadi</legend>
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div className="flex flex-col gap-1.5">
                                        <Label htmlFor="prof-usia">Usia (Tahun) {isMahasiswa ? '*' : ''}</Label>
                                        <Input
                                            id="prof-usia"
                                            name="usia"
                                            type="number"
                                            min={isMahasiswa ? 15 : 10}
                                            max={isMahasiswa ? 40 : 100}
                                            required={isMahasiswa}
                                            value={form.data.usia}
                                            onChange={(e) => form.setData('usia', e.target.value)}
                                            aria-invalid={Boolean(form.errors.usia)}
                                        />
                                        <FieldError message={form.errors.usia} />
                                    </div>

                                    <div className="flex flex-col gap-1.5">
                                        <Label htmlFor="prof-gender">Jenis Kelamin {isMahasiswa ? '*' : ''}</Label>
                                        <Select
                                            id="prof-gender"
                                            name="gender"
                                            required={isMahasiswa}
                                            value={form.data.gender}
                                            onChange={(e) => form.setData('gender', e.target.value as 'P' | 'L')}
                                        >
                                            <option value="P">Perempuan</option>
                                            <option value="L">Laki-laki</option>
                                        </Select>
                                        <FieldError message={form.errors.gender} />
                                    </div>

                                    <div className="flex flex-col gap-1.5">
                                        <Label htmlFor="prof-agama">Agama {isMahasiswa ? '*' : ''}</Label>
                                        <Select
                                            id="prof-agama"
                                            name="agama"
                                            required={isMahasiswa}
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
                                        <Label htmlFor="prof-menarche">Usia Saat Haid Pertama (Tahun)</Label>
                                        <Input
                                            id="prof-menarche"
                                            name="menarche_age"
                                            type="number"
                                            min={8}
                                            max={25}
                                            value={form.data.menarche_age}
                                            onChange={(e) => form.setData('menarche_age', e.target.value)}
                                        />
                                        <FieldError message={form.errors.menarche_age} />
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset className="flex flex-col gap-4 border-t border-slate-200 pt-5 dark:border-slate-700">
                                <legend className="pr-3 text-sm font-bold text-slate-900 dark:text-white">Data Akademik</legend>
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div className="flex flex-col gap-1.5">
                                        <Label htmlFor="prof-nim">Nomor Induk Mahasiswa (NIM)</Label>
                                        <Input
                                            id="prof-nim"
                                            name="nim"
                                            value={form.data.nim}
                                            onChange={(e) => form.setData('nim', e.target.value)}
                                            aria-invalid={Boolean(form.errors.nim)}
                                        />
                                        <FieldError message={form.errors.nim} />
                                    </div>

                                    <div className="flex flex-col gap-1.5">
                                        <Label htmlFor="prof-fakultas">Fakultas</Label>
                                        <Select
                                            id="prof-fakultas"
                                            name="fakultas"
                                            value={form.data.fakultas}
                                            onChange={(e) => form.setData('fakultas', e.target.value)}
                                        >
                                            <option value="">Pilih fakultas</option>
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

                                    <div className="flex flex-col gap-1.5">
                                        <Label htmlFor="prof-prodi">Program Studi {isMahasiswa ? '*' : ''}</Label>
                                        <Input
                                            id="prof-prodi"
                                            name="prodi"
                                            required={isMahasiswa}
                                            value={form.data.prodi}
                                            onChange={(e) => form.setData('prodi', e.target.value)}
                                            aria-invalid={Boolean(form.errors.prodi)}
                                        />
                                        <FieldError message={form.errors.prodi} />
                                    </div>

                                    <div className="flex flex-col gap-1.5">
                                        <Label htmlFor="prof-pendidikan">Pendidikan Terakhir {isMahasiswa ? '*' : ''}</Label>
                                        <Select
                                            id="prof-pendidikan"
                                            name="pendidikan_terakhir"
                                            required={isMahasiswa}
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

                            <div className="flex justify-end border-t border-slate-200 pt-4 dark:border-slate-700">
                                <Button type="submit" disabled={form.processing}>
                                    {form.processing ? 'Menyimpan perubahan...' : 'Simpan Perubahan Profil'}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
