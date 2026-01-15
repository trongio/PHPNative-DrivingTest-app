import { Head, router } from '@inertiajs/react';
import {
    Bookmark,
    BookmarkCheck,
    Check,
    ChevronLeft,
    ChevronRight,
    Filter,
    X,
} from 'lucide-react';
import { useCallback, useState } from 'react';

import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import MobileLayout from '@/layouts/mobile-layout';

interface Answer {
    id: number;
    text: string;
    is_correct: boolean;
    position: number;
}

interface QuestionCategory {
    id: number;
    name: string;
}

interface LicenseType {
    id: number;
    code: string;
    name: string;
    is_parent: boolean;
    children: LicenseType[];
}

interface Question {
    id: number;
    question: string;
    description: string | null;
    full_description: string | null;
    image: string | null;
    image_custom: string | null;
    is_active: boolean;
    answers: Answer[];
    question_category: QuestionCategory;
}

interface UserProgress {
    question_id: number;
    times_correct: number;
    times_wrong: number;
    is_bookmarked: boolean;
}

interface PaginatedQuestions {
    data: Question[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

interface Filters {
    license_type: number | null;
    categories: number[];
    show_inactive: boolean;
    bookmarked: boolean;
    wrong_only: boolean;
    unanswered: boolean;
    per_page: number;
}

interface Stats {
    total: number;
    answered: number;
    filtered: number;
}

interface Props {
    questions: PaginatedQuestions;
    userProgress: Record<number, UserProgress>;
    licenseTypes: LicenseType[];
    categories: QuestionCategory[];
    filters: Filters;
    stats: Stats;
}

interface AnswerState {
    questionId: number;
    selectedAnswerId: number | null;
    correctAnswerId: number | null;
    isCorrect: boolean | null;
    explanation: string | null;
}

export default function QuestionsIndex({
    questions,
    userProgress,
    licenseTypes,
    categories,
    filters,
    stats,
}: Props) {
    const [answerStates, setAnswerStates] = useState<Record<number, AnswerState>>({});
    const [bookmarkedQuestions, setBookmarkedQuestions] = useState<Record<number, boolean>>(
        Object.fromEntries(
            Object.entries(userProgress).map(([qId, p]) => [qId, p.is_bookmarked])
        )
    );
    const [sessionScore, setSessionScore] = useState({ correct: 0, wrong: 0 });
    const [isFilterOpen, setIsFilterOpen] = useState(false);
    const [localFilters, setLocalFilters] = useState<Filters>(filters);

    const handleAnswer = useCallback(async (question: Question, answerId: number) => {
        if (answerStates[question.id]?.selectedAnswerId) return;

        try {
            const response = await fetch(`/questions/${question.id}/answer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>(
                        'meta[name="csrf-token"]'
                    )?.content || '',
                },
                body: JSON.stringify({ answer_id: answerId }),
            });

            const data = await response.json();

            setAnswerStates((prev) => ({
                ...prev,
                [question.id]: {
                    questionId: question.id,
                    selectedAnswerId: answerId,
                    correctAnswerId: data.correct_answer_id,
                    isCorrect: data.is_correct,
                    explanation: data.explanation,
                },
            }));

            setSessionScore((prev) => ({
                correct: prev.correct + (data.is_correct ? 1 : 0),
                wrong: prev.wrong + (data.is_correct ? 0 : 1),
            }));
        } catch (error) {
            console.error('Failed to submit answer:', error);
        }
    }, [answerStates]);

    const handleBookmark = useCallback(async (questionId: number) => {
        try {
            const response = await fetch(`/questions/${questionId}/bookmark`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>(
                        'meta[name="csrf-token"]'
                    )?.content || '',
                },
            });

            const data = await response.json();
            setBookmarkedQuestions((prev) => ({
                ...prev,
                [questionId]: data.is_bookmarked,
            }));
        } catch (error) {
            console.error('Failed to toggle bookmark:', error);
        }
    }, []);

    const applyFilters = useCallback(() => {
        setIsFilterOpen(false);
        router.get('/questions', {
            license_type: localFilters.license_type,
            categories: localFilters.categories,
            show_inactive: localFilters.show_inactive,
            bookmarked: localFilters.bookmarked,
            wrong_only: localFilters.wrong_only,
            unanswered: localFilters.unanswered,
            per_page: localFilters.per_page,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    }, [localFilters]);

    const resetFilters = useCallback(() => {
        const defaultFilters: Filters = {
            license_type: null,
            categories: [],
            show_inactive: false,
            bookmarked: false,
            wrong_only: false,
            unanswered: false,
            per_page: 20,
        };
        setLocalFilters(defaultFilters);
    }, []);

    const goToPage = useCallback((page: number) => {
        router.get('/questions', { ...filters, page }, {
            preserveState: true,
            preserveScroll: true,
        });
    }, [filters]);

    const getAnswerClassName = (question: Question, answer: Answer) => {
        const state = answerStates[question.id];
        if (!state) return 'border-border hover:border-primary hover:bg-accent';

        if (answer.id === state.correctAnswerId) {
            return 'border-green-500 bg-green-50 dark:bg-green-950';
        }
        if (answer.id === state.selectedAnswerId && !state.isCorrect) {
            return 'border-red-500 bg-red-50 dark:bg-red-950';
        }
        return 'border-border opacity-50';
    };

    return (
        <MobileLayout title="ბილეთები" subtitle={`${stats.filtered} კითხვა`}>
            <Head title="ბილეთები" />

            {/* Score Bar */}
            <div className="sticky top-0 z-10 flex items-center justify-between border-b bg-background px-4 py-2">
                <div className="flex items-center gap-4 text-sm">
                    <span className="text-green-600">
                        <Check className="mr-1 inline h-4 w-4" />
                        {sessionScore.correct}
                    </span>
                    <span className="text-red-600">
                        <X className="mr-1 inline h-4 w-4" />
                        {sessionScore.wrong}
                    </span>
                </div>

                <Sheet open={isFilterOpen} onOpenChange={setIsFilterOpen}>
                    <SheetTrigger asChild>
                        <Button variant="outline" size="sm">
                            <Filter className="mr-2 h-4 w-4" />
                            ფილტრი
                        </Button>
                    </SheetTrigger>
                    <SheetContent side="right" className="w-full overflow-y-auto sm:max-w-md">
                        <SheetHeader>
                            <SheetTitle>ფილტრები</SheetTitle>
                        </SheetHeader>

                        <div className="mt-6 space-y-6">
                            {/* License Type Filter */}
                            <div className="space-y-2">
                                <Label>კატეგორია</Label>
                                <Select
                                    value={localFilters.license_type?.toString() || ''}
                                    onValueChange={(v) =>
                                        setLocalFilters((f) => ({
                                            ...f,
                                            license_type: v ? parseInt(v) : null,
                                        }))
                                    }
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="ყველა კატეგორია" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">ყველა კატეგორია</SelectItem>
                                        {licenseTypes.map((lt) => (
                                            <SelectItem key={lt.id} value={lt.id.toString()}>
                                                {lt.code}
                                                {lt.children.length > 0 &&
                                                    `, ${lt.children.map((c) => c.code).join(', ')}`}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>

                            {/* Per Page */}
                            <div className="space-y-2">
                                <Label>გვერდზე</Label>
                                <Select
                                    value={localFilters.per_page.toString()}
                                    onValueChange={(v) =>
                                        setLocalFilters((f) => ({ ...f, per_page: parseInt(v) }))
                                    }
                                >
                                    <SelectTrigger>
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {[10, 20, 50, 100].map((n) => (
                                            <SelectItem key={n} value={n.toString()}>
                                                {n} კითხვა
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>

                            {/* Status Filters */}
                            <div className="space-y-3">
                                <Label>სტატუსი</Label>
                                <div className="space-y-2">
                                    <label className="flex items-center gap-2">
                                        <Checkbox
                                            checked={localFilters.bookmarked}
                                            onCheckedChange={(c) =>
                                                setLocalFilters((f) => ({
                                                    ...f,
                                                    bookmarked: c === true,
                                                }))
                                            }
                                        />
                                        <span className="text-sm">შენახული</span>
                                    </label>
                                    <label className="flex items-center gap-2">
                                        <Checkbox
                                            checked={localFilters.wrong_only}
                                            onCheckedChange={(c) =>
                                                setLocalFilters((f) => ({
                                                    ...f,
                                                    wrong_only: c === true,
                                                }))
                                            }
                                        />
                                        <span className="text-sm">არასწორი პასუხები</span>
                                    </label>
                                    <label className="flex items-center gap-2">
                                        <Checkbox
                                            checked={localFilters.unanswered}
                                            onCheckedChange={(c) =>
                                                setLocalFilters((f) => ({
                                                    ...f,
                                                    unanswered: c === true,
                                                }))
                                            }
                                        />
                                        <span className="text-sm">უპასუხო</span>
                                    </label>
                                    <label className="flex items-center gap-2">
                                        <Checkbox
                                            checked={localFilters.show_inactive}
                                            onCheckedChange={(c) =>
                                                setLocalFilters((f) => ({
                                                    ...f,
                                                    show_inactive: c === true,
                                                }))
                                            }
                                        />
                                        <span className="text-sm">არააქტიურიც</span>
                                    </label>
                                </div>
                            </div>

                            {/* Category Filter */}
                            <div className="space-y-2">
                                <div className="flex items-center justify-between">
                                    <Label>თემები</Label>
                                    <div className="space-x-2">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            onClick={() =>
                                                setLocalFilters((f) => ({
                                                    ...f,
                                                    categories: categories.map((c) => c.id),
                                                }))
                                            }
                                        >
                                            ყველა
                                        </Button>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            onClick={() =>
                                                setLocalFilters((f) => ({ ...f, categories: [] }))
                                            }
                                        >
                                            გასუფთავება
                                        </Button>
                                    </div>
                                </div>
                                <div className="max-h-48 space-y-1 overflow-y-auto rounded border p-2">
                                    {categories.map((cat) => (
                                        <label key={cat.id} className="flex items-center gap-2">
                                            <Checkbox
                                                checked={localFilters.categories.includes(cat.id)}
                                                onCheckedChange={(c) =>
                                                    setLocalFilters((f) => ({
                                                        ...f,
                                                        categories: c
                                                            ? [...f.categories, cat.id]
                                                            : f.categories.filter(
                                                                  (id) => id !== cat.id
                                                              ),
                                                    }))
                                                }
                                            />
                                            <span className="text-xs">{cat.name}</span>
                                        </label>
                                    ))}
                                </div>
                            </div>

                            {/* Action Buttons */}
                            <div className="flex gap-2">
                                <Button
                                    variant="outline"
                                    className="flex-1"
                                    onClick={resetFilters}
                                >
                                    გასუფთავება
                                </Button>
                                <Button className="flex-1" onClick={applyFilters}>
                                    გამოყენება
                                </Button>
                            </div>
                        </div>
                    </SheetContent>
                </Sheet>
            </div>

            {/* Questions List */}
            <div className="space-y-4 p-4">
                {questions.data.map((question, index) => {
                    const state = answerStates[question.id];
                    const isBookmarked = bookmarkedQuestions[question.id] || false;
                    const questionNumber =
                        (questions.current_page - 1) * questions.per_page + index + 1;

                    return (
                        <Card key={question.id} className={!question.is_active ? 'opacity-60' : ''}>
                            <CardContent className="p-4">
                                {/* Question Header */}
                                <div className="mb-3 flex items-start justify-between">
                                    <span className="rounded bg-muted px-2 py-1 text-xs font-medium">
                                        #{questionNumber}
                                    </span>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        className="h-8 w-8"
                                        onClick={() => handleBookmark(question.id)}
                                    >
                                        {isBookmarked ? (
                                            <BookmarkCheck className="h-5 w-5 text-yellow-500" />
                                        ) : (
                                            <Bookmark className="h-5 w-5" />
                                        )}
                                    </Button>
                                </div>

                                {/* Question Image */}
                                {(question.image || question.image_custom) && (
                                    <div className="mb-4 overflow-hidden rounded-lg">
                                        <img
                                            src={`/images/tickets/${question.image_custom || question.image}`}
                                            alt="კითხვის სურათი"
                                            className="w-full object-contain"
                                        />
                                    </div>
                                )}

                                {/* Question Text */}
                                <p className="mb-4 text-base font-medium leading-relaxed">
                                    {question.question}
                                </p>

                                {/* Answer Options */}
                                <div className="space-y-2">
                                    {question.answers.map((answer) => (
                                        <button
                                            key={answer.id}
                                            onClick={() => handleAnswer(question, answer.id)}
                                            disabled={!!state?.selectedAnswerId}
                                            className={`flex w-full items-start gap-3 rounded-lg border p-3 text-left transition-colors ${getAnswerClassName(question, answer)}`}
                                        >
                                            <span className="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full border text-xs">
                                                {answer.position}
                                            </span>
                                            <span className="text-sm">{answer.text}</span>
                                            {state?.correctAnswerId === answer.id && (
                                                <Check className="ml-auto h-5 w-5 shrink-0 text-green-600" />
                                            )}
                                            {state?.selectedAnswerId === answer.id &&
                                                !state.isCorrect && (
                                                    <X className="ml-auto h-5 w-5 shrink-0 text-red-600" />
                                                )}
                                        </button>
                                    ))}
                                </div>

                                {/* Explanation */}
                                {state?.explanation && (
                                    <div className="mt-4 rounded-lg bg-muted p-3">
                                        <p className="text-sm text-muted-foreground">
                                            {state.explanation}
                                        </p>
                                    </div>
                                )}

                                {/* Category Tag */}
                                <div className="mt-4">
                                    <span className="text-xs text-muted-foreground">
                                        {question.question_category.name}
                                    </span>
                                </div>
                            </CardContent>
                        </Card>
                    );
                })}
            </div>

            {/* Pagination */}
            {questions.last_page > 1 && (
                <div className="flex items-center justify-center gap-2 border-t p-4">
                    <Button
                        variant="outline"
                        size="icon"
                        disabled={questions.current_page === 1}
                        onClick={() => goToPage(questions.current_page - 1)}
                    >
                        <ChevronLeft className="h-4 w-4" />
                    </Button>

                    <span className="px-4 text-sm">
                        გვ. {questions.current_page} / {questions.last_page}
                    </span>

                    <Button
                        variant="outline"
                        size="icon"
                        disabled={questions.current_page === questions.last_page}
                        onClick={() => goToPage(questions.current_page + 1)}
                    >
                        <ChevronRight className="h-4 w-4" />
                    </Button>
                </div>
            )}
        </MobileLayout>
    );
}
