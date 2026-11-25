import { cn } from '@/utils';

interface FieldErrorProps {
    message?: string | null;
    id?: string;
    className?: string;
}

export function FieldError({ message, id, className }: FieldErrorProps) {
    if (!message) {
        return null;
    }

    return (
        <p id={id} className={cn('text-xs font-medium text-rose-700 dark:text-rose-300', className)}>
            {message}
        </p>
    );
}
