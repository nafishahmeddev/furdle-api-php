import { Icon } from '@iconify/react';
export default function DownloadPage() {
    return (
        <div className="min-h-screen bg-base-100 flex flex-col items-center justify-center p-4">
            <div className="max-w-md w-full text-center space-y-8">
                <h1 className="text-4xl font-bold text-primary">Download Our App</h1>
                <p className="text-lg text-base-content/80">
                    Get the best experience by downloading our mobile application.
                </p>

                <div className="flex flex-col gap-4">
                    <button className="btn btn-primary btn-lg w-full">
                        <Icon icon="mdi:google-play" className="text-2xl" />
                        Download for Android
                    </button>
                    <button className="btn btn-outline btn-lg w-full">
                        <Icon icon="mdi:apple" className="text-2xl" />
                        Download for iOS
                    </button>
                </div>

                <div className="divider">OR</div>

                <p className="text-sm text-base-content/60">
                    Scan the QR code to download directly
                </p>

                {/* Placeholder for QR Code */}
                <div className="w-32 h-32 bg-base-200 mx-auto rounded-lg flex items-center justify-center">
                    <span className="text-xs text-base-content/40">QR Code</span>
                </div>
            </div>
        </div>
    );
}
