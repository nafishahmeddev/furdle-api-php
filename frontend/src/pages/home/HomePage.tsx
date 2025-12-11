import { Icon } from '@iconify/react';

export function HomePage() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-linear-to-br from-red-50 to-orange-50">
      <div className="text-center max-w-md px-6">
        <div className="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <Icon icon="mdi:close-circle" style={{ fontSize: '2rem', color: '#dc2626' }} />
        </div>
        <h2 className="text-xl font-bold text-gray-900 mb-2">Not Allowed</h2>
        <p className="text-gray-600">You do not have permission to access this page.</p>
      </div>
    </div>
  );
}