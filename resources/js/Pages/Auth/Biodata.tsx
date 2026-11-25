import { Head, router, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
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
import type { AuthUser } from '@/types/sikera';

interface BiodataProps {
    user: AuthUser;
}

export default function Biodata({ user }: BiodataProps) {
    const form = useForm({
        name: user.name ?? '',
        initials: user.initials ?? buildAutoInitials(user.name ?? ''),
        usia: user.usia ? String(user.usia) : '18',
        gender: (user.gender ?? 'P') as 'P' | 'L',
        agama: user.agama ?? 'Islam',
        menarche_age: user.menarche_age ? String(user.menarche_age) : '',
        nim: user.nim ?? '',
        fakultas: user.fakultas ?? '',
        prodi: user.prodi ?? '',
        pendidikan_terakhir: user.pendidikan_terakhir ?? 'SMA/SMK/MA/Sederajat',
    });

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        form.post('/biodata');
    };

    return (
        <AuthLayout width="md">
            <Head title="Lengkapi Biodata Mahasiswa" />
            <Card>
                <CardHeader>
                    <p className="text-xs font-semibold text-teal-700 dark:text-teal-300">Langkah Penyelesaian Akun</p>
                    <CardTitle className="text-xl sm:text-2xl">Lengkapi Biodata Responden</CardTitle>
                    <CardDescription>
                        Isi data demografi dan program studi agar progres modul serta evaluasi Anda tercatat dengan benar.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form onSubmit={handleSubmit} className="flex flex-col gap-5">
                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-12">
                            <div className="flex flex-col gap-1.5 sm:col-span-8">
                                <Label htmlFor="bio-name">Nama Lengkap *</Label>
                                <Input
                                    id="bio-name"
                                    name="name"
                                    required
                                    value={form.data.name}
                                    onChange={(e) => form.setData('name', e.target.value)}
                                    aria-invalid={Boolean(form.errors.name)}
                                />
                                <FieldError message={form.errors.name} />
                            </div>
                            <div className="flex flex-col gap-1.5 sm:col-span-4">
                                <Label htmlFor="bio-initials">Inisial</Label>
                                <Input
                                    id="bio-initials"
                                    name="initials"
                                    value={form.data.initials}
                                    onChange={(e) => form.setData('initials', e.target.value)}
                                />
                                <FieldError message={form.errors.initials} />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="bio-usia">Usia (Tahun) *</Label>
                                <Input
                                    id="bio-usia"
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

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="bio-gender">Jenis Kelamin *</Label>
                                <Select
                                    id="bio-gender"
                                    name="gender"
                                    required
                                    value={form.data.gender}
                                    onChange={(e) => form.setData('gender', e.target.value as 'P' | 'L')}
                                >
                                    <option value="P">Perempuan</option>
                                    <option value="L">Laki-laki</option>
                                </Select>
                                <FieldError message={form.errors.gender} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="bio-agama">Agama *</Label>
                                <Select
                                    id="bio-agama"
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
                        </div>

                        {form.data.gender === 'P' && (
                            <div className="flex flex-col gap-1.5 sm:max-w-xs">
                                <Label htmlFor="bio-menarche">Usia Haid Pertama (Opsional)</Label>
                                <Input
                                    id="bio-menarche"
                                    name="menarche_age"
                                    type="number"
                                    min={8}
                                    max={25}
                                    value={form.data.menarche_age}
                                    onChange={(e) => form.setData('menarche_age', e.target.value)}
                                />
                                <FieldError message={form.errors.menarche_age} />
                            </div>
                        )}

                        <div className="flex flex-col gap-1.5">
                            <Label htmlFor="bio-fakultas">Fakultas</Label>
                            <Select
                                id="bio-fakultas"
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

                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="bio-prodi">Program Studi *</Label>
                                <Input
                                    id="bio-prodi"
                                    name="prodi"
                                    required
                                    value={form.data.prodi}
                                    onChange={(e) => form.setData('prodi', e.target.value)}
                                    aria-invalid={Boolean(form.errors.prodi)}
                                />
                                <FieldError message={form.errors.prodi} />
                            </div>

                            <div className="flex flex-col gap-1.5">
                                <Label htmlFor="bio-nim">NIM Mahasiswa</Label>
                                <Input
                                    id="bio-nim"
                                    name="nim"
                                    value={form.data.nim}
                                    onChange={(e) => form.setData('nim', e.target.value)}
                                />
                                <FieldError message={form.errors.nim} />
                            </div>
                        </div>

                        <div className="flex flex-col gap-1.5">
                            <Label htmlFor="bio-pendidikan">Pendidikan Terakhir *</Label>
                            <Select
                                id="bio-pendidikan"
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

                        <div className="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-4 dark:border-slate-700">
                            <Button type="button" variant="outline" onClick={() => router.post('/logout')}>
                                Keluar Akun
                            </Button>
                            <Button type="submit" disabled={form.processing}>
                                {form.processing ? 'Menyimpan...' : 'Simpan Biodata & Lanjutkan'}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </AuthLayout>
    );
}
