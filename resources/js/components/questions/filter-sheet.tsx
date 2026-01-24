import { Check, Search, X } from 'lucide-react';
import { useMemo, useState } from 'react';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import type { QuestionCategory, QuestionFilters } from '@/types/models';

interface FilterSheetProps {
    categories: QuestionCategory[];
    categoryCounts: Record<number, number>;
    localFilters: QuestionFilters;
    onFiltersChange: (filters: QuestionFilters) => void;
}

export function FilterSheet({
    categories,
    categoryCounts,
    localFilters,
    onFiltersChange,
}: FilterSheetProps) {
    const [categorySearch, setCategorySearch] = useState('');

    // Calculate total count for all categories
    const totalCategoryCount = useMemo(() => {
        return Object.values(categoryCounts).reduce(
            (sum, count) => sum + count,
            0,
        );
    }, [categoryCounts]);

    // Filter categories by search term
    const filteredCategories = useMemo(() => {
        if (!categorySearch.trim()) return categories;
        const searchLower = categorySearch.toLowerCase();
        return categories.filter((cat) =>
            cat.name.toLowerCase().includes(searchLower),
        );
    }, [categories, categorySearch]);

    const handleSelectAll = () => {
        const allCategories = categories
            .filter((c) => (categoryCounts[c.id] || 0) > 0)
            .map((c) => c.id);
        onFiltersChange({
            ...localFilters,
            categories: allCategories,
        });
    };

    const handleClearAll = () => {
        onFiltersChange({
            ...localFilters,
            categories: [],
        });
    };

    const handleToggleCategory = (categoryId: number) => {
        const isSelected = localFilters.categories.includes(categoryId);
        onFiltersChange({
            ...localFilters,
            categories: isSelected
                ? localFilters.categories.filter((id) => id !== categoryId)
                : [...localFilters.categories, categoryId],
        });
    };

    return (
        <SheetContent
            side="right"
            className="flex flex-col overflow-hidden"
            onOpenAutoFocus={(e) => e.preventDefault()}
        >
            <SheetHeader className="flex-row items-center gap-3 px-5 pl-14">
                <SheetTitle className="text-xl">თემები</SheetTitle>
            </SheetHeader>

            {/* Search Input */}
            <div className="px-5 pb-3">
                <div className="relative">
                    <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        placeholder="ძებნა..."
                        value={categorySearch}
                        onChange={(e) => setCategorySearch(e.target.value)}
                        className="pr-9 pl-9"
                    />
                    {categorySearch && (
                        <button
                            type="button"
                            onClick={() => setCategorySearch('')}
                            className="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                        >
                            <X className="h-4 w-4" />
                        </button>
                    )}
                </div>
            </div>

            {/* Action Buttons */}
            <div className="flex gap-2 px-5 pb-3">
                <Button
                    variant="outline"
                    size="sm"
                    className="flex-1"
                    onClick={handleSelectAll}
                >
                    ყველა ({totalCategoryCount})
                </Button>
                <Button
                    variant="outline"
                    size="sm"
                    className="flex-1"
                    onClick={handleClearAll}
                >
                    გასუფთავება
                </Button>
            </div>

            {/* Category List */}
            <div className="flex-1 space-y-1 overflow-y-auto px-5 pb-6">
                {filteredCategories.map((cat) => {
                    const count = categoryCounts[cat.id] || 0;
                    const isSelected = localFilters.categories.includes(cat.id);
                    return (
                        <button
                            key={cat.id}
                            disabled={count === 0}
                            onClick={() => handleToggleCategory(cat.id)}
                            className={`flex w-full items-center justify-between gap-3 rounded-lg border p-4 text-left transition-colors ${
                                count === 0
                                    ? 'cursor-not-allowed opacity-40'
                                    : isSelected
                                      ? 'border-primary bg-primary/10'
                                      : 'hover:bg-accent'
                            }`}
                        >
                            <div className="flex items-center gap-3">
                                <div
                                    className={`flex h-4 w-4 shrink-0 items-center justify-center rounded-sm border ${
                                        isSelected
                                            ? 'border-primary bg-primary text-primary-foreground'
                                            : 'border-input'
                                    }`}
                                >
                                    {isSelected && <Check className="h-3 w-3" />}
                                </div>
                                <span className="text-base">{cat.name}</span>
                            </div>
                            <span className="text-sm text-muted-foreground">
                                {count}
                            </span>
                        </button>
                    );
                })}
            </div>
        </SheetContent>
    );
}
