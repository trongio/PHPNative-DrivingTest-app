import { Link } from '@inertiajs/react';
import { BookOpen, ExternalLink } from 'lucide-react';
import { useCallback, useEffect, useRef, useState } from 'react';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import { Skeleton } from '@/components/ui/skeleton';

interface Sign {
    id: number;
    image: string;
    title: string;
    title_en: string | null;
    description: string | null;
}

interface SignCategory {
    id: number;
    name: string;
    group_number: number;
}

interface SignDetailSheetProps {
    sign: Sign | null;
    category: SignCategory | null;
    open: boolean;
    onOpenChange: (open: boolean) => void;
}

export function SignDetailSheet({
    sign,
    category,
    open,
    onOpenChange,
}: SignDetailSheetProps) {
    const [relatedQuestionsCount, setRelatedQuestionsCount] = useState<
        number | null
    >(null);
    const [isLoading, setIsLoading] = useState(false);
    const abortControllerRef = useRef<AbortController | null>(null);

    // Handle Android back button to close sheet instead of navigating
    useEffect(() => {
        const handlePopState = (e: PopStateEvent) => {
            if (open) {
                e.preventDefault();
                onOpenChange(false);
                window.history.pushState(null, '', window.location.href);
            }
        };

        if (open) {
            window.history.pushState(null, '', window.location.href);
        }

        window.addEventListener('popstate', handlePopState);
        return () => window.removeEventListener('popstate', handlePopState);
    }, [open, onOpenChange]);

    // Fetch related questions count
    const fetchRelatedQuestions = useCallback(async (signId: number) => {
        abortControllerRef.current?.abort();
        abortControllerRef.current = new AbortController();

        setIsLoading(true);
        try {
            const res = await fetch(`/signs/${signId}`, {
                signal: abortControllerRef.current.signal,
            });
            const data = await res.json();
            setRelatedQuestionsCount(data.related_questions_count);
        } catch (error) {
            if (error instanceof Error && error.name !== 'AbortError') {
                console.error(error);
            }
        } finally {
            setIsLoading(false);
        }
    }, []);

    // Trigger fetch when sign changes and sheet is open
    useEffect(() => {
        if (sign && open) {
            fetchRelatedQuestions(sign.id);
        } else {
            setRelatedQuestionsCount(null);
        }

        return () => {
            abortControllerRef.current?.abort();
        };
    }, [sign, open, fetchRelatedQuestions]);

    if (!sign || !category) return null;

    return (
        <Sheet open={open} onOpenChange={onOpenChange}>
            <SheetContent side="bottom" className="max-h-[70vh] rounded-t-2xl">
                <SheetHeader className="text-left">
                    <div className="flex items-start gap-4">
                        <div className="h-24 w-24 shrink-0 overflow-hidden rounded-lg border bg-muted/50 p-2">
                            <img
                                src={`/images/signs/${sign.image}`}
                                alt={sign.title}
                                className="h-full w-full object-contain"
                            />
                        </div>
                        <div className="flex-1 space-y-2">
                            <Badge variant="secondary" className="text-xs">
                                {category.name}
                            </Badge>
                            <SheetTitle className="text-lg leading-tight">
                                {sign.title}
                            </SheetTitle>
                            {sign.title_en && (
                                <SheetDescription className="text-sm">
                                    {sign.title_en}
                                </SheetDescription>
                            )}
                        </div>
                    </div>
                </SheetHeader>

                <div className="mt-4 space-y-4 overflow-y-auto">
                    {sign.description && (
                        <div className="rounded-lg bg-muted/50 p-3">
                            <p className="text-sm text-muted-foreground">
                                {sign.description}
                            </p>
                        </div>
                    )}

                    {isLoading ? (
                        <Skeleton className="h-10 w-full" />
                    ) : relatedQuestionsCount !== null &&
                      relatedQuestionsCount > 0 ? (
                        <Button asChild variant="outline" className="w-full">
                            <Link
                                href={`/questions?sign_id=${sign.id}`}
                                className="flex items-center gap-2"
                            >
                                <BookOpen className="h-4 w-4" />
                                <span>
                                    დაკავშირებული კითხვები (
                                    {relatedQuestionsCount})
                                </span>
                                <ExternalLink className="ml-auto h-4 w-4" />
                            </Link>
                        </Button>
                    ) : null}
                </div>
            </SheetContent>
        </Sheet>
    );
}
