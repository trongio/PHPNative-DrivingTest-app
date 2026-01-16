import { cn } from '@/lib/utils';

interface SignCardProps {
    sign: {
        id: number;
        image: string;
        title: string;
    };
    isSelected?: boolean;
    onClick?: () => void;
}

export function SignCard({ sign, isSelected, onClick }: SignCardProps) {
    return (
        <button
            onClick={onClick}
            className={cn(
                'flex flex-col items-center gap-1.5 rounded-lg border bg-card p-2 transition-all',
                'hover:bg-accent hover:shadow-sm active:scale-95',
                isSelected && 'border-primary bg-primary/5 ring-2 ring-primary/20',
            )}
        >
            <div className="relative aspect-square w-full overflow-hidden rounded-md bg-muted/50">
                <img
                    src={`/images/signs/${sign.image}`}
                    alt={sign.title}
                    loading="lazy"
                    className="h-full w-full object-contain p-1"
                />
            </div>
            <span className="line-clamp-2 w-full overflow-hidden text-center text-xs text-muted-foreground break-all">
                {sign.title}
            </span>
        </button>
    );
}
