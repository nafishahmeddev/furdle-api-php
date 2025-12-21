import { Icon } from '@iconify/react';

export default function DownloadPage() {
    return (
        <div className="min-h-screen bg-gradient-to-br from-base-100 to-base-200 flex flex-col items-center justify-center p-6 relative overflow-hidden">

            {/* Background Decoration */}
            <div className="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
                <div className="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/5 rounded-full blur-3xl"></div>
                <div className="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-secondary/5 rounded-full blur-3xl"></div>
            </div>

            <div className="max-w-4xl w-full grid md:grid-cols-2 gap-12 items-center relative z-10">

                {/* Left Content */}
                <div className="text-left space-y-8">
                    <div className="space-y-4">
                        <div className="inline-flex items-center px-3 py-1 bg-primary/10 rounded-full border border-primary/20">
                            <Icon icon="hugeicons:smart-phone-01" className="text-sm text-primary mr-2" />
                            <span className="text-xs font-semibold text-primary uppercase tracking-wide">Mobile App</span>
                        </div>
                        <h1 className="text-5xl font-bold text-base-content leading-tight">
                            Al-Ameen
                            <span className="block text-primary">Furdle Mobile</span>
                        </h1>
                        <p className="text-lg text-base-content/70 leading-relaxed max-w-md">
                            Experience secure, fast, and reliable identity verification on your mobile device. Built for internal organizational use.
                        </p>
                    </div>

                    <div className="flex flex-col gap-4 max-w-sm">
                        <a
                            href="/releases/android-furdle-v1.0.0.apk"
                            download
                            className="group flex items-center justify-between bg-base-100 hover:bg-base-200 border border-base-300 rounded-2xl p-4 transition-all duration-300 hover:shadow-lg hover:-translate-y-1"
                        >
                            <div className="flex items-center gap-4">
                                <div className="w-12 h-12 bg-success/10 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <Icon icon="hugeicons:android" className="text-2xl text-success" />
                                </div>
                                <div className="text-left">
                                    <div className="font-bold text-base-content">Download for Android</div>
                                    <div className="text-xs text-base-content/50">v1.0.0 • APK</div>
                                </div>
                            </div>
                            <Icon icon="hugeicons:download-04" className="text-xl text-base-content/40 group-hover:text-primary transition-colors" />
                        </a>

                        <button
                            onClick={() => alert("iOS version coming soon!")}
                            className="group flex items-center justify-between bg-base-100/50 border border-base-300 rounded-2xl p-4 cursor-not-allowed opacity-70"
                        >
                            <div className="flex items-center gap-4">
                                <div className="w-12 h-12 bg-neutral/10 rounded-xl flex items-center justify-center">
                                    <Icon icon="hugeicons:apple" className="text-2xl text-neutral" />
                                </div>
                                <div className="text-left">
                                    <div className="font-bold text-base-content">Download for iOS</div>
                                    <div className="text-xs text-base-content/50">Coming Soon</div>
                                </div>
                            </div>
                            <Icon icon="hugeicons:lock-01" className="text-xl text-base-content/40" />
                        </button>
                    </div>

                    <div className="flex items-center gap-2 text-sm text-base-content/40">
                        <Icon icon="hugeicons:shield-02" className="text-lg" />
                        <span>Protected for Internal Use Only</span>
                    </div>
                </div>

                {/* Right Visual (Abstract Device Mockup) */}
                <div className="hidden md:flex justify-center items-center">
                    <div className="relative">
                        <div className="absolute inset-0 bg-primary/20 blur-[100px] rounded-full"></div>
                        <div className="relative w-[300px] h-[600px] bg-base-100 border-8 border-base-300 rounded-[3rem] shadow-2xl overflow-hidden flex flex-col">
                            {/* Status Bar */}
                            <div className="h-6 w-full bg-base-200 flex justify-between px-6 py-2 items-center">
                                <div className="w-10 h-2 bg-base-300 rounded-full"></div>
                                <div className="flex gap-1">
                                    <div className="w-2 h-2 bg-base-300 rounded-full"></div>
                                    <div className="w-2 h-2 bg-base-300 rounded-full"></div>
                                </div>
                            </div>

                            {/* App Screen Content */}
                            <div className="flex-1 bg-gradient-to-br from-base-100 to-base-200 p-6 flex flex-col items-center justify-center space-y-6">
                                <div className="w-24 h-24 bg-primary/10 rounded-3xl flex items-center justify-center mb-4">
                                    <Icon icon="hugeicons:camera-01" className="text-5xl text-primary" />
                                </div>
                                <div className="w-32 h-4 bg-base-300 rounded-full"></div>
                                <div className="w-20 h-3 bg-base-200 rounded-full"></div>

                                <div className="w-full h-32 bg-base-100 rounded-2xl border border-base-200 shadow-sm mt-8"></div>
                                <div className="w-full h-12 bg-primary/10 rounded-xl mt-4"></div>
                            </div>

                            {/* Home Indicator */}
                            <div className="h-8 w-full bg-base-100 flex justify-center items-center">
                                <div className="w-32 h-1 bg-base-300 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    );
}
