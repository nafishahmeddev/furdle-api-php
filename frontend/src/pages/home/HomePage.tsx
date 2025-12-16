import React from 'react';
import { Icon } from '@iconify/react';

export default function HomePage() {
  return (
    <div className="min-h-screen bg-base-100">
      {/* Header */}
      <header className="bg-primary text-primary-content px-6 py-12">
        <div className="max-w-4xl mx-auto text-center">
          <div className="w-16 h-16 bg-primary-content/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <Icon icon="hugeicons:camera-01" className="text-3xl text-primary-content" />
          </div>
          <h1 className="text-4xl font-bold mb-2">Welcome to Al-Ameen Face</h1>
          <p className="text-primary-content/80 text-lg">Secure biometric registration system</p>
        </div>
      </header>

      {/* Main Content */}
      <main className="max-w-4xl mx-auto px-6 py-12">
        <div className="text-center">
          <p className="text-base-content/70 text-lg leading-relaxed max-w-2xl mx-auto">
            Experience seamless and secure face registration with our advanced biometric technology.
            Join thousands of users who trust our system for reliable identity verification.
          </p>
        </div>
      </main>

      {/* Footer */}
      <footer className="bg-base-200 border-t border-base-300 px-6 py-8">
        <div className="max-w-4xl mx-auto text-center">
          <p className="text-base-content/50 text-sm">
            Powered by <span className="font-semibold text-primary">Furdle</span>
          </p>
        </div>
      </footer>
    </div>
  );
}