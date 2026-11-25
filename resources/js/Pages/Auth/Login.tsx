import { Head, Link, useForm } from '@inertiajs/react';
import { useState, type FormEvent } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { FieldError } from '@/components/ui/field-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout';

export default function Login() {
    const [showPassword, setShowPassword] = useState(false);
    const form = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        form.post('/login', {
            onFinish: () => form.reset('password'),
        });
    };

    return (
        <AuthLayout width="sm">
            <Head title="Masuk Akun" />
            <Card>
                <CardHeader>
                    <p className="text-xs font-semibold text-teal-700 dark:text-teal-300">Portal Pembelajaran &amp; Evaluasi</p>
                    <CardTitle className="text-xl sm:text-2xl">Masuk ke Akun SIKERA</CardTitle>
                    <CardDescription>
                        Gunakan email yang terdaftar sebagai mahasiswa responden, dosen pembimbing akademik, atau pengelola riset.
                    </CardDescription>
                </CardHeader>

                <CardContent>
                    <a
                        href="/auth/google"
                        className="flex min-h-11 w-full items-center justify-center gap-2.5 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 shadow-theme-xs transition-colors hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700 dark:focus-visible:outline-teal-400"
                    >
                        <svg width={18} height={18} viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#4285F4" d="M23.49 12.27c0-.79-.07-1.54-.19-2.27H12v4.51h6.47c-.29 1.48-1.14 2.73-2.4 3.58v3h3.86c2.26-2.09 3.56-5.17 3.56-8.82z" />
                            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.86-3c-1.08.72-2.45 1.16-4.07 1.16-3.13 0-5.78-2.11-6.73-4.96H1.29v3.09C3.26 21.3 7.31 24 12 24z" />
                            <path fill="#FBBC05" d="M5.27 14.29a7.12 7.12 0 010-4.58V6.62H1.29a11.99 11.99 0 000 10.76l3.98-3.09z" />
                            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.31 0 3.26 2.7 1.29 6.62l3.98 3.09c.95-2.85 3.6-4.96 6.73-4.96z" />
                        </svg>
                        Masuk dengan Akun Google
                    </a>

                    <div className="my-5 flex items-center gap-3">
                        <span className="h-px flex-1 bg-slate-200 dark:bg-slate-700" aria-hidden="true" />
                        <span className="text-xs font-medium text-slate-600 dark:text-slate-300">atau gunakan email</span>
                        <span className="h-px flex-1 bg-slate-200 dark:bg-slate-700" aria-hidden="true" />
                    </div>

                    <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                        <div className="flex flex-col gap-1.5">
                            <Label htmlFor="login-email">Alamat Email</Label>
                            <Input
                                id="login-email"
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

                        <div className="flex flex-col gap-1.5">
                            <div className="flex items-center justify-between gap-2">
                                <Label htmlFor="login-password">Kata Sandi</Label>
                                <button
                                    type="button"
                                    onClick={() => setShowPassword((prev) => !prev)}
                                    className="text-xs font-semibold text-teal-700 hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-teal-300 dark:focus-visible:outline-teal-400"
                                >
                                    {showPassword ? 'Sembunyikan' : 'Tampilkan'}
                                </button>
                            </div>
                            <Input
                                id="login-password"
                                name="password"
                                type={showPassword ? 'text' : 'password'}
                                autoComplete="current-password"
                                required
                                value={form.data.password}
                                onChange={(e) => form.setData('password', e.target.value)}
                                aria-invalid={Boolean(form.errors.password)}
                                placeholder="Masukkan kata sandi"
                            />
                            <FieldError message={form.errors.password} />
                        </div>

                        <label className="inline-flex min-h-11 items-center gap-2.5 text-sm text-slate-700 dark:text-slate-200">
                            <input
                                type="checkbox"
                                name="remember"
                                checked={form.data.remember}
                                onChange={(e) => form.setData('remember', e.target.checked)}
                                className="size-4 rounded border-slate-300 text-teal-700 focus:ring-teal-700 dark:border-slate-600 dark:bg-slate-800"
                            />
                            <span>Ingat sesi masuk pada perangkat ini</span>
                        </label>

                        <Button type="submit" disabled={form.processing} className="w-full">
                            {form.processing ? 'Memverifikasi akun...' : 'Masuk ke Ruang Kerja'}
                        </Button>
                    </form>

                    <p className="mt-5 border-t border-slate-200 pt-4 text-center text-sm text-slate-600 dark:border-slate-700 dark:text-slate-300">
                        Belum memiliki akun mahasiswa?{' '}
                        <Link href="/register" className="font-semibold text-teal-700 hover:underline dark:text-teal-300">
                            Daftar responden baru
                        </Link>
                    </p>
                </CardContent>
            </Card>
        </AuthLayout>
    );
}
