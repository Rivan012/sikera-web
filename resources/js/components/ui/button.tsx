import { cn } from '@/utils';
import { Button as ButtonPrimitive } from '@base-ui/react/button';
import { cva, type VariantProps } from 'class-variance-authority';

const buttonVariants = cva(
    "group/button inline-flex shrink-0 items-center justify-center rounded-lg border border-transparent bg-clip-padding text-sm font-semibold whitespace-nowrap transition-colors duration-150 outline-none select-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:focus-visible:outline-teal-400 disabled:pointer-events-none disabled:opacity-55 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4",
    {
        variants: {
            variant: {
                default:
                    'bg-teal-700 text-white shadow-theme-xs hover:bg-teal-800 dark:bg-teal-400 dark:text-slate-950 dark:hover:bg-teal-300',
                pine:
                    'bg-pine-700 text-white shadow-theme-xs hover:bg-pine-800 dark:bg-pine-400 dark:text-pine-950 dark:hover:bg-pine-300',
                outline:
                    'border-slate-300 bg-white text-slate-800 shadow-theme-xs hover:bg-slate-100 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700',
                ghost:
                    'border-transparent bg-transparent text-slate-700 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 dark:hover:text-white',
                destructive:
                    'bg-rose-700 text-white shadow-theme-xs hover:bg-rose-800 dark:bg-rose-400 dark:text-slate-950 dark:hover:bg-rose-300',
                link: 'h-auto! min-h-0! p-0! font-semibold text-teal-700 underline-offset-4 hover:underline dark:text-teal-300',
            },
            size: {
                default: 'min-h-11 gap-2 px-4 py-2.5',
                xs: 'min-h-11 gap-1.5 px-3 py-2 text-xs',
                sm: 'min-h-11 gap-1.5 px-3.5 py-2 text-xs sm:text-sm',
                lg: 'min-h-12 gap-2.5 px-5 py-3 text-base',
                icon: 'size-11',
                'icon-sm': 'size-11',
                'icon-lg': 'size-11',
            },
        },
        defaultVariants: {
            variant: 'default',
            size: 'default',
        },
    },
);

function Button({
    className,
    variant = 'default',
    size = 'default',
    ...props
}: ButtonPrimitive.Props & VariantProps<typeof buttonVariants>) {
    return (
        <ButtonPrimitive
            data-slot="button"
            className={cn(buttonVariants({ variant, size, className }))}
            {...props}
        />
    );
}

export { Button, buttonVariants };
