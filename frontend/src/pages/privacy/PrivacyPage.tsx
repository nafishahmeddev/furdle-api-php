import React from "react";
import { Icon } from "@iconify/react";

export default function PrivacyPage() {
  return (
    <div className="min-h-screen bg-gradient-to-br from-base-100 to-base-200 py-12 px-4 sm:px-6 lg:px-8">
      <div className="max-w-4xl mx-auto">

        {/* Header */}
        <div className="text-center mb-12">
          <div className="inline-flex items-center justify-center w-20 h-20 bg-primary/10 rounded-2xl mb-6">
            <Icon icon="hugeicons:shield-02" className="text-4xl text-primary" />
          </div>
          <h1 className="text-4xl font-bold text-base-content mb-4">Privacy Policy</h1>
          <p className="text-lg text-base-content/60 max-w-2xl mx-auto">
            Internal usage guidelines and data protection standards for the Al-Ameen Mission Face Recognition System.
          </p>
          <div className="mt-6 inline-flex items-center px-4 py-2 bg-warning/10 rounded-full border border-warning/20">
            <Icon icon="hugeicons:lock-01" className="text-lg text-warning mr-2" />
            <span className="text-sm font-medium text-warning-content/80">For Internal Use Only</span>
          </div>
        </div>

        {/* Content Card */}
        <div className="bg-base-100 rounded-3xl shadow-xl border border-base-content/5 overflow-hidden">
          <div className="p-8 sm:p-12 space-y-12">

            {/* Section 1: Data Collection */}
            <div className="flex gap-6">
              <div className="flex-shrink-0">
                <div className="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
                  <Icon icon="hugeicons:database-01" className="text-2xl text-primary" />
                </div>
              </div>
              <div>
                <h2 className="text-xl font-bold text-base-content mb-3">1. Information We Collect</h2>
                <p className="text-base-content/70 leading-relaxed mb-4">
                  To provide the functionality of the App, we collect the following types of information:
                </p>
                <ul className="list-disc pl-5 space-y-2 text-base-content/70">
                  <li><strong>Personal Information:</strong> Name, Class, and Form Number (provided by the organization).</li>
                  <li><strong>Biometric Data:</strong> Facial geometry features used strictly for identification purposes.</li>
                  <li><strong>Device Information:</strong> Camera input used in real-time for scanning.</li>
                </ul>
              </div>
            </div>

            {/* Section 2: How We Use Data */}
            <div className="flex gap-6">
              <div className="flex-shrink-0">
                <div className="w-12 h-12 bg-success/10 rounded-xl flex items-center justify-center">
                  <Icon icon="hugeicons:security-check" className="text-2xl text-success" />
                </div>
              </div>
              <div>
                <h2 className="text-xl font-bold text-base-content mb-3">2. How We Use Your Data</h2>
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
            </div>

            {/* Section 3: Data Retention & Deletion (Critical for Play Store) */}
            <div className="flex gap-6">
              <div className="flex-shrink-0">
                <div className="w-12 h-12 bg-warning/10 rounded-xl flex items-center justify-center">
                  <Icon icon="hugeicons:time-quarter-past" className="text-2xl text-warning" />
                </div>
              </div>
              <div>
                <h2 className="text-xl font-bold text-base-content mb-3">3. Data Retention & Deletion</h2>
                <p className="text-base-content/70 leading-relaxed mb-2">
                  <strong>Retention:</strong> Student data is retained only for the duration of the academic session or as long as the student is enrolled in the Al-Ameen Mission.
                </p>
                <p className="text-base-content/70 leading-relaxed">
                  <strong>Deletion:</strong> Users (or their guardians) may request data deletion by contacting the administration. Upon graduation or withdrawal, all biometric data is permanently purged from our systems.
                </p>
              </div>
            </div>

            {/* Section 4: Third Party Disclosure */}
            <div className="flex gap-6">
              <div className="flex-shrink-0">
                <div className="w-12 h-12 bg-error/10 rounded-xl flex items-center justify-center">
                  <Icon icon="hugeicons:unavailable" className="text-2xl text-error" />
                </div>
              </div>
              <div>
                <h2 className="text-xl font-bold text-base-content mb-3">4. Third-Party Disclosure</h2>
                <p className="text-base-content/70 leading-relaxed">
                  We do <span className="font-semibold text-base-content">NOT</span> sell, trade, or transfer your personally identifiable information to outside parties. This is a closed-loop system for internal organizational use only.
                </p>
              </div>
            </div>

            {/* Section 5: Permissions */}
            <div className="flex gap-6">
              <div className="flex-shrink-0">
                <div className="w-12 h-12 bg-info/10 rounded-xl flex items-center justify-center">
                  <Icon icon="hugeicons:camera-01" className="text-2xl text-info" />
                </div>
              </div>
              <div>
                <h2 className="text-xl font-bold text-base-content mb-3">5. App Permissions</h2>
                <p className="text-base-content/70 leading-relaxed">
                  <strong>Camera:</strong> Required to capture facial images for real-time recognition. Images are processed instantly and are not stored in the device gallery.
                </p>
              </div>
            </div>

            {/* Section 6: Contact */}
            <div className="flex gap-6">
              <div className="flex-shrink-0">
                <div className="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
                  <Icon icon="hugeicons:mail-01" className="text-2xl text-primary" />
                </div>
              </div>
              <div>
                <h2 className="text-xl font-bold text-base-content mb-3">6. Contact Us</h2>
                <p className="text-base-content/70 leading-relaxed">
                  For any privacy-related questions or data deletion requests, please contact:
                </p>
                <p className="mt-2 text-primary font-medium">admin@alameenmission.org</p>
              </div>
            </div>

          </div>

          {/* Footer of Card */}
          <div className="bg-base-200/50 p-6 sm:p-8 border-t border-base-content/5">
            <p className="text-sm text-center text-base-content/50">
              Last Updated: {new Date().toLocaleDateString()}
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}
