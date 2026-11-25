import { Link, router, usePage } from '@inertiajs/react';
import { useEffect, useState, type ReactNode } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown';
import { Sheet, SheetContent, SheetTitle } from '@/components/ui/sheet';
import { useTheme } from '@/contexts/theme-context';
import { useIsMobile } from '@/hooks/use-mobile';
import {
    BarChartIcon,
    BookOpenIcon,
    ChevronDownIcon,
    ClipboardCheckIcon,
    ClipboardListIcon,
    FileTextIcon,
    GraduationCapIcon,
    GridIcon,
    HeartPulseIcon,
    ImageIcon,
    LogOutIcon,
    MenuTwoIcon,
    MessageSquareIcon,
    MoonIcon,
    SparklesIcon,
    SunIcon,
    TableIcon,
    UserCircleIcon,
    UsersIcon,
    XIcon,
} from '@/icons';
import type { AuthUser, PosttestGate, SharedProps } from '@/types/sikera';
import { cn } from '@/utils';

interface NavItem {
    key: string;
    label: string;
    href: string;
    icon: ReactNode;
    match: string[];
    badge?: ReactNode;
}

interface NavGroup {
    key: string;
    label: string;
    items: NavItem[];
}

const ROLE_LABEL: Record<AuthUser['role'], string> = {
    admin: 'Pengelola Riset',
    dosen_pa: 'Dosen Pembimbing Akademik',
    mahasiswa: 'Mahasiswa Responden',
};

function posttestBadge(gate: PosttestGate | null): ReactNode {
    if (!gate) {
        return null;
    }
    switch (gate.state) {
        case 'completed':
            return <Badge color="success" size="sm">Selesai</Badge>;
        case 'ready':
            return <Badge color="primary" variant="solid" size="sm">Siap</Badge>;
        case 'waiting_delay':
            return <Badge color="warning" size="sm">Jeda 3 hari</Badge>;
        default:
            return <Badge color="gray" size="sm">Terkunci</Badge>;
    }
}

function buildNavigation(user: AuthUser, gate: PosttestGate | null): NavGroup[] {
    const groups: NavGroup[] = [];

    if (user.role === 'admin') {
        groups.push({
            key: 'admin',
            label: 'Pengelola Riset',
            items: [
                { key: 'admin-dashboard', label: 'Dashboard Grafik', href: '/admin/dashboard', icon: <BarChartIcon />, match: ['/admin/dashboard'] },
                { key: 'admin-users', label: 'Daftar Pengguna', href: '/admin/users', icon: <UsersIcon />, match: ['/admin/users'] },
                { key: 'admin-modules', label: 'Kelola Modul', href: '/admin/modules', icon: <BookOpenIcon />, match: ['/admin/modules', '/admin/topics'] },
                { key: 'admin-pretest', label: 'Kelola Pre-Test', href: '/admin/pretest-questions', icon: <ClipboardListIcon />, match: ['/admin/pretest-questions'] },
                { key: 'admin-posttest', label: 'Kelola Post-Test', href: '/admin/posttest-questions', icon: <ClipboardCheckIcon />, match: ['/admin/posttest-questions'] },
                { key: 'admin-evaluations', label: 'Jawaban Evaluasi', href: '/admin/evaluations', icon: <FileTextIcon />, match: ['/admin/evaluations'] },
                { key: 'admin-sheets', label: 'Lembar Data', href: '/admin/sheets', icon: <TableIcon />, match: ['/admin/sheets'] },
                { key: 'admin-posters', label: 'Unggah Poster', href: '/admin/posters', icon: <ImageIcon />, match: ['/admin/posters'] },
            ],
        });
    } else if (user.role === 'dosen_pa') {
        groups.push({
            key: 'dosen',
            label: 'Ruang Kerja',
            items: [
                { key: 'dosen-dashboard', label: 'Dashboard Dosen PA', href: '/dosen/dashboard', icon: <GraduationCapIcon />, match: ['/dosen'] },
            ],
        });
    } else {
        groups.push({
            key: 'mahasiswa',
            label: 'Ruang Kerja',
            items: [
                { key: 'mhs-dashboard', label: 'Menu Utama', href: '/mahasiswa/dashboard', icon: <GridIcon />, match: ['/mahasiswa'] },
                {
                    key: 'mhs-posttest',
                    label: 'Post-Test Akhir',
                    href: '/posttest',
                    icon: <ClipboardCheckIcon />,
                    match: ['/posttest'],
                    badge: posttestBadge(gate),
                },
            ],
        });
    }

    const education: NavItem[] = [
        { key: 'modules', label: 'Modul Edukasi', href: '/modules', icon: <BookOpenIcon />, match: ['/modules'] },
        { key: 'posters', label: 'Visual Poster Kaspro', href: '/posters', icon: <ImageIcon />, match: ['/posters'] },
    ];

    if (user.role === 'mahasiswa') {
        education.push({
            key: 'selfcare',
            label: user.gender === 'P' ? 'Kalender Haid & IMT' : 'Kalkulator IMT & Gizi',
            href: '/selfcare',
            icon: <HeartPulseIcon />,
            match: ['/selfcare'],
        });
    }

    education.push(
        { key: 'cases', label: 'Studi Kasus Kampus', href: '/cases', icon: <FileTextIcon />, match: ['/cases'] },
        { key: 'forum', label: 'Forum Konsultasi', href: '/forum', icon: <MessageSquareIcon />, match: ['/forum'] },
    );

    groups.push({ key: 'education', label: 'Edukasi & Layanan', items: education });

    if (user.role === 'mahasiswa') {
        groups.push({
            key: 'daily',
            label: 'Aktivitas Harian',
            items: [
                { key: 'trivia', label: 'Trivia Harian', href: '/trivia', icon: <SparklesIcon />, match: ['/trivia'] },
                { key: 'myth', label: 'Mitos vs Fakta', href: '/myth-fact', icon: <SparklesIcon />, match: ['/myth-fact'] },
                { key: 'diary', label: 'Catatan Mood', href: '/diary', icon: <FileTextIcon />, match: ['/diary'] },
            ],
        });
    }

    return groups;
}

