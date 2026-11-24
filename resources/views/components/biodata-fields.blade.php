@props(['user', 'mahasiswa'])

<fieldset class="border-t border-slate-200 dark:border-slate-700">
    <legend class="pr-3 text-lg font-bold">Biodata pribadi</legend>
    <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <x-profile-input name="usia" label="Usia (tahun)" type="number" :value="$user->usia" :required="$mahasiswa" :min="$mahasiswa ? 15 : 10" :max="$mahasiswa ? 40 : 100" />
        <x-profile-input name="gender" label="Jenis kelamin" :value="$user->gender" :required="$mahasiswa" :options="['P' => 'Perempuan', 'L' => 'Laki-laki']" />
        <x-profile-input name="agama" label="Agama" :value="$user->agama" :required="$mahasiswa" maxlength="255" list="agama-umum" />
        <datalist id="agama-umum">
            @foreach(['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu', 'Lainnya'] as $agama)
                <option value="{{ $agama }}"></option>
            @endforeach
        </datalist>
        <x-profile-input name="menarche_age" label="Usia saat haid pertama (tahun)" type="number" :value="$user->menarche_age" min="8" max="25" />
    </div>
</fieldset>

<fieldset class="border-t border-slate-200 dark:border-slate-700">
    <legend class="pr-3 text-lg font-bold">Data akademik</legend>
    <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <x-profile-input name="nim" label="Nomor induk mahasiswa (NIM)" :value="$user->nim" maxlength="50" />
        <x-profile-input name="fakultas" label="Fakultas" :value="$user->fakultas" maxlength="255" />
        <x-profile-input name="prodi" label="Program studi" :value="$user->prodi" :required="$mahasiswa" maxlength="255" />
        <x-profile-input name="pendidikan_terakhir" label="Pendidikan terakhir" :value="$user->pendidikan_terakhir" :required="$mahasiswa" maxlength="255" list="jenjang-pendidikan" />
        <datalist id="jenjang-pendidikan">
            @foreach(['SMA/SMK/MA/Sederajat', 'Diploma (D3/D4)', 'Sarjana (S1)'] as $jenjang)
                <option value="{{ $jenjang }}"></option>
            @endforeach
        </datalist>
    </div>
</fieldset>
