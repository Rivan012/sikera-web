<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('nim'))) {
            $this->merge(['nim' => strtoupper($this->input('nim'))]);
        }
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $user = $this->user();
        $googleLinked = $user->google_id !== null;
        $biodata = $user->isMahasiswa() ? 'required' : 'nullable';
        $emailChange = ! $googleLinked && $this->filled('email') && $this->input('email') !== $user->email;

        return [
            'name' => ['required', 'string', 'max:255'],
            'initials' => ['nullable', 'string', 'max:50'],
            'email' => ['bail', 'required', 'email', 'max:255', $googleLinked ? Rule::in([$user->email]) : Rule::unique('users')->ignore($user)],
            'current_password' => $emailChange ? ['bail', 'required', 'string', 'current_password'] : ['exclude'],
            'avatar' => $googleLinked ? ['missing'] : ['nullable', 'string', 'url:https', 'max:2048'],
            'nim' => ['bail', 'nullable', 'string', 'max:50', Rule::unique('users')->ignore($user)],
            'usia' => [$biodata, 'integer', $user->isMahasiswa() ? 'between:15,40' : 'between:10,100'],
            'gender' => [$biodata, Rule::in(['L', 'P'])],
            'agama' => [$biodata, 'string', 'max:255'],
            'prodi' => [$biodata, 'string', 'max:255'],
            'fakultas' => ['nullable', 'string', 'max:255'],
            'pendidikan_terakhir' => [$biodata, 'string', 'max:255'],
            'menarche_age' => ['nullable', 'integer', 'between:8,25'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max.string' => ':attribute maksimal :max karakter.',
            'integer' => ':attribute harus berupa bilangan bulat.',
            'between.numeric' => ':attribute harus antara :min dan :max tahun.',
            'gender.in' => 'Pilih jenis kelamin yang tersedia.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'email.unique' => 'Email sudah digunakan akun lain.',
            'email.in' => 'Gunakan email yang terhubung ke akun Google Anda.',
            'nim.unique' => 'NIM sudah digunakan akun lain.',
            'avatar.url' => 'Masukkan tautan foto yang diawali https://.',
            'avatar.missing' => 'Foto akun ini mengikuti foto profil Google Anda.',
            'current_password.required' => 'Masukkan kata sandi saat ini untuk mengubah email.',
            'current_password.current_password' => 'Kata sandi saat ini salah.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'name' => 'Nama lengkap', 'initials' => 'Inisial', 'email' => 'Email',
            'nim' => 'NIM', 'usia' => 'Usia', 'gender' => 'Jenis kelamin',
            'prodi' => 'Program studi', 'agama' => 'Agama', 'fakultas' => 'Fakultas',
            'pendidikan_terakhir' => 'Pendidikan terakhir', 'menarche_age' => 'Usia saat haid pertama',
            'avatar' => 'Tautan foto', 'current_password' => 'Kata sandi saat ini',
        ];
    }
}