function isActive(url: string, item: NavItem): boolean {
    const path = url.split('?')[0];
    return item.match.some((prefix) => path === prefix || path.startsWith(`${prefix}/`));
}

function UserAvatar({ user, size = 36 }: { user: AuthUser; size?: number }) {
    const initial = (user.initials || user.name || '?').slice(0, 2).toUpperCase();

    return (
        <span
            className="flex shrink-0 items-center justify-center overflow-hidden rounded-lg border border-teal-200 bg-teal-50 text-xs font-bold text-teal-800 dark:border-teal-800 dark:bg-teal-950 dark:text-teal-200"
            style={{ width: size, height: size }}
        >
            {user.avatar ? (
                <img
                    src={user.avatar}
                    alt=""
                    width={size}
                    height={size}
                    referrerPolicy="no-referrer"
                    className="h-full w-full object-cover"
                />
            ) : (
                <span aria-hidden="true">{initial}</span>
            )}
        </span>
    );
}

function SidebarBrand({ expanded }: { expanded: boolean }) {
    return (
        <Link
            href="/dashboard"
            aria-label="Dasbor SIKERA"
            className="inline-flex min-h-11 items-center gap-3 rounded-lg px-1.5 py-1 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:focus-visible:outline-teal-400"
        >
            <img src="/images/sikera-logo.webp" alt="" width={40} height={40} className="shrink-0" />
            {expanded && (
                <span className="flex flex-col">
                    <span className="text-base leading-tight font-bold tracking-tight text-slate-900 dark:text-white">SIKERA</span>
                    <span className="text-[11px] font-medium text-slate-600 dark:text-slate-300">Sistem Informasi Kesehatan Remaja</span>
                </span>
            )}
        </Link>
    );
}

function SidebarNav({
    groups,
    url,
    expanded,
    onNavigate,
}: {
    groups: NavGroup[];
    url: string;
    expanded: boolean;
    onNavigate?: () => void;
}) {
    return (
        <nav aria-label="Menu navigasi sistem" className="flex flex-col gap-6">
            {groups.map((group) => (
                <div key={group.key}>
                    <h2
                        className={cn(
                            'mb-2 px-3 text-xs font-semibold text-slate-600 dark:text-slate-300',
                            !expanded && 'sr-only',
                        )}
                    >
                        {group.label}
                    </h2>
                    <ul className="flex flex-col gap-1">
                        {group.items.map((item) => {
                            const active = isActive(url, item);
                            return (
                                <li key={item.key}>
                                    <Link
                                        href={item.href}
                                        onClick={onNavigate}
                                        aria-current={active ? 'page' : undefined}
                                        title={!expanded ? item.label : undefined}
                                        className={cn(
                                            'group menu-item',
                                            active ? 'menu-item-active' : 'menu-item-inactive',
                                            !expanded && 'justify-center px-0',
                                        )}
                                    >
                                        <span className={cn(active ? 'menu-item-icon-active' : 'menu-item-icon-inactive')}>
                                            {item.icon}
                                        </span>
                                        {expanded ? (
                                            <>
                                                <span className="flex-1 truncate">{item.label}</span>
                                                {item.badge}
                                            </>
                                        ) : (
                                            <span className="sr-only">{item.label}</span>
                                        )}
                                    </Link>
                                </li>
                            );
                        })}
                    </ul>
                </div>
            ))}
        </nav>
    );
}

