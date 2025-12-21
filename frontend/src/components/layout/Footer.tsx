import { Icon } from '@iconify/react';
import { Link } from 'react-router';

export default function Footer() {
    return (
        <footer className="bg-base-100 border-t border-base-200 pt-16 pb-8">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                    {/* Brand Column */}
                    <div className="col-span-1 md:col-span-2">
                        <div className="flex items-center gap-2 mb-6">
                            <div className="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
                                <Icon icon="hugeicons:camera-01" className="text-2xl text-primary" />
                            </div>
                            <span className="font-bold text-xl text-base-content">Al-Ameen Face</span>
                        </div>
                        <p className="text-base-content/60 text-sm leading-relaxed max-w-sm mb-6">
                            Official internal tool for Al-Ameen Mission identity verification and attendance management.
                        </p>
                    </div>

                    {/* Quick Links */}
                    <div>
                        <h3 className="font-bold text-base-content mb-6">Quick Links</h3>
                        <ul className="space-y-4">
                            <li>
                                <Link to="/" className="text-sm text-base-content/60 hover:text-primary transition-colors">
                                    Home
                                </Link>
                            </li>
                            <li>
                                <Link to="/download" className="text-sm text-base-content/60 hover:text-primary transition-colors">
                                    Download App
                                </Link>
                            </li>
                        </ul>
                    </div>

                    {/* Legal */}
                    <div>
                        <h3 className="font-bold text-base-content mb-6">Legal</h3>
                        <ul className="space-y-4">
                            <li>
                                <Link to="/furdle/privacy" className="text-sm text-base-content/60 hover:text-primary transition-colors">
                                    Privacy Policy
                                </Link>
                            </li>
                            <li>
                                <a href="#" className="text-sm text-base-content/60 hover:text-primary transition-colors">
                                    Terms of Service
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div className="pt-8 border-t border-base-200">
                    <div className="flex flex-col md:flex-row justify-between items-center gap-4">
                        <p className="text-sm text-base-content/40">
                            © {new Date().getFullYear()} Al-Ameen Face Recognition. All rights reserved.
                        </p>
                        <div className="flex items-center gap-2 text-sm text-base-content/40">
                            <span>Powered by</span>
                            <span className="font-semibold text-primary">Furdle</span>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    );
}
