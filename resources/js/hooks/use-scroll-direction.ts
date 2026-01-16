import { useCallback, useEffect, useRef, useState } from 'react';

type ScrollDirection = 'up' | 'down' | null;

interface UseScrollDirectionOptions {
    threshold?: number;
    containerRef?: React.RefObject<HTMLElement | null>;
}

export function useScrollDirection({
    threshold = 10,
    containerRef,
}: UseScrollDirectionOptions = {}) {
    const [direction, setDirection] = useState<ScrollDirection>(null);
    const [isAtTop, setIsAtTop] = useState(true);
    const lastScrollY = useRef(0);
    const ticking = useRef(false);

    const updateScrollDirection = useCallback(() => {
        const target = containerRef?.current ?? window;
        const scrollY =
            target === window
                ? window.scrollY
                : (target as HTMLElement).scrollTop;

        const diff = scrollY - lastScrollY.current;

        setIsAtTop(scrollY < threshold);

        if (Math.abs(diff) < threshold) {
            ticking.current = false;
            return;
        }

        setDirection(diff > 0 ? 'down' : 'up');
        lastScrollY.current = scrollY;
        ticking.current = false;
    }, [threshold, containerRef]);

    const handleScroll = useCallback(() => {
        if (!ticking.current) {
            window.requestAnimationFrame(updateScrollDirection);
            ticking.current = true;
        }
    }, [updateScrollDirection]);

    useEffect(() => {
        const target = containerRef?.current ?? window;
        target.addEventListener('scroll', handleScroll, { passive: true });
        return () => target.removeEventListener('scroll', handleScroll);
    }, [handleScroll, containerRef]);

    return { direction, isAtTop };
}
