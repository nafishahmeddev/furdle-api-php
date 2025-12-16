import React from 'react';
import { Icon } from '@iconify/react';

export default function HomePage() {
  return (
    <div className="min-h-screen bg-base-100 flex flex-col">
      {/* Header */}
      <header className="bg-primary text-primary-content px-6 py-16 flex-shrink-0">
        <div className="max-w-4xl mx-auto text-center">
          <div className="w-20 h-20 bg-primary-content/20 rounded-full flex items-center justify-center mx-auto mb-6">
            <Icon icon="hugeicons:camera-01" className="text-4xl text-primary-content" />
          </div>
          <h1 className="text-5xl font-bold mb-4">Al-Ameen Face</h1>
          <p className="text-primary-content/90 text-xl max-w-2xl mx-auto leading-relaxed">
            Advanced biometric registration system for secure identity verification
          </p>
        </div>
      </header>

      {/* Main Content */}
      <main className="flex-1 flex items-center justify-center px-6 py-16">
        <div className="text-center max-w-3xl">
          <div className="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-8">
            <Icon icon="hugeicons:face-id" className="text-3xl text-primary" />
          </div>
          <h2 className="text-3xl font-semibold text-base-content mb-6">
            Welcome to Secure Face Registration
          </h2>
          <p className="text-base-content/70 text-lg leading-relaxed mb-8">
            Experience seamless and secure face registration with our advanced biometric technology.
            Join thousands of users who trust our system for reliable identity verification.
          </p>
          <div className="flex justify-center space-x-8 text-sm text-base-content/50">
            <div className="flex items-center">
              <Icon icon="hugeicons:shield-01" className="text-lg mr-2" />
              Secure
            </div>
            <div className="flex items-center">
              <Icon icon="hugeicons:zap-01" className="text-lg mr-2" />
              Fast
            </div>
            <div className="flex items-center">
              <Icon icon="hugeicons:checkmark-circle-01" className="text-lg mr-2" />
              Reliable
            </div>
          </div>
        </div>
      </main>

      {/* Footer */}
      <footer className="bg-base-200 border-t border-base-300 px-6 py-6 flex-shrink-0">
        <div className="max-w-4xl mx-auto text-center">
          <p className="text-base-content/50 text-sm">
            Powered by <span className="font-semibold text-primary">Furdle</span>
          </p>
        </div>
      </footer>
    </div>
  );
}