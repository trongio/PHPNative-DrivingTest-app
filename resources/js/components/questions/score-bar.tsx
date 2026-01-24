import { Bookmark, Check, Filter, TriangleAlert, X } from 'lucide-react';

import { LicenseTypeSelect } from '@/components/license-type-select';
import { Button } from '@/components/ui/button';
import { SheetTrigger } from '@/components/ui/sheet';
import type { LicenseType, QuestionFilters } from '@/types/models';

interface ScoreBarProps {
    sessionScore: { correct: number; wrong: number };
    filters: QuestionFilters;
    licenseTypes: LicenseType[];
    onToggleCorrectFilter: () => void;
    onToggleWrongFilter: () => void;
    onToggleBookmarkFilter: () => void;
    onToggleInactiveFilter: () => void;
    onLicenseTypeChange: (licenseType: number | null) => void;
}

export function ScoreBar({
    sessionScore,
    filters,
    licenseTypes,
    onToggleCorrectFilter,
    onToggleWrongFilter,
    onToggleBookmarkFilter,
    onToggleInactiveFilter,
    onLicenseTypeChange,
}: ScoreBarProps) {
    return (
        <div className="sticky top-0 z-10 flex items-center justify-between gap-2 border-b bg-background px-4 py-2">
            <div className="flex items-center gap-2 text-sm">
                <button
                    onClick={onToggleCorrectFilter}
                    className={`flex items-center gap-1 rounded-md px-2 py-1 transition-colors ${
                        filters.correct_only
                            ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                            : 'text-green-600 hover:bg-green-50 dark:hover:bg-green-950'
                    }`}
                >
                    <Check className="h-4 w-4" />
                    {sessionScore.correct}
                </button>
                <button
                    onClick={onToggleWrongFilter}
                    className={`flex items-center gap-1 rounded-md px-2 py-1 transition-colors ${
                        filters.wrong_only
                            ? 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300'
                            : 'text-red-600 hover:bg-red-50 dark:hover:bg-red-950'
                    }`}
                >
                    <X className="h-4 w-4" />
                    {sessionScore.wrong}
                </button>
            </div>

            <div className="flex items-center gap-2">
                {/* Bookmarked Questions Toggle */}
                <Button
                    variant="outline"
                    size="icon"
                    className="h-8 w-8"
                    onClick={onToggleBookmarkFilter}
                >
                    <Bookmark
                        className={`h-4 w-4 ${
                            filters.bookmarked
                                ? 'fill-yellow-500 text-yellow-500'
                                : 'text-muted-foreground'
                        }`}
                    />
                </Button>

                {/* Inactive Questions Toggle */}
                <Button
                    variant="outline"
                    size="icon"
                    className="h-8 w-8"
                    onClick={onToggleInactiveFilter}
                >
                    <TriangleAlert
                        className={`h-4 w-4 ${
                            filters.show_inactive
                                ? 'text-red-500'
                                : 'text-muted-foreground'
                        }`}
                    />
                </Button>

                {/* License Type Selector */}
                <LicenseTypeSelect
                    value={filters.license_type}
                    onValueChange={onLicenseTypeChange}
                    licenseTypes={licenseTypes}
                    placeholder="ყველა"
                    emptyLabel="ყველა"
                />

                <SheetTrigger asChild>
                    <Button variant="outline" size="sm">
                        <Filter className="h-4 w-4" />
                    </Button>
                </SheetTrigger>
            </div>
        </div>
    );
}
