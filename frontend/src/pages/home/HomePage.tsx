import { Icon } from '@iconify/react';

export default function HomePage() {
  return (
    <div className="min-h-screen bg-gradient-to-br from-base-100 to-base-200">
      {/* Hero Section */}
      <section className="relative overflow-hidden">
        <div className="absolute inset-0 bg-primary/5"></div>
        <div className="relative max-w-6xl mx-auto px-6 py-20">
          <div className="text-center">
            <div className="inline-flex items-center justify-center w-24 h-24 bg-primary/10 rounded-full mb-8">
              <Icon icon="hugeicons:camera-01" className="text-5xl text-primary" />
            </div>
            <h1 className="text-6xl font-bold text-base-content mb-6 leading-tight">
              Al-Ameen
              <span className="block text-primary">Face Recognition</span>
            </h1>
            <p className="text-xl text-base-content/70 max-w-3xl mx-auto leading-relaxed mb-8">
              Revolutionizing identity verification with cutting-edge biometric technology.
              Secure, fast, and reliable face registration for modern institutions.
            </p>

            <div className="flex flex-wrap justify-center gap-6 text-base-content/60">
              <div className="flex items-center bg-base-100/50 px-4 py-2 rounded-full">
                <Icon icon="hugeicons:shield-01" className="text-2xl mr-3 text-success" />
                <span className="font-medium">Bank-Level Security</span>
              </div>
              <div className="flex items-center bg-base-100/50 px-4 py-2 rounded-full">
                <Icon icon="hugeicons:zap" className="text-2xl mr-3 text-warning" />
                <span className="font-medium">Lightning Fast</span>
              </div>
              <div className="flex items-center bg-base-100/50 px-4 py-2 rounded-full">
                <Icon icon="hugeicons:checkmark-circle-01" className="text-2xl mr-3 text-info" />
                <span className="font-medium">99.9% Accuracy</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Features Grid */}
      <section className="py-16 px-6">
        <div className="max-w-6xl mx-auto">
          <div className="text-center mb-16">
            <h2 className="text-4xl font-bold text-base-content mb-4">
              Why Choose Al-Ameen Face?
            </h2>
            <p className="text-lg text-base-content/70 max-w-2xl mx-auto">
              Experience the future of biometric authentication with our comprehensive solution
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div className="bg-base-100 rounded-2xl p-8 border border-base-300 hover:shadow-lg transition-shadow">
              <div className="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6">
                <Icon icon="hugeicons:face-id" className="text-3xl text-primary" />
              </div>
              <h3 className="text-xl font-semibold text-base-content mb-3">Advanced Recognition</h3>
              <p className="text-base-content/70 leading-relaxed">
                State-of-the-art facial recognition algorithms ensure precise identification with minimal false positives.
              </p>
            </div>

            <div className="bg-base-100 rounded-2xl p-8 border border-base-300 hover:shadow-lg transition-shadow">
              <div className="w-14 h-14 bg-success/10 rounded-xl flex items-center justify-center mb-6">
                <Icon icon="hugeicons:ai-lock" className="text-3xl text-success" />
              </div>
              <h3 className="text-xl font-semibold text-base-content mb-3">Enterprise Security</h3>
              <p className="text-base-content/70 leading-relaxed">
                Military-grade encryption and secure data handling protect sensitive biometric information.
              </p>
            </div>

            <div className="bg-base-100 rounded-2xl p-8 border border-base-300 hover:shadow-lg transition-shadow">
              <div className="w-14 h-14 bg-info/10 rounded-xl flex items-center justify-center mb-6">
                <Icon icon="hugeicons:dashboard-speed-01" className="text-3xl text-info" />
              </div>
              <h3 className="text-xl font-semibold text-base-content mb-3">Real-Time Processing</h3>
              <p className="text-base-content/70 leading-relaxed">
                Instant verification results with sub-second processing times for seamless user experience.
              </p>
            </div>

            <div className="bg-base-100 rounded-2xl p-8 border border-base-300 hover:shadow-lg transition-shadow">
              <div className="w-14 h-14 bg-warning/10 rounded-xl flex items-center justify-center mb-6">
                <Icon icon="hugeicons:settings-01" className="text-3xl text-warning" />
              </div>
              <h3 className="text-xl font-semibold text-base-content mb-3">Easy Integration</h3>
              <p className="text-base-content/70 leading-relaxed">
                RESTful APIs and comprehensive documentation make integration with existing systems effortless.
              </p>
            </div>

            <div className="bg-base-100 rounded-2xl p-8 border border-base-300 hover:shadow-lg transition-shadow">
              <div className="w-14 h-14 bg-error/10 rounded-xl flex items-center justify-center mb-6">
                <Icon icon="hugeicons:user-multiple-02" className="text-3xl text-error" />
              </div>
              <h3 className="text-xl font-semibold text-base-content mb-3">Multi-User Support</h3>
              <p className="text-base-content/70 leading-relaxed">
                Handle thousands of concurrent users with our scalable architecture and cloud infrastructure.
              </p>
            </div>

            <div className="bg-base-100 rounded-2xl p-8 border border-base-300 hover:shadow-lg transition-shadow">
              <div className="w-14 h-14 bg-secondary/10 rounded-xl flex items-center justify-center mb-6">
                <Icon icon="hugeicons:analytics-01" className="text-3xl text-secondary" />
              </div>
              <h3 className="text-xl font-semibold text-base-content mb-3">Analytics Dashboard</h3>
              <p className="text-base-content/70 leading-relaxed">
                Comprehensive reporting and analytics to monitor system performance and user engagement.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-base-100 border-t border-base-300 px-6 py-8">
        <div className="max-w-6xl mx-auto text-center">
          <div className="flex justify-center items-center mb-4">
            <Icon icon="hugeicons:camera-01" className="text-2xl text-primary mr-2" />
            <span className="font-bold text-lg text-base-content">Al-Ameen Face</span>
          </div>
          <p className="text-base-content/50 text-sm mb-4">
            Powered by <span className="font-semibold text-primary">Furdle</span> • Advanced Biometric Solutions
          </p>
          <p className="text-base-content/40 text-xs">
            © 2025 Al-Ameen Face Recognition System. All rights reserved.
          </p>
        </div>
      </footer>
    </div>
  );
}