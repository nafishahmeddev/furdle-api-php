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
              <span className="block text-primary">Furdle</span>
            </h1>
            <p className="text-xl text-base-content/70 max-w-3xl mx-auto leading-relaxed mb-8">
              Internal identity verification system for student attendance and access control.
              Secure and efficient face registration for authorized use.
            </p>

            <div className="flex flex-wrap justify-center gap-6 text-base-content/60">
              <div className="flex items-center bg-base-100/50 px-4 py-2 rounded-full">
                <Icon icon="hugeicons:shield-01" className="text-2xl mr-3 text-success" />
                <span className="font-medium">Secure Data</span>
              </div>
              <div className="flex items-center bg-base-100/50 px-4 py-2 rounded-full">
                <Icon icon="hugeicons:zap" className="text-2xl mr-3 text-warning" />
                <span className="font-medium">High Speed</span>
              </div>
              <div className="flex items-center bg-base-100/50 px-4 py-2 rounded-full">
                <Icon icon="hugeicons:checkmark-circle-01" className="text-2xl mr-3 text-info" />
                <span className="font-medium">High Accuracy</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Features Grid */}
      <section className="py-16 px-6">
        <div className="max-w-6xl mx-auto">
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div className="bg-base-100 rounded-2xl p-8 border border-base-300 hover:shadow-lg transition-shadow">
              <div className="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6">
                <Icon icon="hugeicons:face-id" className="text-3xl text-primary" />
              </div>
              <h3 className="text-xl font-semibold text-base-content mb-3">Advanced Recognition</h3>
              <p className="text-base-content/70 leading-relaxed">
                State-of-the-art facial recognition algorithms ensure precise identification.
              </p>
            </div>

            <div className="bg-base-100 rounded-2xl p-8 border border-base-300 hover:shadow-lg transition-shadow">
              <div className="w-14 h-14 bg-info/10 rounded-xl flex items-center justify-center mb-6">
                <Icon icon="hugeicons:dashboard-speed-01" className="text-3xl text-info" />
              </div>
              <h3 className="text-xl font-semibold text-base-content mb-3">Real-Time Processing</h3>
              <p className="text-base-content/70 leading-relaxed">
                Instant verification results for seamless student attendance tracking.
              </p>
            </div>

            <div className="bg-base-100 rounded-2xl p-8 border border-base-300 hover:shadow-lg transition-shadow">
              <div className="w-14 h-14 bg-error/10 rounded-xl flex items-center justify-center mb-6">
                <Icon icon="hugeicons:user-multiple-02" className="text-3xl text-error" />
              </div>
              <h3 className="text-xl font-semibold text-base-content mb-3">Student Database</h3>
              <p className="text-base-content/70 leading-relaxed">
                Securely linked to the internal student records for accurate verification.
              </p>
            </div>
          </div>
        </div>
      </section>


    </div>
  );
}