import { useEffect, useState } from 'react';

export interface CountdownBreakdown {
    remainingSeconds: number;
    days: number;
    hours: number;
    minutes: number;
    seconds: number;
    isExpired: boolean;
    label: string;
}

function computeRemaining(targetIso: string | null | undefined, fallbackSeconds = 0): number {
    if (targetIso) {
        const targetMs = new Date(targetIso).getTime();
        if (!Number.isNaN(targetMs)) {
            return Math.max(0, Math.ceil((targetMs - Date.now()) / 1000));
        }
    }
    return Math.max(0, Math.floor(fallbackSeconds));
}

export function formatCountdownBreakdown(totalSeconds: number): CountdownBreakdown {
    const safe = Math.max(0, Math.floor(totalSeconds));
    const days = Math.floor(safe / 86400);
    const hours = Math.floor((safe % 86400) / 3600);
    const minutes = Math.floor((safe % 3600) / 60);
    const seconds = safe % 60;

    const parts: string[] = [];
    if (days > 0) {
        parts.push(`${days} hari`);
    }
    parts.push(`${String(hours).padStart(2, '0')} jam`);
    parts.push(`${String(minutes).padStart(2, '0')} menit`);
    parts.push(`${String(seconds).padStart(2, '0')} detik`);

    return {
        remainingSeconds: safe,
        days,
        hours,
        minutes,
        seconds,
        isExpired: safe <= 0,
        label: parts.join(' '),
    };
}

export function useCountdown(
    targetIso: string | null | undefined,
    initialSeconds = 0,
    onExpire?: () => void,
): CountdownBreakdown {
    const [secondsLeft, setSecondsLeft] = useState<number>(() =>
        computeRemaining(targetIso, initialSeconds),
    );

    useEffect(() => {
        const start = computeRemaining(targetIso, initialSeconds);
        setSecondsLeft(start);

        if (start <= 0) {
            return undefined;
        }

        let fired = false;
        const id = window.setInterval(() => {
            const next = computeRemaining(targetIso, 0);
            setSecondsLeft(next);
            if (next <= 0 && !fired) {
                fired = true;
                window.clearInterval(id);
                onExpire?.();
            }
        }, 1000);

        return () => window.clearInterval(id);
    }, [targetIso, initialSeconds, onExpire]);

    return formatCountdownBreakdown(secondsLeft);
}
