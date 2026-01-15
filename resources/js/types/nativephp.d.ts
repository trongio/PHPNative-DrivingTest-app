import 'react';

declare module 'react' {
    namespace JSX {
        interface IntrinsicElements {
            'native:top-bar': React.DetailedHTMLProps<
                React.HTMLAttributes<HTMLElement> & {
                    title: string;
                    subtitle?: string;
                    'show-navigation-icon'?: boolean;
                    label?: string;
                    'background-color'?: string;
                    'text-color'?: string;
                    elevation?: number;
                },
                HTMLElement
            >;
            'native:top-bar-action': React.DetailedHTMLProps<
                React.HTMLAttributes<HTMLElement> & {
                    id: string;
                    icon: string;
                    label?: string;
                    url?: string;
                },
                HTMLElement
            >;
            'native:bottom-nav': React.DetailedHTMLProps<
                React.HTMLAttributes<HTMLElement> & {
                    'label-visibility'?: 'labeled' | 'selected' | 'unlabeled';
                    dark?: boolean;
                },
                HTMLElement
            >;
            'native:bottom-nav-item': React.DetailedHTMLProps<
                React.HTMLAttributes<HTMLElement> & {
                    id: string;
                    icon: string;
                    label: string;
                    url: string;
                    active?: boolean;
                    badge?: string | number;
                    news?: boolean;
                },
                HTMLElement
            >;
        }
    }
}