function ThemeToggleButton() {
    const { theme, toggleTheme } = useTheme();
    const isDark = theme === 'dark';

    return (
        <Button
            variant="outline"
            size="sm"
            onClick={toggleTheme}
            aria-pressed={isDark}
            aria-label={isDark ? 'Beralih ke mode terang' : 'Beralih ke mode gelap'}
        >
            {isDark ? <SunIcon className="size-4 text-amber-300" /> : <MoonIcon className="size-4" />}
            <span className="hidden sm:inline">{isDark ? 'Mode Terang' : 'Mode Gelap'}</span>
        </Button>
    );
}

function UserMenu({ user }: { user: AuthUser }) {
    const [open, setOpen] = useState(false);

    return (
        <DropdownMenu open={open} onOpenChange={setOpen}>
            <DropdownMenuTrigger
                aria-label="Buka menu akun"
                className="flex min-h-11 items-center gap-2.5 rounded-lg px-1.5 text-slate-800 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:text-slate-100 dark:hover:bg-slate-800 dark:focus-visible:outline-teal-400"
            >
                <UserAvatar user={user} />
                <span className="hidden text-left md:block">
                    <span className="block max-w-40 truncate text-xs font-semibold">{user.name}</span>
                    <span className="block text-[11px] font-medium text-slate-600 dark:text-slate-300">{ROLE_LABEL[user.role]}</span>
                </span>
                <ChevronDownIcon className={cn('hidden size-4 text-slate-600 md:block dark:text-slate-300', open && 'rotate-180')} />
            </DropdownMenuTrigger>
            <DropdownMenuContent>
                <div className="px-3 py-2">
                    <p className="truncate text-sm font-semibold text-slate-900 dark:text-white">{user.name}</p>
                    <p className="truncate text-xs text-slate-600 dark:text-slate-300">{user.email}</p>
                </div>
                <DropdownMenuSeparator />
                <DropdownMenuItem onClick={() => router.visit('/profil')}>
                    <UserCircleIcon />
                    Edit Profil
                </DropdownMenuItem>
                <DropdownMenuItem variant="destructive" onClick={() => router.post('/logout')}>
                    <LogOutIcon />
                    Keluar
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}

type FlashTone = 'success' | 'info' | 'warning' | 'error';

const FLASH_STYLES: Record<FlashTone, string> = {
    success: 'border-teal-300 bg-teal-50 text-teal-950 dark:border-teal-700 dark:bg-teal-950/80 dark:text-teal-100',
    info: 'border-slate-300 bg-white text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100',
    warning: 'border-amber-300 bg-amber-50 text-amber-950 dark:border-amber-700 dark:bg-amber-950/80 dark:text-amber-100',
    error: 'border-rose-300 bg-rose-50 text-rose-950 dark:border-rose-700 dark:bg-rose-950/80 dark:text-rose-100',
};

const FLASH_LABEL: Record<FlashTone, string> = {
    success: 'Tutup pemberitahuan berhasil',
    info: 'Tutup informasi',
    warning: 'Tutup peringatan',
    error: 'Tutup pesan galat',
};

export function FlashMessages() {
    const { flash } = usePage<SharedProps>().props;
    const [dismissed, setDismissed] = useState<Record<string, boolean>>({});

    useEffect(() => {
        setDismissed({});
    }, [flash?.success, flash?.info, flash?.warning, flash?.error]);

    const entries = (['success', 'info', 'warning', 'error'] as FlashTone[])
        .map((tone) => ({ tone, message: flash?.[tone] }))
        .filter((entry): entry is { tone: FlashTone; message: string } => Boolean(entry.message) && !dismissed[entry.tone]);

    if (!entries.length) {
        return null;
    }

    return (
        <div className="mb-5 flex flex-col gap-3">
            {entries.map(({ tone, message }) => (
                <div
                    key={tone}
                    role={tone === 'error' || tone === 'warning' ? 'alert' : 'status'}
                    className={cn('flex items-center justify-between gap-3 rounded-xl border px-4 py-2 text-sm font-medium', FLASH_STYLES[tone])}
                >
                    <span>{message}</span>
                    <button
                        type="button"
                        onClick={() => setDismissed((prev) => ({ ...prev, [tone]: true }))}
                        aria-label={FLASH_LABEL[tone]}
                        className="inline-flex min-h-11 min-w-11 shrink-0 items-center justify-center rounded-lg hover:bg-black/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:hover:bg-white/10 dark:focus-visible:outline-teal-400"
                    >
                        <XIcon className="size-4" />
                    </button>
                </div>
            ))}
        </div>
    );
}

interface AppLayoutProps {
    title: string;
    children: ReactNode;
    actions?: ReactNode;
}

export default function AppLayout({ title, children, actions }: AppLayoutProps) {
    const page = usePage<SharedProps>();
    const user = page.props.auth.user;
    const gate = page.props.posttestGate;
    const url = page.url;
    const isMobile = useIsMobile();
    const [desktopExpanded, setDesktopExpanded] = useState(true);
    const [mobileOpen, setMobileOpen] = useState(false);

    useEffect(() => {
        setMobileOpen(false);
    }, [url]);

    if (!user) {
        return <main className="mx-auto max-w-5xl p-6">{children}</main>;
    }

    const groups = buildNavigation(user, gate);

    const toggleSidebar = () => {
        if (isMobile) {
            setMobileOpen((open) => !open);
        } else {
            setDesktopExpanded((open) => !open);
        }
    };

    return (
        <div className="flex min-h-svh w-full bg-slate-50 dark:bg-slate-900">
            <aside
                aria-label="Navigasi samping"
                className={cn(
                    'sticky top-0 hidden h-svh shrink-0 flex-col border-r border-slate-200 bg-white transition-[width] duration-200 xl:flex dark:border-slate-800 dark:bg-slate-900',
                    desktopExpanded ? 'w-72.5' : 'w-22.5',
                )}
            >
                <div className={cn('flex items-center border-b border-slate-200 px-4 pt-5 pb-4 dark:border-slate-800', !desktopExpanded && 'justify-center px-2')}>
                    <SidebarBrand expanded={desktopExpanded} />
                </div>
                <div className={cn('no-scrollbar flex flex-1 flex-col overflow-y-auto py-5', desktopExpanded ? 'px-4' : 'px-3')}>
                    <SidebarNav groups={groups} url={url} expanded={desktopExpanded} />
                </div>
            </aside>

            <Sheet open={isMobile && mobileOpen} onOpenChange={setMobileOpen}>
                <SheetContent side="left" className="xl:hidden">
                    <div className="flex items-center justify-between gap-2 border-b border-slate-200 px-4 pt-5 pb-4 dark:border-slate-800">
                        <SheetTitle className="sr-only">Menu navigasi SIKERA</SheetTitle>
                        <SidebarBrand expanded />
                        <Button variant="ghost" size="icon" onClick={() => setMobileOpen(false)} aria-label="Tutup menu navigasi">
                            <XIcon />
                        </Button>
                    </div>
                    <div className="no-scrollbar flex flex-1 flex-col overflow-y-auto px-4 py-5">
                        <SidebarNav groups={groups} url={url} expanded onNavigate={() => setMobileOpen(false)} />
                    </div>
                </SheetContent>
            </Sheet>

            <div className="flex min-w-0 flex-1 flex-col">
                <header className="sticky top-0 z-50 border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
                    <div className="flex w-full items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
                        <div className="flex min-w-0 items-center gap-3">
                            <Button
                                variant="outline"
                                size="icon"
                                onClick={toggleSidebar}
                                aria-expanded={isMobile ? mobileOpen : desktopExpanded}
                                aria-label="Buka atau tutup menu navigasi samping"
                            >
                                <MenuTwoIcon />
                            </Button>
                            <h1 className="truncate text-sm font-bold tracking-tight text-slate-900 sm:text-base lg:text-lg dark:text-white">
                                {title}
                            </h1>
                        </div>
                        <div className="flex shrink-0 items-center gap-2 sm:gap-3">
                            <ThemeToggleButton />
                            <UserMenu user={user} />
                        </div>
                    </div>
                </header>

                <main className="flex-1">
                    <div className="mx-auto w-full max-w-(--breakpoint-2xl) p-4 sm:p-6 lg:p-8">
                        <FlashMessages />
                        {actions && <div className="mb-5 flex flex-wrap items-center justify-end gap-2">{actions}</div>}
                        {children}
                    </div>
                </main>
            </div>
        </div>
    );
}

export { ThemeToggleButton, UserAvatar };
