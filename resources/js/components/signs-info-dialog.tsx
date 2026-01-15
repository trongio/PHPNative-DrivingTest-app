import { useState } from 'react';

import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface Sign {
    id: number;
    image: string;
    title: string;
    description: string | null;
}

interface SignsInfoDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    description: string | null;
    signs: Sign[];
}

export function SignsInfoDialog({ open, onOpenChange, description, signs }: SignsInfoDialogProps) {
    const [selectedSign, setSelectedSign] = useState<Sign | null>(null);

    const handleOpenChange = (newOpen: boolean) => {
        if (!newOpen) {
            setSelectedSign(null);
        }
        onOpenChange(newOpen);
    };

    return (
        <Dialog open={open} onOpenChange={handleOpenChange}>
            <DialogContent className="max-h-[80vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>დამატებითი ინფორმაცია</DialogTitle>
                </DialogHeader>
                <div className="space-y-4">
                    {/* Signs Grid - 5 columns */}
                    {signs.length > 0 && (
                        <div className="grid grid-cols-5 gap-2">
                            {signs.map((sign) => (
                                <button
                                    key={sign.id}
                                    onClick={() => {
                                        setSelectedSign(
                                            selectedSign?.id === sign.id
                                                ? null
                                                : sign,
                                        );
                                        setTimeout(() => {
                                            document
                                                .getElementById(
                                                    `sign-detail-${sign.id}`,
                                                )
                                                ?.scrollIntoView({
                                                    behavior: 'smooth',
                                                    block: 'center',
                                                });
                                        }, 100);
                                    }}
                                    className={`flex flex-col items-center rounded-lg border p-2 transition-colors ${selectedSign?.id === sign.id ? 'border-primary bg-accent' : 'hover:bg-accent'}`}
                                >
                                    <img
                                        src={`/images/signs/${sign.image}`}
                                        alt={sign.title}
                                        className="h-12 w-12 object-contain"
                                    />
                                </button>
                            ))}
                        </div>
                    )}

                    {/* Selected Sign Details */}
                    {selectedSign && (
                        <div
                            id={`sign-detail-${selectedSign.id}`}
                            className="rounded-lg border bg-card p-4"
                        >
                            <div className="flex items-start gap-4">
                                <img
                                    src={`/images/signs/${selectedSign.image}`}
                                    alt={selectedSign.title}
                                    className="h-20 w-20 shrink-0 object-contain"
                                />
                                <div className="flex-1">
                                    <h4 className="font-semibold">
                                        {selectedSign.title}
                                    </h4>
                                    {selectedSign.description && (
                                        <p className="mt-1 text-sm text-muted-foreground">
                                            {selectedSign.description}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Question Description */}
                    {description && (
                        <div className="rounded-lg bg-muted p-3">
                            <p className="text-sm">{description}</p>
                        </div>
                    )}
                </div>
            </DialogContent>
        </Dialog>
    );
}
