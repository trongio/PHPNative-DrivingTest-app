import { type ReactNode } from 'react';

interface MobileLayoutProps {
    children: ReactNode;
    title: string;
    subtitle?: string;
    showBackButton?: boolean;
    actions?: ReactNode;
}

export default function MobileLayout({
    children,
    title,
    subtitle,
    showBackButton = true,
    actions,
}: MobileLayoutProps) {
    return (
        <div className="flex min-h-screen flex-col bg-background">
            <native:top-bar
                title={title}
                subtitle={subtitle}
                show-navigation-icon={showBackButton}
            >
                {actions}
            </native:top-bar>

            <main className="flex-1 overflow-y-auto pb-20">{children}</main>

            <native:bottom-nav label-visibility="labeled">
                <native:bottom-nav-item
                    id="home"
                    icon="home"
                    label="მთავარი"
                    url="/dashboard"
                />
                <native:bottom-nav-item
                    id="questions"
                    icon="book"
                    label="ბილეთები"
                    url="/questions"
                />
                <native:bottom-nav-item
                    id="test"
                    icon="edit"
                    label="ტესტი"
                    url="/test"
                />
                <native:bottom-nav-item
                    id="signs"
                    icon="image"
                    label="ნიშნები"
                    url="/signs"
                />
                <native:bottom-nav-item
                    id="profile"
                    icon="person"
                    label="პროფილი"
                    url="/settings/profile"
                />
            </native:bottom-nav>
        </div>
    );
}
