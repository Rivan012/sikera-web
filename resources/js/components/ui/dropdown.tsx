import { cn } from '@/utils';
import { Menu as MenuPrimitive } from '@base-ui/react/menu';

function DropdownMenu({ ...props }: MenuPrimitive.Root.Props) {
    return <MenuPrimitive.Root data-slot="dropdown-menu" {...props} />;
}

function DropdownMenuTrigger({ ...props }: MenuPrimitive.Trigger.Props) {
    return <MenuPrimitive.Trigger data-slot="dropdown-menu-trigger" {...props} />;
}

function DropdownMenuContent({
    align = 'end',
    side = 'bottom',
    sideOffset = 8,
    className,
    ...props
}: MenuPrimitive.Popup.Props &
    Pick<MenuPrimitive.Positioner.Props, 'align' | 'side' | 'sideOffset'>) {
    return (
        <MenuPrimitive.Portal>
            <MenuPrimitive.Positioner
                className="isolate z-99999 outline-none"
                align={align}
                side={side}
                sideOffset={sideOffset}
            >
                <MenuPrimitive.Popup
                    data-slot="dropdown-menu-content"
                    className={cn(
                        'flex max-h-(--available-height) min-w-60 origin-(--transform-origin) flex-col gap-1 overflow-y-auto rounded-xl border border-slate-200 bg-white p-2 shadow-theme-lg outline-none duration-100 data-closed:animate-out data-closed:fade-out-0 data-open:animate-in data-open:fade-in-0 dark:border-slate-700 dark:bg-slate-900',
                        className,
                    )}
                    {...props}
                />
            </MenuPrimitive.Positioner>
        </MenuPrimitive.Portal>
    );
}

function DropdownMenuItem({
    className,
    variant = 'default',
    ...props
}: MenuPrimitive.Item.Props & { variant?: 'default' | 'destructive' }) {
    return (
        <MenuPrimitive.Item
            data-slot="dropdown-menu-item"
            data-variant={variant}
            className={cn(
                'relative flex min-h-11 cursor-pointer items-center gap-3 rounded-lg px-3 py-2 text-start text-sm font-medium text-slate-700 outline-hidden select-none data-highlighted:bg-slate-100 data-highlighted:text-slate-900 dark:text-slate-200 dark:data-highlighted:bg-slate-800 dark:data-highlighted:text-white data-[variant=destructive]:text-rose-700 data-[variant=destructive]:data-highlighted:bg-rose-50 dark:data-[variant=destructive]:text-rose-300 dark:data-[variant=destructive]:data-highlighted:bg-rose-950/60 [&_svg]:shrink-0',
                className,
            )}
            {...props}
        />
    );
}

function DropdownMenuSeparator({ className, ...props }: MenuPrimitive.Separator.Props) {
    return (
        <MenuPrimitive.Separator
            data-slot="dropdown-menu-separator"
            className={cn('-mx-2 my-1 h-px bg-slate-200 dark:bg-slate-700', className)}
            {...props}
        />
    );
}

export { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger };
