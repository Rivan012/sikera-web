import { type ClassValue, clsx } from 'clsx';
import { extendTailwindMerge } from 'tailwind-merge';

type AdditionalClassGroupIds =
    | 'menu-item-state'
    | 'menu-item-icon-state'
    | 'menu-item-arrow-state'
    | 'menu-dropdown-item-state'
    | 'menu-dropdown-badge-state';

const customTwMerge = extendTailwindMerge<AdditionalClassGroupIds>({
    extend: {
        classGroups: {
            'font-size': [
                'text-heading-one',
                'text-heading-two',
                'text-heading-three',
                'text-heading-four',
                'text-heading-five',
                'text-heading-six',
                'text-title-sm',
                'text-title-md',
                'text-title-lg',
                'text-title-xl',
                'text-title-2xl',
                'text-theme-xs',
                'text-theme-sm',
                'text-theme-xl',
            ],
            shadow: [
                'shadow-theme-xs',
                'shadow-theme-sm',
                'shadow-theme-md',
                'shadow-theme-lg',
                'shadow-theme-xl',
                'shadow-datepicker',
                'shadow-focus-ring',
                'shadow-slider-navigation',
                'shadow-tooltip',
            ],
            'menu-item-state': ['menu-item-active', 'menu-item-inactive'],
            'menu-item-icon-state': ['menu-item-icon-active', 'menu-item-icon-inactive'],
            'menu-item-arrow-state': ['menu-item-arrow-active', 'menu-item-arrow-inactive'],
            'menu-dropdown-item-state': ['menu-dropdown-item-active', 'menu-dropdown-item-inactive'],
            'menu-dropdown-badge-state': ['menu-dropdown-badge-active', 'menu-dropdown-badge-inactive'],
        },
    },
});

export function cn(...inputs: ClassValue[]) {
    return customTwMerge(clsx(...inputs));
}
