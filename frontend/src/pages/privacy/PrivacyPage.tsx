import { Icon } from "@iconify/react";

export default function PrivacyPage() {
  return (
    <div className="min-h-screen bg-base-100 py-12 ">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {/* Simple Header */}
        <div className="mb-12 border-b border-base-200 pb-8">
          <h1 className="text-3xl font-bold text-base-content mb-2">Privacy Policy</h1>
          <p className="text-base-content/60">
            Last Updated: {new Date().toLocaleDateString()}
          </p>
        </div>

        {/* Content - No Card */}
        <div className="space-y-12">

          {/* Section 1: Data Collection */}
          <section>
            <div className="flex items-center gap-3 mb-4">
              <Icon icon="hugeicons:database-01" className="text-2xl text-primary" />
              <h2 className="text-xl font-bold text-base-content">1. Information We Collect</h2>
            </div>
            <div className="pl-9">
              <p className="text-base-content/70 leading-relaxed mb-4">
                To provide the functionality of the App, we collect the following types of information:
              </p>
              <ul className="list-disc pl-5 space-y-2 text-base-content/70">
                <li><strong>Personal Information:</strong> Name, Class, and Form Number (provided by the organization).</li>
                <li><strong>Biometric Data:</strong> Facial geometry features used strictly for identification purposes.</li>
                <li><strong>Device Information:</strong> Camera input used in real-time for scanning.</li>
              </ul>
            </div>
          </section>

          {/* Section 2: How We Use Data */}
          <section>
            <div className="flex items-center gap-3 mb-4">
              <Icon icon="hugeicons:security-check" className="text-2xl text-success" />
              <h2 className="text-xl font-bold text-base-content">2. How We Use Your Data</h2>
            </div>
            <div className="pl-9">
              <p className="text-base-content/70 leading-relaxed">
                The collected data is used exclusively for:
              </p>
              <ul className="mt-3 space-y-2 text-base-content/70">
                <li className="flex items-center">
                  <Icon icon="hugeicons:checkmark-circle-01" className="text-success text-lg mr-2" />
                  Verifying student identity for internal attendance.
                </li>
                <li className="flex items-center">
                  <Icon icon="hugeicons:checkmark-circle-01" className="text-success text-lg mr-2" />
                  Preventing unauthorized access to restricted areas.
                </li>
              </ul>
            </div>
          </section>

          {/* Section 3: Data Retention & Deletion */}
          <section>
            <div className="flex items-center gap-3 mb-4">
              <Icon icon="hugeicons:time-quarter-past" className="text-2xl text-warning" />
              <h2 className="text-xl font-bold text-base-content">3. Data Retention & Deletion</h2>
            </div>
            <div className="pl-9">
              <p className="text-base-content/70 leading-relaxed mb-2">
                <strong>Retention:</strong> Student data is retained only for the duration of the academic session or as long as the student is enrolled in the Al-Ameen Mission.
              </p>
              <p className="text-base-content/70 leading-relaxed">
                <strong>Deletion:</strong> Users (or their guardians) may request data deletion by contacting the administration. Upon graduation or withdrawal, all biometric data is permanently purged from our systems.
              </p>
            </div>
          </section>

          {/* Section 4: Third Party Disclosure */}
          <section>
            <div className="flex items-center gap-3 mb-4">
              <Icon icon="hugeicons:unavailable" className="text-2xl text-error" />
              <h2 className="text-xl font-bold text-base-content">4. Third-Party Disclosure</h2>
            </div>
            <div className="pl-9">
              <p className="text-base-content/70 leading-relaxed">
                We do <span className="font-semibold text-base-content">NOT</span> sell, trade, or transfer your personally identifiable information to outside parties. This is a closed-loop system for internal organizational use only.
              </p>
            </div>
          </section>

          {/* Section 5: Permissions */}
          <section>
            <div className="flex items-center gap-3 mb-4">
              <Icon icon="hugeicons:camera-01" className="text-2xl text-info" />
              <h2 className="text-xl font-bold text-base-content">5. App Permissions</h2>
            </div>
            <div className="pl-9">
              <p className="text-base-content/70 leading-relaxed">
                <strong>Camera:</strong> Required to capture facial images for real-time recognition. Images are processed instantly and are not stored in the device gallery.
              </p>
            </div>
          </section>

          {/* Section 6: Contact */}
          <section>
            <div className="flex items-center gap-3 mb-4">
              <Icon icon="hugeicons:mail-01" className="text-2xl text-primary" />
              <h2 className="text-xl font-bold text-base-content">6. Contact Us</h2>
            </div>
            <div className="pl-9">
              <p className="text-base-content/70 leading-relaxed">
                For any privacy-related questions or data deletion requests, please contact:
              </p>
              <p className="mt-2 text-primary font-medium">admin@alameenmission.org</p>
            </div>
          </section>

        </div>
      </div>
    </div>
  );
}
