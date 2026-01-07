'use client';

import * as React from 'react';
import { motion, useSpring, useScroll, useTransform, HTMLMotionProps, SpringOptions } from 'framer-motion';
import { cn } from '@/lib/utils';

export type ScrollProgressDirection = 'horizontal' | 'vertical';
export type ScrollProgressMode = 'width' | 'height' | 'scaleX' | 'scaleY';

interface ScrollProgressContextValue {
    scrollYProgress: ReturnType<typeof useSpring>;
    direction: ScrollProgressDirection;
    global: boolean;
    containerRef: React.RefObject<HTMLDivElement | null>;
}

const ScrollProgressContext = React.createContext<ScrollProgressContextValue | null>(null);

function useScrollProgressContext() {
    const context = React.useContext(ScrollProgressContext);
    if (!context) {
        throw new Error('ScrollProgress components must be used within a ScrollProgressProvider');
    }
    return context;
}

// Provider Props
interface ScrollProgressProviderProps {
    children: React.ReactNode;
    global?: boolean;
    direction?: ScrollProgressDirection;
    transition?: SpringOptions;
}

// ScrollProgressProvider
export function ScrollProgressProvider({
    children,
    global = false,
    direction = 'vertical',
    transition = { stiffness: 250, damping: 40, bounce: 0 }
}: ScrollProgressProviderProps) {
    const containerRef = React.useRef<HTMLDivElement>(null);

    const { scrollYProgress: rawScrollYProgress, scrollXProgress: rawScrollXProgress } = useScroll(
        global
            ? undefined
            : {
                container: containerRef as React.RefObject<HTMLElement>
            }
    );

    const progress = direction === 'vertical' ? rawScrollYProgress : rawScrollXProgress;
    const scrollYProgress = useSpring(progress, transition);

    return (
        <ScrollProgressContext.Provider
            value={{
                scrollYProgress,
                direction,
                global,
                containerRef
            }}
        >
            {children}
        </ScrollProgressContext.Provider>
    );
}

// ScrollProgress Props
interface ScrollProgressProps extends Omit<HTMLMotionProps<'div'>, 'style'> {
    asChild?: boolean;
    mode?: ScrollProgressMode;
    style?: React.CSSProperties;
}

// ScrollProgress
export const ScrollProgress = React.forwardRef<HTMLDivElement, ScrollProgressProps>(
    ({ className, mode = 'width', style, ...props }, ref) => {
        const { scrollYProgress, direction, global } = useScrollProgressContext();

        const scaleX = useTransform(scrollYProgress, [0, 1], [0, 1]);
        const scaleY = useTransform(scrollYProgress, [0, 1], [0, 1]);
        const width = useTransform(scrollYProgress, [0, 1], ['0%', '100%']);
        const height = useTransform(scrollYProgress, [0, 1], ['0%', '100%']);

        const getStyleByMode = () => {
            switch (mode) {
                case 'scaleX':
                    return { scaleX, transformOrigin: 'left' };
                case 'scaleY':
                    return { scaleY, transformOrigin: 'top' };
                case 'height':
                    return { height };
                case 'width':
                default:
                    return { width };
            }
        };

        return (
            <motion.div
                ref={ref}
                className={cn('bg-primary rounded-full', className)}
                style={{
                    ...getStyleByMode(),
                    ...style
                }}
                data-global={global}
                data-direction={direction}
                {...props}
            />
        );
    }
);

ScrollProgress.displayName = 'ScrollProgress';

// ScrollProgressContainer Props
interface ScrollProgressContainerProps extends Omit<HTMLMotionProps<'div'>, 'ref'> {
    asChild?: boolean;
}

// ScrollProgressContainer
export const ScrollProgressContainer = React.forwardRef<HTMLDivElement, ScrollProgressContainerProps>(
    ({ className, children, ...props }, forwardedRef) => {
        const { containerRef, direction } = useScrollProgressContext();

        // Merge refs
        const mergedRef = React.useCallback(
            (node: HTMLDivElement | null) => {
                (containerRef as React.MutableRefObject<HTMLDivElement | null>).current = node;
                if (typeof forwardedRef === 'function') {
                    forwardedRef(node);
                } else if (forwardedRef) {
                    forwardedRef.current = node;
                }
            },
            [containerRef, forwardedRef]
        );

        return (
            <motion.div
                ref={mergedRef}
                className={cn('overflow-auto', className)}
                data-direction={direction}
                {...props}
            >
                {children}
            </motion.div>
        );
    }
);

ScrollProgressContainer.displayName = 'ScrollProgressContainer';
