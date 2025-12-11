import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { useState, useEffect, useRef } from 'react';
import { Icon } from '@iconify/react';
import { apiService } from './utils/api';

// Utility function to convert data URL to File
const dataURLtoFile = (dataurl: string, filename: string): File => {
  const arr = dataurl.split(',');
  const mime = arr[0].match(/:(.*?);/)![1];
  const bstr = atob(arr[1]);
  let n = bstr.length;
  const u8arr = new Uint8Array(n);
  while (n--) {
    u8arr[n] = bstr.charCodeAt(n);
  }
  return new File([u8arr], filename, { type: mime });
};

function App() {
  const query = new URLSearchParams(window.location.search);
  const formNo: string = query.get('form_no') || '';
  const session: string = query.get('session') || '';
  const queryClient = useQueryClient();

  const [iframeHeight, setIframeHeight] = useState('400px');
  const iframeRef = useRef<HTMLIFrameElement>(null);

  const lookupQuery = useQuery({
    queryKey: ['third-party-lookup', formNo, session],
    queryFn: () => apiService.thirdPartyLookup(formNo, session),
    enabled: !!formNo && !!session,
  });

  const faceQuery = useQuery({
    queryKey: ['faces', lookupQuery.data?.result.student.form_no],
    queryFn: () => {
      if (!lookupQuery.data) throw new Error('Lookup data not available');
      const query = {
        ...(lookupQuery.data.result.query || {}),
        code: lookupQuery.data.result.student.form_no,
      };
      return apiService.searchFaces(
        lookupQuery.data.result.url,
        lookupQuery.data.result.token,
        query
      );
    },
    enabled: !!lookupQuery.data,
  });

  const deleteMutation = useMutation({
    mutationFn: async (faceId: number) => {
      if (!lookupQuery.data) throw new Error('Lookup data not available');
      return apiService.deleteFace(
        lookupQuery.data.result.url,
        lookupQuery.data.result.token,
        faceId
      );
    },
    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: ['faces', lookupQuery.data?.result.student.form_no]
      });
    },
  });

  const registerMutation = useMutation({
    mutationFn: async (image: string) => {
      if (!lookupQuery.data) throw new Error('Lookup data not available');

      const file = dataURLtoFile(image, 'face.jpg');
      return apiService.registerFace(
        lookupQuery.data.result.url,
        lookupQuery.data.result.token,
        file,
        lookupQuery.data.result.payload || {},
        lookupQuery.data.result.query || {}
      );
    },
  });

  useEffect(() => {
    const handleMessage = async (event: MessageEvent) => {
      if (event.origin === 'https://face.nafish.me') {
        if (event.data.type === 'resize' && event.data.height) {
          setIframeHeight(event.data.height + 'px');
        } else if (event.data.type === 'face-confirmed' && event.data.payload && event.data.payload.image) {
          if (!lookupQuery.data) return;
          registerMutation.mutate(event.data.payload.image);
        }
      }
    };
    window.addEventListener('message', handleMessage);
    return () => window.removeEventListener('message', handleMessage);
  }, [lookupQuery.data, registerMutation]);

  useEffect(() => {
    const interval = setInterval(() => {
      iframeRef.current?.contentWindow?.postMessage(
        { type: 'getHeight' },
        '*'
      );
    }, 1000);
    return () => clearInterval(interval);
  }, []);


  // Early return for loading state
  if (!formNo || !session) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-linear-to-br from-red-50 to-orange-50">
        <div className="text-center max-w-md px-6">
          <div className="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <Icon icon="mdi:alert-circle" style={{ fontSize: '2rem', color: '#dc2626' }} />
          </div>
          <h2 className="text-xl font-bold text-gray-900 mb-2">Invalid Access</h2>
          <p className="text-gray-600">Form number and session are required to access this page.</p>
        </div>
      </div>
    );
  }

  if (lookupQuery.isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-linear-to-br from-indigo-50 to-blue-50">
        <div className="text-center">
          <div className="w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mx-auto mb-4"></div>
          <p className="text-gray-600">Loading...</p>
        </div>
      </div>
    );
  }


  if (!lookupQuery.data) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-linear-to-br from-red-50 to-orange-50">
        <div className="text-center max-w-md px-6">
          <div className="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <Icon icon="mdi:close-circle" style={{ fontSize: '2rem', color: '#dc2626' }} />
          </div>
          <h2 className="text-xl font-bold text-gray-900 mb-2">Access Denied</h2>
          <p className="text-gray-600">{'Unable to verify your session.'}</p>
        </div>
      </div>
    );
  }


  if (registerMutation.isSuccess) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-linear-to-br from-green-50 to-emerald-50 px-4">
        <div className="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center">
          <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <Icon icon="mdi:check-circle" style={{ fontSize: '2.5rem', color: '#16a34a' }} />
          </div>
          <h2 className="text-2xl font-bold text-gray-900 mb-3">Registration Successful!</h2>
          <p className="text-gray-600 leading-relaxed">Your face has been successfully registered in our system.</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-linear-to-br from-slate-50 to-gray-100">
      <div className="max-w-md mx-auto bg-white shadow-xl min-h-screen">
        {/* Header */}
        <header className="bg-linear-to-r from-indigo-600 to-purple-600 text-white px-6 py-8 relative overflow-hidden">
          <div className="absolute inset-0 bg-black opacity-10"></div>
          <div className="relative z-10">
            <div className="flex items-center mb-2">
              <div className="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3">
                <Icon icon="mdi:camera" style={{ fontSize: '1.25rem', color: 'white' }} />
              </div>
              <h1 className="text-2xl font-bold">Face Registration</h1>
            </div>
            <p className="text-indigo-100 text-sm leading-relaxed">Complete your biometric registration securely</p>
          </div>
        </header>

        {/* Loading State */}
        {registerMutation.isPending && (
          <div className="px-6 py-4 bg-blue-50 border-b border-blue-200">
            <div className="flex items-center">
              <div className="w-5 h-5 border-2 border-blue-200 border-t-blue-600 rounded-full animate-spin mr-3"></div>
              <p className="text-sm text-blue-700 font-medium">Submitting face data...</p>
            </div>
          </div>
        )}

        {/* Error States */}
        {registerMutation.isError && (
          <div className="px-6 py-4 bg-red-50 border-b border-red-200">
            <div className="flex items-center">
              <Icon icon="mdi:alert-circle" style={{ fontSize: '1.25rem', color: '#dc2626' }} className="mr-3" />
              <p className="text-sm text-red-700">Failed to register face. Please try again.</p>
            </div>
          </div>
        )}

        {deleteMutation.isError && (
          <div className="px-6 py-4 bg-red-50 border-b border-red-200">
            <div className="flex items-center">
              <Icon icon="mdi:alert-circle" style={{ fontSize: '1.25rem', color: '#dc2626' }} className="mr-3" />
              <p className="text-sm text-red-700">Failed to delete existing face. Please try again.</p>
            </div>
          </div>
        )}

        {/* Main Content */}
        <main className="px-6 py-6 space-y-6">
          {/* Student Info Card */}
          <div className="bg-white border border-gray-200 rounded-2xl p-5">
            <div className="flex items-center space-x-4">
              <div className="relative">
                <img
                  src={lookupQuery.data.result.student.image}
                  alt="Student Photo"
                  className="w-16 h-16 rounded-full object-cover border-2 border-indigo-200"
                />
                <div className="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full border-2 border-white flex items-center justify-center">
                  <Icon icon="mdi:check" style={{ fontSize: '0.75rem', color: 'white' }} />
                </div>
              </div>
              <div className="flex-1">
                <h3 className="font-semibold text-gray-900 text-lg mb-1">
                  {lookupQuery.data.result.student.student_name}
                </h3>
                <div className="space-y-1">
                  <div className="flex items-center text-sm text-gray-600">
                    <Icon icon="mdi:file-document" style={{ fontSize: '1rem', color: '#9ca3af' }} className="mr-2" />
                    Form: {lookupQuery.data.result.student.form_no}
                  </div>
                  <div className="flex items-center text-sm text-gray-600">
                    <Icon icon="mdi:school" style={{ fontSize: '1rem', color: '#9ca3af' }} className="mr-2" />
                    Class: {lookupQuery.data.result.student.class_name}
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Face Registration Section */}
          {faceQuery.data && faceQuery.data.result && faceQuery.data.result.records.length > 0 ? (
            <div className="bg-yellow-50 border border-yellow-200 rounded-2xl p-5">
              <div className="flex items-start mb-3">
                <Icon icon="mdi:alert" style={{ fontSize: '1.25rem', color: '#d97706' }} className="mr-3 mt-0.5" />
                <h4 className="text-sm font-bold text-yellow-800">Face Already Registered</h4>
              </div>
              <p className="text-sm text-yellow-700 mb-4 leading-relaxed">
                A face matching your biometric data has already been registered. If you believe this is an error, you can delete the existing data and register a new one.
              </p>
              <button
                onClick={() => {
                  const records = faceQuery.data!.result.records;
                  for (const record of records) {
                    deleteMutation.mutate(record.id);
                  }
                }}
                disabled={deleteMutation.isPending}
                className="w-full bg-yellow-600 text-white px-4 py-3 rounded-xl hover:bg-yellow-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium"
              >
                {deleteMutation.isPending ? (
                  <div className="flex items-center justify-center">
                    <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                    Deleting...
                  </div>
                ) : (
                  'Delete Existing Face & Register New'
                )}
              </button>
            </div>
          ) : (
            <>
              {/* Face Capture */}
              <div className="bg-white border-2 border-dashed border-gray-300 rounded-2xl p-4">
                <div>
                  <iframe
                    id="captureFrame"
                    ref={iframeRef}
                    src="https://face.nafish.me/frame/capture"
                    className="w-full border-0"
                    style={{ height: iframeHeight }}
                    onLoad={() => {
                      iframeRef.current?.contentWindow?.postMessage(
                        { type: 'getHeight' },
                        '*'
                      );
                    }}
                    allow="camera; microphone; autoplay"
                  />
                </div>
              </div>

              {/* Instructions */}
              <div className="bg-red-50 border border-red-200 rounded-2xl p-5">
                <div className="flex items-center mb-3">
                  <Icon icon="mdi:alert" style={{ fontSize: '1.25rem', color: '#dc2626' }} className="mr-3" />
                  <h4 className="text-sm font-bold text-red-800">Important Instructions</h4>
                </div>
                <ul className="text-sm text-red-700 space-y-2">
                  <li className="flex items-start">
                    <Icon icon="mdi:circle-outline" style={{ fontSize: '1rem', color: '#dc2626' }} className="mr-3 mt-0.5 shrink-0" />
                    Ensure you are in a well-lit area
                  </li>
                  <li className="flex items-start">
                    <Icon icon="mdi:circle-outline" style={{ fontSize: '1rem', color: '#dc2626' }} className="mr-3 mt-0.5 shrink-0" />
                    Position your face within the capture frame
                  </li>
                  <li className="flex items-start">
                    <Icon icon="mdi:circle-outline" style={{ fontSize: '1rem', color: '#dc2626' }} className="mr-3 mt-0.5 shrink-0" />
                    Remove hats, sunglasses, or anything obscuring your face
                  </li>
                  <li className="flex items-start">
                    <Icon icon="mdi:circle-outline" style={{ fontSize: '1rem', color: '#dc2626' }} className="mr-3 mt-0.5 shrink-0" />
                    Follow the on-screen prompts to complete capture
                  </li>
                </ul>
              </div>
            </>
          )}
        </main>
      </div>
    </div>
  );
}

export default App
