import { Head, router, useForm } from '@inertiajs/react';
import { useRef, useState, type FormEvent } from 'react';

interface User {
    id: number;
    name: string;
    profile_image_url: string | null;
    has_password: boolean;
}

interface Props {
    users: User[];
}

export default function UserSelection({ users }: Props) {
    const [selectedUser, setSelectedUser] = useState<User | null>(null);
    const [isCreating, setIsCreating] = useState(false);
    const [newImagePreview, setNewImagePreview] = useState<string | null>(null);
    const fileInputRef = useRef<HTMLInputElement>(null);

    // Login form - include 'error' for general auth errors from Laravel
    const loginForm = useForm<{
        user_id: number;
        password: string;
        error?: string;
    }>({
        user_id: 0,
        password: '',
    });

    // Registration form
    const registerForm = useForm<{
        name: string;
        password: string;
        profile_image: File | null;
    }>({
        name: '',
        password: '',
        profile_image: null,
    });

    const handleUserClick = (user: User) => {
        if (user.has_password) {
            setSelectedUser(user);
            loginForm.setData('user_id', user.id);
            loginForm.setData('password', '');
            loginForm.clearErrors();
        } else {
            // Direct login for passwordless users - use router.post with data directly
            // (setData is async, so loginForm.post would use stale data)
            router.post('/login', { user_id: user.id, password: '' });
        }
    };

    const handlePasswordSubmit = (e: FormEvent) => {
        e.preventDefault();
        loginForm.post('/login');
    };

    const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            registerForm.setData('profile_image', file);
            const reader = new FileReader();
            reader.onloadend = () => {
                setNewImagePreview(reader.result as string);
            };
            reader.readAsDataURL(file);
        }
    };

    const handleCreateUser = (e: FormEvent) => {
        e.preventDefault();
        // Use JSON for text-only submission, FormData only when image is selected
        if (registerForm.data.profile_image) {
            registerForm.post('/register', {
                forceFormData: true,
            });
        } else {
            registerForm.post('/register');
        }
    };

    const resetCreateForm = () => {
        setIsCreating(false);
        registerForm.reset();
        setNewImagePreview(null);
    };

    const getInitials = (name: string) => {
        return name
            .split(' ')
            .map((n) => n[0])
            .join('')
            .toUpperCase()
            .slice(0, 2);
    };

    const getRandomColor = (name: string) => {
        const colors = [
            'bg-blue-500',
            'bg-green-500',
            'bg-purple-500',
            'bg-pink-500',
            'bg-indigo-500',
            'bg-teal-500',
            'bg-orange-500',
            'bg-cyan-500',
        ];
        const index = name.charCodeAt(0) % colors.length;
        return colors[index];
    };

    // Password prompt modal
    if (selectedUser) {
        return (
            <>
                <Head title="პაროლის შეყვანა" />
                <div className="flex min-h-screen flex-col items-center justify-center bg-gray-100 p-4 dark:bg-gray-900">
                    <div className="w-full max-w-sm rounded-2xl bg-white p-6 shadow-lg dark:bg-gray-800">
                        <button
                            onClick={() => {
                                setSelectedUser(null);
                                loginForm.reset();
                            }}
                            className="mb-4 text-gray-500 hover:text-gray-700 dark:text-gray-400"
                        >
                            ← უკან
                        </button>

                        <div className="mb-6 flex flex-col items-center">
                            {selectedUser.profile_image_url ? (
                                <img
                                    src={selectedUser.profile_image_url}
                                    alt={selectedUser.name}
                                    className="mb-3 h-20 w-20 rounded-full object-cover"
                                />
                            ) : (
                                <div
                                    className={`mb-3 flex h-20 w-20 items-center justify-center rounded-full text-2xl font-bold text-white ${getRandomColor(selectedUser.name)}`}
                                >
                                    {getInitials(selectedUser.name)}
                                </div>
                            )}
                            <h2 className="text-xl font-semibold text-gray-900 dark:text-white">
                                {selectedUser.name}
                            </h2>
                        </div>

                        <form onSubmit={handlePasswordSubmit}>
                            <input
                                type="password"
                                value={loginForm.data.password}
                                onChange={(e) =>
                                    loginForm.setData(
                                        'password',
                                        e.target.value,
                                    )
                                }
                                placeholder="პაროლი"
                                className="mb-3 w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                autoFocus
                            />
                            {loginForm.errors.error && (
                                <p className="mb-3 text-sm text-red-500">
                                    {loginForm.errors.error}
                                </p>
                            )}
                            {loginForm.errors.password && (
                                <p className="mb-3 text-sm text-red-500">
                                    {loginForm.errors.password}
                                </p>
                            )}
                            <button
                                type="submit"
                                disabled={loginForm.processing}
                                className="w-full rounded-xl bg-blue-500 py-3 font-semibold text-white transition hover:bg-blue-600 disabled:opacity-50"
                            >
                                {loginForm.processing
                                    ? 'იტვირთება...'
                                    : 'შესვლა'}
                            </button>
                        </form>
                    </div>
                </div>
            </>
        );
    }

    // Create new user form
    if (isCreating) {
        return (
            <>
                <Head title="ახალი მომხმარებელი" />
                <div className="flex min-h-screen flex-col items-center justify-center bg-gray-100 p-4 dark:bg-gray-900">
                    <div className="w-full max-w-sm rounded-2xl bg-white p-6 shadow-lg dark:bg-gray-800">
                        <button
                            onClick={resetCreateForm}
                            className="mb-4 text-gray-500 hover:text-gray-700 dark:text-gray-400"
                        >
                            ← უკან
                        </button>

                        <h2 className="mb-6 text-center text-xl font-semibold text-gray-900 dark:text-white">
                            ახალი მომხმარებელი
                        </h2>

                        <form onSubmit={handleCreateUser}>
                            {/* Profile Image */}
                            <div className="mb-4 flex justify-center">
                                <button
                                    type="button"
                                    onClick={() =>
                                        fileInputRef.current?.click()
                                    }
                                    className="relative"
                                >
                                    {newImagePreview ? (
                                        <img
                                            src={newImagePreview}
                                            alt="Preview"
                                            className="h-24 w-24 rounded-full object-cover"
                                        />
                                    ) : (
                                        <div className="flex h-24 w-24 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-600">
                                            <span className="text-3xl text-gray-400">
                                                +
                                            </span>
                                        </div>
                                    )}
                                    <div className="absolute right-0 bottom-0 flex h-8 w-8 items-center justify-center rounded-full bg-blue-500 text-white">
                                        <span className="text-sm">📷</span>
                                    </div>
                                </button>
                                <input
                                    ref={fileInputRef}
                                    type="file"
                                    accept="image/*"
                                    onChange={handleImageChange}
                                    className="hidden"
                                />
                            </div>
                            <p className="mb-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                დაამატეთ ფოტო (არასავალდებულო)
                            </p>

                            {/* Name */}
                            <input
                                type="text"
                                value={registerForm.data.name}
                                onChange={(e) =>
                                    registerForm.setData('name', e.target.value)
                                }
                                placeholder="სახელი / მეტსახელი"
                                className="mb-3 w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                autoFocus
                            />
                            {registerForm.errors.name && (
                                <p className="mb-3 text-sm text-red-500">
                                    {registerForm.errors.name}
                                </p>
                            )}

                            {/* Password (optional) */}
                            <input
                                type="password"
                                value={registerForm.data.password}
                                onChange={(e) =>
                                    registerForm.setData(
                                        'password',
                                        e.target.value,
                                    )
                                }
                                placeholder="პაროლი (არასავალდებულო)"
                                className="mb-3 w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            />
                            {registerForm.errors.password && (
                                <p className="mb-3 text-sm text-red-500">
                                    {registerForm.errors.password}
                                </p>
                            )}
                            <p className="mb-4 text-xs text-gray-500 dark:text-gray-400">
                                პაროლი საჭიროა მხოლოდ თუ გსურთ თქვენი ანგარიშის
                                დაცვა
                            </p>

                            <button
                                type="submit"
                                disabled={
                                    registerForm.processing ||
                                    !registerForm.data.name.trim()
                                }
                                className="w-full rounded-xl bg-blue-500 py-3 font-semibold text-white transition hover:bg-blue-600 disabled:opacity-50"
                            >
                                {registerForm.processing
                                    ? 'იტვირთება...'
                                    : 'დაწყება'}
                            </button>
                        </form>
                    </div>
                </div>
            </>
        );
    }

    // Main user selection screen
    return (
        <>
            <Head title="აირჩიეთ მომხმარებელი" />
            <div className="flex min-h-screen flex-col bg-gray-100 p-4 dark:bg-gray-900">
                <div className="mx-auto w-full max-w-md">
                    {/* Header */}
                    <div className="mb-8 pt-8 text-center">
                        <h1 className="mb-2 text-2xl font-bold text-gray-900 dark:text-white">
                            მართვის მოწმობა
                        </h1>
                        <p className="text-gray-600 dark:text-gray-400">
                            აირჩიეთ მომხმარებელი
                        </p>
                    </div>

                    {/* User Cards */}
                    {users.length > 0 && (
                        <div className="mb-6 grid grid-cols-2 gap-4">
                            {users.map((user) => (
                                <button
                                    key={user.id}
                                    onClick={() => handleUserClick(user)}
                                    className="flex flex-col items-center rounded-2xl bg-white p-4 shadow-md transition hover:shadow-lg active:scale-95 dark:bg-gray-800"
                                >
                                    {user.profile_image_url ? (
                                        <img
                                            src={user.profile_image_url}
                                            alt={user.name}
                                            className="mb-3 h-16 w-16 rounded-full object-cover"
                                        />
                                    ) : (
                                        <div
                                            className={`mb-3 flex h-16 w-16 items-center justify-center rounded-full text-xl font-bold text-white ${getRandomColor(user.name)}`}
                                        >
                                            {getInitials(user.name)}
                                        </div>
                                    )}
                                    <span className="font-medium text-gray-900 dark:text-white">
                                        {user.name}
                                    </span>
                                    {user.has_password && (
                                        <span className="mt-1 text-xs text-gray-500">
                                            🔒
                                        </span>
                                    )}
                                </button>
                            ))}
                        </div>
                    )}

                    {/* Add New User Button */}
                    <button
                        onClick={() => setIsCreating(true)}
                        className="flex w-full items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-gray-300 bg-white/50 py-6 text-gray-600 transition hover:border-blue-400 hover:bg-white hover:text-blue-500 dark:border-gray-600 dark:bg-gray-800/50 dark:text-gray-400 dark:hover:border-blue-500 dark:hover:text-blue-400"
                    >
                        <span className="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-xl text-blue-500 dark:bg-blue-900">
                            +
                        </span>
                        <span className="font-medium">ახალი მომხმარებელი</span>
                    </button>
                </div>
            </div>
        </>
    );
}
