export interface FakultasGroup {
    kampus: 'UINFAS' | 'DEHASEN' | 'UNIB';
    options: { value: string; label: string }[];
}

const RAW_FAKULTAS: Record<'UINFAS' | 'DEHASEN' | 'UNIB', string[]> = {
    UINFAS: [
        'Fakultas Tarbiyah dan Tadris',
        'Fakultas Syariah',
        'Fakultas Ushuluddin, Adab dan Dakwah',
        'Fakultas Ekonomi dan Bisnis Islam',
    ],
    DEHASEN: [
        'Fakultas Pertanian',
        'Fakultas Ekonomi dan Bisnis',
        'Fakultas Ilmu Komputer',
        'Fakultas Ilmu-Ilmu Sosial',
        'Fakultas Keguruan dan Ilmu Pendidikan',
        'Fakultas Hukum',
        'Fakultas Ilmu Kesehatan',
    ],
    UNIB: [
        'Fakultas Keguruan dan Ilmu Pendidikan (FKIP)',
        'Fakultas Hukum (FH)',
        'Fakultas Ekonomi dan Bisnis (FEB)',
        'Fakultas Ilmu Sosial dan Ilmu Politik (FISIP)',
        'Fakultas Pertanian (FP)',
        'Fakultas Matematika dan Ilmu Pengetahuan Alam (FMIPA)',
        'Fakultas Teknik (FT)',
        'Fakultas Kedokteran dan Ilmu Kesehatan (FKIK)',
    ],
};

export const DAFTAR_FAKULTAS_PER_KAMPUS: FakultasGroup[] = (
    Object.entries(RAW_FAKULTAS) as [('UINFAS' | 'DEHASEN' | 'UNIB'), string[]][]
).map(([kampus, list]) => ({
    kampus,
    options: list.map((fakultas) => ({
        value: kampus === 'UNIB' ? fakultas : `${kampus} - ${fakultas}`,
        label: `${kampus} - ${fakultas}`,
    })),
}));

export const DAFTAR_AGAMA = [
    'Islam',
    'Kristen Protestan',
    'Katolik',
    'Hindu',
    'Buddha',
    'Khonghucu',
    'Lainnya',
] as const;

export const DAFTAR_JENJANG_PENDIDIKAN = [
    { value: 'SMA/SMK/MA/Sederajat', label: 'SMA / SMK / MA / Sederajat' },
    { value: 'Diploma (D3/D4)', label: 'Diploma (D3/D4)' },
    { value: 'Sarjana (S1)', label: 'Sarjana (S1)' },
] as const;

export function buildAutoInitials(name: string): string {
    const parts = name.trim().split(/\s+/).filter(Boolean);
    if (!parts.length) {
        return '';
    }
    return parts
        .map((p) => p.charAt(0).toUpperCase())
        .join('')
        .slice(0, 6);
}

export function formatBytes(bytes?: number | null): string {
    if (!bytes || bytes <= 0) {
        return '0 KB';
    }
    if (bytes < 1024 * 1024) {
        return `${Math.max(1, Math.round(bytes / 1024))} KB`;
    }
    return `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
}

export function formatDateId(iso?: string | null, withTime = false): string {
    if (!iso) {
        return '-';
    }
    const date = new Date(iso);
    if (Number.isNaN(date.getTime())) {
        return iso;
    }
    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        ...(withTime ? { hour: '2-digit', minute: '2-digit' } : {}),
    }).format(date);
}
