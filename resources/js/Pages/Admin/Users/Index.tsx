import { Head, router, useForm, usePage } from '@inertiajs/react';
import { useState, type FormEvent } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { EmptyState } from '@/components/ui/empty-state';
import { FieldError } from '@/components/ui/field-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
    TableWrapper,
} from '@/components/ui/table';
import {
    DAFTAR_AGAMA,
    DAFTAR_FAKULTAS_PER_KAMPUS,
    DAFTAR_JENJANG_PENDIDIKAN,
} from '@/constants/akademik';
import { PencilIcon, PlusIcon, SearchIcon, TrashBinIcon, UsersIcon } from '@/icons';
import AppLayout from '@/layouts/AppLayout';
import type { AuthUser, SharedProps, UserRole } from '@/types/sikera';
import { cn } from '@/utils';

interface AdminUsersProps {
    users: AuthUser[];
    roleFilter: string;
    searchQuery: string | null;
    totalAllUsers: number;
    totalMhsUsers: number;
    totalDosenUsers: number;
    totalAdminUsers: number;
}

export default function Index({
    users,
    roleFilter,
    searchQuery,
    totalAllUsers,
    totalMhsUsers,
    totalDosenUsers,
    totalAdminUsers,
}: AdminUsersProps) {
    const { auth } = usePage<SharedProps>().props;
    const [search, setSearch] = useState(searchQuery ?? '');
    const [dialogOpen, setDialogOpen] = useState(false);
    const [editingUser, setEditingUser] = useState<AuthUser | null>(null);
    const [deletingUser, setDeletingUser] = useState<AuthUser | null>(null);
    const [deleting, setDeleting] = useState(false);

    const form = useForm({
        name: '',
        initials: '',
        email: '',
        nim: '',
        role: 'mahasiswa' as UserRole,
        usia: '18',
        gender: 'P' as 'P' | 'L',
        agama: 'Islam',
        prodi: '',
        fakultas: '',
        pendidikan_terakhir: 'SMA/SMK/MA/Sederajat',
        points: '0',
        is_biodata_filled: true,
        pretest_completed: false,
        posttest_completed: false,
        password: '',
        password_confirmation: '',
    });

    const openCreate = () => {
        setEditingUser(null);
        form.clearErrors();
        form.setData({
            name: '',
            initials: '',
            email: '',
            nim: '',
            role: 'mahasiswa',
            usia: '18',
            gender: 'P',
            agama: 'Islam',
            prodi: '',
            fakultas: '',
            pendidikan_terakhir: 'SMA/SMK/MA/Sederajat',
            points: '0',
            is_biodata_filled: true,
            pretest_completed: false,
            posttest_completed: false,
            password: '',
            password_confirmation: '',
        });
        setDialogOpen(true);
    };

    const openEdit = (user: AuthUser) => {
        setEditingUser(user);
        form.clearErrors();
        form.setData({
            name: user.name ?? '',
            initials: user.initials ?? '',
            email: user.email ?? '',
            nim: user.nim ?? '',
            role: user.role,
            usia: user.usia ? String(user.usia) : '',
            gender: (user.gender ?? 'P') as 'P' | 'L',
            agama: user.agama ?? 'Islam',
            prodi: user.prodi ?? '',
            fakultas: user.fakultas ?? '',
            pendidikan_terakhir: user.pendidikan_terakhir ?? 'SMA/SMK/MA/Sederajat',
            points: String(user.points ?? 0),
            is_biodata_filled: Boolean(user.is_biodata_filled),
            pretest_completed: Boolean(user.pretest_completed),
            posttest_completed: Boolean(user.posttest_completed),
            password: '',
            password_confirmation: '',
        });
        setDialogOpen(true);
    };

    const applyFilter = (nextRole: string, nextSearch = search) => {
        router.get(
            '/admin/users',
            {
                ...(nextRole !== 'all' ? { role: nextRole } : {}),
                ...(nextSearch.trim() ? { q: nextSearch.trim() } : {}),
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    const submitUser = (event: FormEvent) => {
        event.preventDefault();
        if (editingUser) {
            form.put(`/admin/users/${editingUser.id}`, {
                preserveScroll: true,
                onSuccess: () => setDialogOpen(false),
            });
        } else {
            form.post('/admin/users', {
                preserveScroll: true,
                onSuccess: () => setDialogOpen(false),
            });
        }
    };

    const confirmDelete = () => {
        if (!deletingUser) {
            return;
        }
        setDeleting(true);
        router.delete(`/admin/users/${deletingUser.id}`, {
            preserveScroll: true,
            onFinish: () => {
                setDeleting(false);
                setDeletingUser(null);
            },
        });
    };

    const tabs = [
        { key: 'all', label: `Semua (${totalAllUsers})` },
        { key: 'mahasiswa', label: `Mahasiswa (${totalMhsUsers})` },
        { key: 'dosen_pa', label: `Dosen PA (${totalDosenUsers})` },
        { key: 'admin', label: `Admin (${totalAdminUsers})` },
    ];

    return (
        <AppLayout
            title="Daftar Pengguna Terdaftar"
            actions={
                <Button size="sm" onClick={openCreate}>
                    <PlusIcon />
                    Tambah Pengguna
                </Button>
            }
        >
            <Head title="Kelola Pengguna" />

            <div className="flex flex-col gap-5">
                <Card size="sm" className="flex-col justify-between gap-3 sm:flex-row sm:items-center">
                    <div className="flex gap-1.5 overflow-x-auto pb-1 sm:pb-0" role="tablist" aria-label="Filter peran pengguna">
                        {tabs.map((tab) => {
                            const active = (roleFilter || 'all') === tab.key;
                            return (
                                <button
                                    key={tab.key}
                                    type="button"
                                    role="tab"
                                    aria-selected={active}
                                    onClick={() => applyFilter(tab.key)}
                                    className={cn(
                                        'inline-flex min-h-11 shrink-0 items-center rounded-lg border px-3 text-xs font-semibold transition-colors',
                                        active
                                            ? 'border-teal-700 bg-teal-700 text-white dark:border-teal-400 dark:bg-teal-400 dark:text-slate-950'
                                            : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200',
                                    )}
                                >
                                    {tab.label}
                                </button>
                            );
                        })}
                    </div>

                    <form
                        onSubmit={(e) => {
                            e.preventDefault();
                            applyFilter(roleFilter || 'all', search);
                        }}
                        className="flex items-center gap-2"
                    >
                        <Label htmlFor="admin-user-search" className="sr-only">
                            Cari pengguna
                        </Label>
                        <Input
                            id="admin-user-search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Cari nama, NIM, atau email..."
                            className="sm:w-64"
                        />
                        <Button type="submit" variant="outline" size="icon" aria-label="Cari pengguna">
                            <SearchIcon />
                        </Button>
                    </form>
                </Card>

                {users.length === 0 ? (
                    <EmptyState
                        icon={<UsersIcon className="size-8" />}
                        title="Tidak ada pengguna yang cocok"
                        description="Ubah kata kunci pencarian atau tambahkan akun baru."
                    />
                ) : (
                    <TableWrapper>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nama &amp; Email</TableHead>
                                    <TableHead>NIM / Prodi</TableHead>
                                    <TableHead>Peran</TableHead>
                                    <TableHead>Evaluasi</TableHead>
                                    <TableHead>Aksi</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {users.map((u) => (
                                    <TableRow key={u.id}>
                                        <TableCell>
                                            <div className="font-semibold">{u.name}</div>
                                            <div className="text-xs text-slate-600 dark:text-slate-300">{u.email}</div>
                                        </TableCell>
                                        <TableCell>
                                            <div className="text-xs font-semibold">{u.nim || '-'}</div>
                                            <div className="text-xs text-slate-600 dark:text-slate-300">{u.prodi || '-'}</div>
                                        </TableCell>
                                        <TableCell>
                                            <Badge
                                                color={u.role === 'admin' ? 'error' : u.role === 'dosen_pa' ? 'info' : 'primary'}
                                                size="sm"
                                            >
                                                {u.role === 'admin' ? 'Admin' : u.role === 'dosen_pa' ? 'Dosen PA' : 'Mahasiswa'}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            {u.role === 'mahasiswa' ? (
                                                <div className="flex flex-wrap gap-1">
                                                    <Badge color={u.pretest_completed ? 'info' : 'gray'} size="sm">
                                                        Pre: {u.pretest_completed ? 'Ya' : 'Belum'}
                                                    </Badge>
                                                    <Badge color={u.posttest_completed ? 'success' : 'gray'} size="sm">
                                                        Post: {u.posttest_completed ? 'Ya' : 'Belum'}
                                                    </Badge>
                                                </div>
                                            ) : (
                                                <span className="text-xs text-slate-500">-</span>
                                            )}
                                        </TableCell>
                                        <TableCell>
                                            <div className="flex items-center gap-1.5">
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    size="icon"
                                                    onClick={() => openEdit(u)}
                                                    aria-label={`Edit pengguna ${u.name}`}
                                                >
                                                    <PencilIcon />
                                                </Button>
                                                {u.id !== auth.user?.id && (
                                                    <Button
                                                        type="button"
                                                        variant="destructive"
                                                        size="icon"
                                                        onClick={() => setDeletingUser(u)}
                                                        aria-label={`Hapus pengguna ${u.name}`}
                                                    >
                                                        <TrashBinIcon />
                                                    </Button>
                                                )}
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </TableWrapper>
                )}
            </div>

            <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
                <DialogContent className="sm:max-w-xl">
                    <DialogHeader>
                        <DialogTitle>{editingUser ? 'Perbarui Data Pengguna' : 'Tambah Pengguna Baru'}</DialogTitle>
                        <DialogDescription>
                            Isi data akun dan hak akses sesuai peran di SIKERA.
                        </DialogDescription>
                    </DialogHeader>

                    <form onSubmit={submitUser} className="flex flex-col gap-4">
                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div className="flex flex-col gap-1">
                                <Label htmlFor="u-name">Nama Lengkap *</Label>
                                <Input
                                    id="u-name"
                                    required
                                    value={form.data.name}
                                    onChange={(e) => form.setData('name', e.target.value)}
                                />
                                <FieldError message={form.errors.name} />
                            </div>
                            <div className="flex flex-col gap-1">
                                <Label htmlFor="u-email">Email *</Label>
                                <Input
                                    id="u-email"
                                    type="email"
                                    required
                                    value={form.data.email}
                                    onChange={(e) => form.setData('email', e.target.value)}
                                />
                                <FieldError message={form.errors.email} />
                            </div>
                            <div className="flex flex-col gap-1">
                                <Label htmlFor="u-role">Peran *</Label>
                                <Select
                                    id="u-role"
                                    value={form.data.role}
                                    onChange={(e) => form.setData('role', e.target.value as UserRole)}
                                >
                                    <option value="mahasiswa">Mahasiswa Responden</option>
                                    <option value="dosen_pa">Dosen Pembimbing Akademik</option>
                                    <option value="admin">Super Admin</option>
                                </Select>
                            </div>
                            <div className="flex flex-col gap-1">
                                <Label htmlFor="u-nim">NIM / NIP</Label>
                                <Input
                                    id="u-nim"
                                    value={form.data.nim}
                                    onChange={(e) => form.setData('nim', e.target.value)}
                                />
                                <FieldError message={form.errors.nim} />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <div className="flex flex-col gap-1">
                                <Label htmlFor="u-usia">Usia</Label>
                                <Input
                                    id="u-usia"
                                    type="number"
                                    value={form.data.usia}
                                    onChange={(e) => form.setData('usia', e.target.value)}
                                />
                            </div>
                            <div className="flex flex-col gap-1">
                                <Label htmlFor="u-gender">Jenis Kelamin</Label>
                                <Select
                                    id="u-gender"
                                    value={form.data.gender}
                                    onChange={(e) => form.setData('gender', e.target.value as 'P' | 'L')}
                                >
                                    <option value="P">Perempuan</option>
                                    <option value="L">Laki-laki</option>
                                </Select>
                            </div>
                            <div className="flex flex-col gap-1">
                                <Label htmlFor="u-agama">Agama</Label>
                                <Select
                                    id="u-agama"
                                    value={form.data.agama}
                                    onChange={(e) => form.setData('agama', e.target.value)}
                                >
                                    {DAFTAR_AGAMA.map((a) => (
                                        <option key={a} value={a}>{a}</option>
                                    ))}
                                </Select>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div className="flex flex-col gap-1">
                                <Label htmlFor="u-fakultas">Fakultas</Label>
                                <Select
                                    id="u-fakultas"
                                    value={form.data.fakultas}
                                    onChange={(e) => form.setData('fakultas', e.target.value)}
                                >
                                    <option value="">Pilih fakultas</option>
                                    {DAFTAR_FAKULTAS_PER_KAMPUS.map((group) => (
                                        <optgroup key={group.kampus} label={group.kampus}>
                                            {group.options.map((opt) => (
                                                <option key={opt.value} value={opt.value}>{opt.label}</option>
                                            ))}
                                        </optgroup>
                                    ))}
                                </Select>
                            </div>
                            <div className="flex flex-col gap-1">
                                <Label htmlFor="u-prodi">Program Studi</Label>
                                <Input
                                    id="u-prodi"
                                    value={form.data.prodi}
                                    onChange={(e) => form.setData('prodi', e.target.value)}
                                />
                            </div>
                            <div className="flex flex-col gap-1">
                                <Label htmlFor="u-pendidikan">Pendidikan Terakhir</Label>
                                <Select
                                    id="u-pendidikan"
                                    value={form.data.pendidikan_terakhir}
                                    onChange={(e) => form.setData('pendidikan_terakhir', e.target.value)}
                                >
                                    {DAFTAR_JENJANG_PENDIDIKAN.map((j) => (
                                        <option key={j.value} value={j.value}>{j.label}</option>
                                    ))}
                                </Select>
                            </div>
                            <div className="flex flex-col gap-1">
                                <Label htmlFor="u-points">Poin</Label>
                                <Input
                                    id="u-points"
                                    type="number"
                                    min={0}
                                    value={form.data.points}
                                    onChange={(e) => form.setData('points', e.target.value)}
                                />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div className="flex flex-col gap-1">
                                <Label htmlFor="u-pass">
                                    Kata Sandi {editingUser ? '(Kosongkan bila tetap)' : '*'}
                                </Label>
                                <Input
                                    id="u-pass"
                                    type="password"
                                    required={!editingUser}
                                    value={form.data.password}
                                    onChange={(e) => form.setData('password', e.target.value)}
                                />
                                <FieldError message={form.errors.password} />
                            </div>
                            <div className="flex flex-col gap-1">
                                <Label htmlFor="u-pass-confirm">Konfirmasi Kata Sandi</Label>
                                <Input
                                    id="u-pass-confirm"
                                    type="password"
                                    required={!editingUser && Boolean(form.data.password)}
                                    value={form.data.password_confirmation}
                                    onChange={(e) => form.setData('password_confirmation', e.target.value)}
                                />
                            </div>
                        </div>

                        <DialogFooter>
                            <Button type="button" variant="outline" onClick={() => setDialogOpen(false)}>
                                Batal
                            </Button>
                            <Button type="submit" disabled={form.processing}>
                                {form.processing ? 'Menyimpan...' : 'Simpan Pengguna'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <Dialog open={Boolean(deletingUser)} onOpenChange={(open) => !open && setDeletingUser(null)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Hapus Akun Pengguna?</DialogTitle>
                        <DialogDescription>
                            Akun <strong>{deletingUser?.name}</strong> beserta riwayat aktivitasnya akan dihapus permanen.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button type="button" variant="outline" onClick={() => setDeletingUser(null)}>
                            Batal
                        </Button>
                        <Button type="button" variant="destructive" disabled={deleting} onClick={confirmDelete}>
                            {deleting ? 'Menghapus...' : 'Ya, Hapus Pengguna'}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
