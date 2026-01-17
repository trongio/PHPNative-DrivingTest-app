import { Card, CardContent } from '@/components/ui/card';

interface TestResultStatsProps {
    correctCount: number;
    wrongCount: number;
    unansweredCount: number;
}

export function TestResultStats({
    correctCount,
    wrongCount,
    unansweredCount,
}: TestResultStatsProps) {
    return (
        <div className="grid grid-cols-3 gap-2">
            <Card>
                <CardContent className="p-3 text-center">
                    <div className="text-xl font-bold text-green-600 dark:text-green-400">
                        {correctCount}
                    </div>
                    <div className="text-xs text-muted-foreground">სწორი</div>
                </CardContent>
            </Card>
            <Card>
                <CardContent className="p-3 text-center">
                    <div className="text-xl font-bold text-red-600 dark:text-red-400">
                        {wrongCount}
                    </div>
                    <div className="text-xs text-muted-foreground">
                        არასწორი
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardContent className="p-3 text-center">
                    <div className="text-xl font-bold text-yellow-600 dark:text-yellow-400">
                        {unansweredCount}
                    </div>
                    <div className="text-xs text-muted-foreground">უპასუხო</div>
                </CardContent>
            </Card>
        </div>
    );
}
