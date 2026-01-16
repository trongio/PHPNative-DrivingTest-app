import { Head } from '@inertiajs/react';
import { History } from 'lucide-react';

import { Card, CardContent } from '@/components/ui/card';
import MobileLayout from '@/layouts/mobile-layout';

export default function HistoryIndex() {
    return (
        <MobileLayout>
            <Head title="ისტორია" />
            <div className="flex flex-col gap-4 p-4">
                <Card>
                    <CardContent className="flex flex-col items-center justify-center gap-4 py-12">
                        <History className="h-16 w-16 text-muted-foreground" />
                        <div className="text-center">
                            <h2 className="text-lg font-semibold">ისტორია</h2>
                            <p className="text-sm text-muted-foreground">
                                მალე დაემატება
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </MobileLayout>
    );
}
