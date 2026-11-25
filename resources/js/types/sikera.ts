import type { PageProps as InertiaPageProps } from '@inertiajs/core';

export type UserRole = 'mahasiswa' | 'dosen_pa' | 'admin';

export interface AuthUser {
    id: number;
    name: string;
    initials: string | null;
    email: string;
    nim: string | null;
    role: UserRole;
    gender: 'P' | 'L' | null;
    usia: number | null;
    prodi: string | null;
    fakultas: string | null;
    agama?: string | null;
    pendidikan_terakhir?: string | null;
    menarche_age?: number | null;
    avatar: string | null;
    points: number;
    streak_days: number;
    is_biodata_filled: boolean;
    pretest_completed: boolean;
    posttest_completed: boolean;
    google_linked: boolean;
    all_modules_completed_at?: string | null;
}

export interface FlashProps {
    success?: string | null;
    info?: string | null;
    warning?: string | null;
    error?: string | null;
    trivia_result?: {
        is_correct: boolean;
        correct_answer: string;
        explanation: string;
        points_earned: number;
    } | null;
    myth_result?: {
        is_correct: boolean;
        actual_is_fact: boolean;
        explanation: string;
        points_earned: number;
    } | null;
}

export type PosttestGateState = 'locked_incomplete' | 'waiting_delay' | 'ready' | 'completed';

export interface LatestPosttestSummary {
    total_score: number;
    pre_score: number;
    n_gain_score: number | null;
    effectiveness_category: string;
    submitted_at: string | null;
}

export interface PosttestGate {
    state: PosttestGateState;
    total_modules: number;
    completed_modules: number;
    total_topics: number;
    completed_topics: number;
    all_modules_completed: boolean;
    all_modules_completed_at: string | null;
    posttest_available_at: string | null;
    is_posttest_unlocked: boolean;
    remaining_seconds: number;
    delay_days: number;
    has_completed_posttest: boolean;
    latest_posttest: LatestPosttestSummary | null;
}

export interface SharedProps extends InertiaPageProps {
    auth: {
        user: AuthUser | null;
    };
    flash: FlashProps;
    posttestGate: PosttestGate | null;
    errors: Record<string, string>;
}

export interface QuestionOption {
    label: 'A' | 'B' | 'C' | 'D' | string;
    text: string;
}

export interface TestQuestionItem {
    id: number;
    question_type: 'pre_test' | 'post_test';
    module_target: number | null;
    question_text: string;
    options: QuestionOption[];
    correct_answer?: string;
    explanation?: string | null;
    created_at?: string;
    updated_at?: string;
}

export interface ModuleTopicItem {
    id: number;
    educational_module_id: number;
    topic_code: string;
    title: string;
    content_html?: string | null;
    pdf_path?: string | null;
    pdf_original_name?: string | null;
    pdf_size?: number | null;
    youtube_video_id?: string | null;
    order_index: number;
}

export interface EducationalModuleItem {
    id: number;
    module_number: number;
    title: string;
    subtitle?: string | null;
    description?: string | null;
    banner_image?: string | null;
    estimated_time?: string | number | null;
    topics_count?: number;
    topics?: ModuleTopicItem[];
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginationLink[];
}
