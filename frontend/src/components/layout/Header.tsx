import { Icon } from '@iconify/react';
import { Link, useLocation } from 'react-router';

export default function Header() {
    const location = useLocation();

    const isActive = (path: string) => location.pathname === path;

    return (
        <header className="fixed top-0 left-0 right-0 z-50 bg-base-100/80 backdrop-blur-md border-b border-base-200">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex justify-between items-center h-16">
                    {/* Logo */}
                    <Link to="/" className="flex items-center gap-2 group">
                        <div className="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                            <Icon icon="hugeicons:camera-01" className="text-2xl text-primary" />
                        </div>
                        <div className="flex flex-col">
                            <span className="font-bold text-lg leading-tight text-base-content">Al-Ameen</span>
                            <span className="text-xs font-medium text-primary tracking-wide">Face Recognition</span>
                        </div>
                    </Link>

                    {/* Desktop Navigation */}
                    <nav className="hidden md:flex items-center gap-1">
                        <Link
                            to="/"
                            className={`px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 ${isActive('/')
                                    ? 'bg-primary/10 text-primary'
                                    : 'text-base-content/70 hover:bg-base-200 hover:text-base-content'
                                }`}
                        >
                            Home
                        </Link>
                        <Link
                            to="/download"
                            className={`px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 ${isActive('/download')
                                    ? 'bg-primary/10 text-primary'
                                    : 'text-base-content/70 hover:bg-base-200 hover:text-base-content'
                                }`}
                        >
                            Download
                        </Link>
                    </nav>

                    {/* Mobile Menu Button - Placeholder for active state or future expansion */}
                    <div className="md:hidden">
                        {/* Can add a mobile menu dropdown here if needed */}
                        <div className="w-10 h-10 flex items-center justify-center">
                            <Icon icon="hugeicons:menu-01" className="text-2xl text-base-content/70" />
                        </div>
                    </div>
                </div>
            </div>
        </header>
    );
}
