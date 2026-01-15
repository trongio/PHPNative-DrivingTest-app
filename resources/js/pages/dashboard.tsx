import { Head, usePage } from '@inertiajs/react';

import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import MobileLayout from '@/layouts/mobile-layout';
import { type SharedData } from '@/types';

export default function Dashboard() {
    const { auth } = usePage<SharedData>().props;
    const user = auth.user;

    const getInitials = (name: string) => {
        return name
            .split(' ')
            .map((n) => n[0])
            .join('')
            .toUpperCase()
            .slice(0, 2);
    };

    return (
        <MobileLayout>
            <Head title="მთავარი" />
            <div className="flex flex-col gap-4 p-4">
                {/* User Profile Card */}
                <Card>
                    <CardContent className="flex items-center gap-4 p-4">
                        <Avatar className="h-16 w-16">
                            <AvatarImage src={user.profile_image_url || undefined} alt={user.name} />
                            <AvatarFallback className="bg-primary text-xl text-primary-foreground">
                                {getInitials(user.name)}
                            </AvatarFallback>
                        </Avatar>
                        <div>
                            <h2 className="text-lg font-semibold">{user.name}</h2>
                            <p className="text-sm text-muted-foreground">მოსწავლე</p>
                        </div>
                    </CardContent>
                </Card>

                {/* Statistics */}
                <Card>
                    <CardHeader className="pb-2">
                        <CardTitle className="text-base">სტატისტიკა</CardTitle>
                    </CardHeader>
                    <CardContent className="grid grid-cols-2 gap-3">
                        <div className="rounded-lg bg-blue-500/10 p-3 text-center">
                            <div className="text-2xl font-bold text-blue-500">0</div>
                            <div className="text-xs text-muted-foreground">ტესტები</div>
                        </div>
                        <div className="rounded-lg bg-green-500/10 p-3 text-center">
                            <div className="text-2xl font-bold text-green-500">0%</div>
                            <div className="text-xs text-muted-foreground">წარმატება</div>
                        </div>
                        <div className="rounded-lg bg-purple-500/10 p-3 text-center">
                            <div className="text-2xl font-bold text-purple-500">0</div>
                            <div className="text-xs text-muted-foreground">სწორი პასუხი</div>
                        </div>
                        <div className="rounded-lg bg-orange-500/10 p-3 text-center">
                            <div className="text-2xl font-bold text-orange-500">0</div>
                            <div className="text-xs text-muted-foreground">არასწორი</div>
                        </div>
                    </CardContent>
                </Card>

                {/* Progress */}
                <Card>
                    <CardHeader className="pb-2">
                        <CardTitle className="text-base">პროგრესი</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="mb-2 flex justify-between text-sm">
                            <span className="text-muted-foreground">შესწავლილი კითხვები</span>
                            <span className="font-medium">0 / 1000</span>
                        </div>
                        <div className="h-2 overflow-hidden rounded-full bg-secondary">
                            <div className="h-full w-0 rounded-full bg-primary transition-all" />
                        </div>
                    </CardContent>
                </Card>
            </div>
        </MobileLayout>
    );
}
