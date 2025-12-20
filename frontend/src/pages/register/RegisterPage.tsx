import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { useState, useEffect, useRef } from 'react';
import { Icon } from '@iconify/react';
import { apiService } from '../../utils/api';

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

function RegisterPage() {
  const query = new URLSearchParams(window.location.search);
  const formNo: string = query.get('form_no') || '';
  const session: string = query.get('session') || '';
  const redirect: string = query.get('redirect') || '';
  const queryClient = useQueryClient();

  const [iframeHeight, setIframeHeight] = useState('400px');
  const [showDeleteModal, setShowDeleteModal] = useState(false);
  const iframeRef = useRef<HTMLIFrameElement>(null);
  const deleteModalRef = useRef<HTMLDialogElement>(null);

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
        lookupQuery.data.result.query || {},
        lookupQuery.data.result.uquery || {}
      );
    },
  });

  useEffect(() => {
    const handleMessage = async (event: MessageEvent) => {
      if (event.origin === 'https://idexa.app') {
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

  useEffect(() => {
    if (registerMutation.isSuccess && redirect) {
      setTimeout(() => {
        window.location.href = redirect;
      }, 3000);
    }
  }, [registerMutation.isSuccess, redirect]);

  useEffect(() => {
    if (showDeleteModal && deleteModalRef.current) {
      deleteModalRef.current.showModal();
    } else if (deleteModalRef.current) {
      deleteModalRef.current.close();
    }
  }, [showDeleteModal]);


  // Early return for loading state
  if (!formNo || !session) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-base-100 px-4">
        <div className="text-center max-w-md max-sm:max-w-sm w-full">
          <div className="bg-base-100 rounded-2xl max-sm:rounded-xl shadow-lg border border-base-300 p-8 max-sm:p-6">
            <div className="w-20 h-20 max-sm:w-16 max-sm:h-16 bg-warning/10 rounded-full flex items-center justify-center mx-auto mb-6 max-sm:mb-4">
              <Icon icon="hugeicons:alert-circle" className="text-4xl max-sm:text-3xl text-warning" />
            </div>
            <h2 className="text-xl max-sm:text-lg font-bold text-base-content mb-4 max-sm:mb-2">Invalid Access</h2>
            <p className="text-base max-sm:text-sm text-base-content/70">Form number and session are required to access this page.</p>
          </div>
        </div>
      </div>
    );
  }

  if (lookupQuery.isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-base-100 to-base-200 px-4">
        <div className="text-center max-w-md w-full">
          <div className="bg-base-100 rounded-2xl shadow-lg border border-base-300 p-8">
            <div className="loading loading-spinner loading-lg text-primary mx-auto mb-6"></div>
            <h2 className="text-xl font-semibold text-base-content mb-2">Loading...</h2>
            <p className="text-base-content/70">Please wait while we verify your session</p>
          </div>
        </div>
      </div>
    );
  }


  if (!lookupQuery.data) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-base-100 to-base-200 px-4">
        <div className="text-center max-w-md max-sm:max-w-sm w-full">
          <div className="bg-base-100 rounded-2xl max-sm:rounded-xl shadow-lg border border-base-300 p-8 max-sm:p-6">
            <div className="w-20 h-20 max-sm:w-16 max-sm:h-16 bg-error/10 rounded-full flex items-center justify-center mx-auto mb-6 max-sm:mb-4">
              <Icon icon="hugeicons:close-circle" className="text-4xl max-sm:text-3xl text-error" />
            </div>
            <h2 className="text-xl max-sm:text-lg font-bold text-base-content mb-4 max-sm:mb-2">Access Denied</h2>
            <p className="text-base max-sm:text-sm text-base-content/70 leading-relaxed">Unable to verify your session. Please check your credentials and try again.</p>
          </div>
        </div>
      </div>
    );
  }


  return (
    <div className="min-h-screen bg-gradient-to-br from-base-100 to-base-200">
      {/* Hero Section */}
      <section className="relative overflow-hidden bg-primary/5">
        <div className="absolute inset-0 bg-gradient-to-r from-primary/10 to-primary/5"></div>
        <div className="relative max-w-4xl mx-auto px-6 py-8 max-lg:px-4 max-lg:py-6 max-sm:px-4 max-sm:py-4">
          <div className="text-center">
            <div className="inline-flex items-center justify-center w-12 h-12 max-sm:w-10 max-sm:h-10 bg-primary/10 rounded-full mb-3 max-sm:mb-2">
              <Icon icon="hugeicons:face-id" className="text-2xl max-sm:text-xl text-primary" />
            </div>
            <h1 className="text-2xl max-sm:text-xl font-bold text-base-content mb-2 max-sm:mb-1 leading-tight">
              Face Registration
            </h1>
            <p className="text-sm max-sm:text-xs text-base-content/60">
              Complete your biometric registration securely
            </p>
          </div>
        </div>
      </section>

      {/* Main Content */}
      <section className="py-12 max-sm:py-8 px-6 max-sm:px-4">
        <div className="max-w-4xl mx-auto space-y-8 max-sm:space-y-6">

          {/* Error States */}
          {registerMutation.isError && (
            <div className="bg-error/5 border border-error/20 rounded-2xl p-6 max-sm:p-4">
              <div className="flex items-start">
                <div className="w-12 h-12 max-sm:w-10 max-sm:h-10 bg-error/10 rounded-xl flex items-center justify-center mr-4 max-sm:mr-3 flex-shrink-0">
                  <Icon icon="hugeicons:alert-circle" className="text-2xl max-sm:text-xl text-error" />
                </div>
                <div>
                  <h3 className="text-lg max-sm:text-base font-semibold text-error mb-2 max-sm:mb-1">Registration Failed</h3>
                  <p className="text-sm max-sm:text-xs text-error/80">Please try again or contact support if the issue persists.</p>
                </div>
              </div>
            </div>
          )}

          {deleteMutation.isError && (
            <div className="bg-error/5 border border-error/20 rounded-2xl p-6 max-sm:p-4">
              <div className="flex items-start">
                <div className="w-12 h-12 max-sm:w-10 max-sm:h-10 bg-error/10 rounded-xl flex items-center justify-center mr-4 max-sm:mr-3 flex-shrink-0">
                  <Icon icon="hugeicons:alert-circle" className="text-2xl max-sm:text-xl text-error" />
                </div>
                <div>
                  <h3 className="text-lg max-sm:text-base font-semibold text-error mb-2 max-sm:mb-1">Deletion Failed</h3>
                  <p className="text-sm max-sm:text-xs text-error/80">Unable to delete existing face data. Please try again.</p>
                </div>
              </div>
            </div>
          )}

          {/* Student Info Card */}
          <div className="bg-base-100 rounded-2xl p-8 max-sm:p-6 border border-base-300 shadow-sm hover:shadow-lg transition-shadow">
            <div className="flex items-center mb-6 max-sm:mb-4">
              <div className="w-14 h-14 max-sm:w-12 max-sm:h-12 bg-primary/10 rounded-xl flex items-center justify-center mr-4 max-sm:mr-3">
                <Icon icon="hugeicons:user-01" className="text-3xl max-sm:text-2xl text-primary" />
              </div>
              <div>
                <h2 className="text-2xl max-sm:text-xl font-bold text-base-content">Student Information</h2>
                <p className="text-base-content/60 text-sm max-sm:text-xs">Verified student details</p>
              </div>
            </div>
            <div className="flex items-center space-x-6 max-sm:space-x-4">
              <div className="relative">
                <img
                  src={lookupQuery.data.result.student.image}
                  alt="Student Photo"
                  className="w-24 h-24 max-sm:w-20 max-sm:h-20 rounded-xl object-cover border-3 border-primary/20"
                />
                <div className="absolute -bottom-2 -right-2 w-8 h-8 max-sm:w-6 max-sm:h-6 bg-success rounded-full border-3 border-base-100 flex items-center justify-center">
                  <Icon icon="hugeicons:checkmark" className="text-sm max-sm:text-xs text-success-content" />
                </div>
              </div>
              <div className="flex-1">
                <h3 className="font-bold text-base-content text-xl max-sm:text-lg mb-3 max-sm:mb-2">
                  {lookupQuery.data.result.student.student_name}
                </h3>
                <div className="space-y-2 max-sm:space-y-1.5">
                  <div className="flex items-center text-base-content/70">
                    <Icon icon="hugeicons:file-01" className="text-lg max-sm:text-base mr-3 max-sm:mr-2 text-primary" />
                    <span className="text-sm max-sm:text-xs"><strong>Form:</strong> {lookupQuery.data.result.student.form_no}</span>
                  </div>
                  <div className="flex items-center text-base-content/70">
                    <Icon icon="hugeicons:school" className="text-lg max-sm:text-base mr-3 max-sm:mr-2 text-primary" />
                    <span className="text-sm max-sm:text-xs"><strong>Class:</strong> {lookupQuery.data.result.student.class_name}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Face Registration Section */}
          {faceQuery.data && faceQuery.data.result && faceQuery.data.result.records.length > 0 ? (
            <div className="bg-gradient-to-r from-warning/5 to-warning/10 border border-warning/30 rounded-2xl p-8 max-sm:p-6">
              <div className="flex items-start mb-6 max-sm:mb-4">
                <div className="w-14 h-14 max-sm:w-12 max-sm:h-12 bg-warning/10 rounded-xl flex items-center justify-center mr-4 max-sm:mr-3 flex-shrink-0">
                  <Icon icon="hugeicons:alert-triangle" className="text-3xl max-sm:text-2xl text-warning" />
                </div>
                <div>
                  <h3 className="text-xl max-sm:text-lg font-bold text-warning mb-2 max-sm:mb-1">Face Already Registered</h3>
                  <p className="text-base-content/70 text-sm max-sm:text-xs leading-relaxed">
                    A face matching your biometric data has already been registered. If you believe this is an error, you can delete the existing data and register a new one.
                  </p>
                </div>
              </div>
              <div className="flex justify-end">
                <button
                  onClick={() => setShowDeleteModal(true)}
                  disabled={deleteMutation.isPending}
                  className="btn btn-warning btn-outline btn-md max-sm:btn-sm"
                >
                  {deleteMutation.isPending ? (
                    <>
                      <span className="loading loading-spinner loading-sm"></span>
                      Deleting...
                    </>
                  ) : (
                    <>
                      <Icon icon="hugeicons:delete-01" className="text-base max-sm:text-sm mr-2 max-sm:mr-1" />
                      Delete & Register New
                    </>
                  )}
                </button>
              </div>
            </div>
          ) : (
            <>
              {/* Face Capture Card */}
              <div className="bg-base-100 rounded-2xl p-8 max-sm:p-6 border border-base-300 shadow-sm hover:shadow-lg transition-shadow">
                <div className="flex items-center mb-6 max-sm:mb-4">
                  <div className="w-14 h-14 max-sm:w-12 max-sm:h-12 bg-primary/10 rounded-xl flex items-center justify-center mr-4 max-sm:mr-3">
                    <Icon icon="hugeicons:camera-01" className="text-3xl max-sm:text-2xl text-primary" />
                  </div>
                  <div>
                    <h3 className="text-xl max-sm:text-lg font-bold text-base-content">Face Capture</h3>
                    <p className="text-base-content/60 text-sm max-sm:text-xs">Position your face in the frame below</p>
                  </div>
                </div>
                <div className="rounded-xl overflow-hidden">
                  <iframe
                    id="captureFrame"
                    ref={iframeRef}
                    src="https://idexa.app/frame/capture"
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

              {/* Instructions Card */}
              <div className="bg-gradient-to-r from-info/5 to-info/10 border border-info/30 rounded-2xl p-8 max-sm:p-6">
                <div className="flex items-center mb-6 max-sm:mb-4">
                  <div className="w-14 h-14 max-sm:w-12 max-sm:h-12 bg-info/10 rounded-xl flex items-center justify-center mr-4 max-sm:mr-3">
                    <Icon icon="hugeicons:alert-01" className="text-3xl max-sm:text-2xl text-info" />
                  </div>
                  <div>
                    <h3 className="text-xl max-sm:text-lg font-bold text-info">Important Instructions</h3>
                    <p className="text-base-content/60 text-sm max-sm:text-xs">Follow these steps for best results</p>
                  </div>
                </div>
                <div className="grid gap-4 max-sm:gap-3">
                  <div className="flex items-start bg-base-100/50 rounded-xl p-4 max-sm:p-3">
                    <Icon icon="ph:dot-outline" className="text-xl max-sm:text-lg text-info mr-3 max-sm:mr-2 mt-0.5 flex-shrink-0" />
                    <span className="text-sm max-sm:text-xs text-base-content/80">Ensure you are in a well-lit area with good lighting</span>
                  </div>
                  <div className="flex items-start bg-base-100/50 rounded-xl p-4 max-sm:p-3">
                    <Icon icon="ph:dot-outline" className="text-xl max-sm:text-lg text-info mr-3 max-sm:mr-2 mt-0.5 flex-shrink-0" />
                    <span className="text-sm max-sm:text-xs text-base-content/80">Position your face within the capture frame clearly</span>
                  </div>
                  <div className="flex items-start bg-base-100/50 rounded-xl p-4 max-sm:p-3">
                    <Icon icon="ph:dot-outline" className="text-xl max-sm:text-lg text-info mr-3 max-sm:mr-2 mt-0.5 flex-shrink-0" />
                    <span className="text-sm max-sm:text-xs text-base-content/80">Remove hats, sunglasses, or anything obscuring your face</span>
                  </div>
                  <div className="flex items-start bg-base-100/50 rounded-xl p-4 max-sm:p-3">
                    <Icon icon="ph:dot-outline" className="text-xl max-sm:text-lg text-info mr-3 max-sm:mr-2 mt-0.5 flex-shrink-0" />
                    <span className="text-sm max-sm:text-xs text-base-content/80">Follow the on-screen prompts to complete capture</span>
                  </div>
                </div>
              </div>
            </>
          )}
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-base-100 border-t border-base-300 px-6 max-sm:px-4 py-8 max-sm:py-6">
        <div className="max-w-4xl mx-auto text-center">
          <div className="flex justify-center items-center mb-4 max-sm:mb-3">
            <Icon icon="hugeicons:camera-01" className="text-2xl max-sm:text-xl text-primary mr-2" />
            <span className="font-bold text-lg max-sm:text-base text-base-content">Al-Ameen Face</span>
          </div>
          <p className="text-base-content/50 text-sm max-sm:text-xs mb-2 max-sm:mb-1">
            Powered by <span className="font-semibold text-primary">Furdle</span> • Advanced Biometric Solutions
          </p>
          <p className="text-base-content/40 text-xs max-sm:text-[10px]">
            © 2025 Al-Ameen Face Recognition System. All rights reserved.
          </p>
        </div>
      </footer>

      {/* Delete Confirmation Modal */}
      <dialog ref={deleteModalRef} className="modal">
        <div className="modal-box max-w-md max-lg:max-w-sm max-sm:max-w-xs mx-4">
          <h3 className="font-bold text-lg max-sm:text-base text-base-content mb-4 max-sm:mb-3">
            <Icon icon="hugeicons:alert-triangle" className="text-warning text-xl max-sm:text-lg mr-2 inline" />
            Confirm Deletion
          </h3>
          <p className="py-4 max-sm:py-3 text-base max-sm:text-sm text-base-content/70">
            Are you sure you want to delete the existing face data? This action cannot be undone.
          </p>
          <div className="modal-action flex-row max-sm:flex-col gap-3 max-sm:gap-2">
            <button
              className="btn btn-ghost order-1 max-sm:order-2"
              onClick={() => setShowDeleteModal(false)}
            >
              Cancel
            </button>
            <button
              className="btn btn-error order-2 max-sm:order-1"
              onClick={() => {
                const records = faceQuery.data!.result.records;
                for (const record of records) {
                  deleteMutation.mutate(record.id);
                }
                setShowDeleteModal(false);
              }}
            >
              <Icon icon="hugeicons:delete-01" className="text-base max-sm:text-sm mr-2 max-sm:mr-1" />
              Delete
            </button>
          </div>
        </div>
        <form method="dialog" className="modal-backdrop">
          <button onClick={() => setShowDeleteModal(false)}>close</button>
        </form>
      </dialog>

      {/* Loading Overlay */}
      {(registerMutation.isPending || deleteMutation.isPending) && (
        <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 px-4">
          <div className="bg-base-100 rounded-2xl p-8 max-sm:p-6 text-center border border-base-300 shadow-xl max-w-sm w-full">
            <div className="w-16 h-16 max-sm:w-12 max-sm:h-12 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto mb-4 max-sm:mb-3"></div>
            <h3 className="text-lg max-sm:text-base font-semibold text-base-content mb-2 max-sm:mb-1">
              {registerMutation.isPending ? 'Processing Registration' : 'Deleting Face Data'}
            </h3>
            <p className="text-sm max-sm:text-xs text-base-content/70">
              {registerMutation.isPending ? 'Submitting your face data...' : 'Removing existing records...'}
            </p>
          </div>
        </div>
      )}

      {/* Success Overlay */}
      {registerMutation.isSuccess && (
        <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 px-4 py-8">
          <div className="max-w-lg max-lg:max-w-md max-sm:max-w-sm w-full bg-base-100 rounded-2xl shadow-xl p-8 max-sm:p-6 text-center border border-base-300">
            <div className="w-20 h-20 max-sm:w-16 max-sm:h-16 bg-success/10 rounded-full flex items-center justify-center mx-auto mb-6 max-sm:mb-4">
              <Icon icon="hugeicons:checkmark-circle-04" className="text-4xl max-sm:text-3xl text-success" />
            </div>
            <h2 className="text-2xl max-sm:text-xl font-bold text-base-content mb-3 max-sm:mb-2">Registration Successful!</h2>
            <p className="text-base max-sm:text-sm text-base-content/70 leading-relaxed mb-4 max-sm:mb-3">
              Your face has been successfully registered in our system. You can now access secure services using biometric verification.
            </p>
            {redirect && (
              <p className="text-sm max-sm:text-xs text-base-content/50">You will be redirected in 3 seconds...</p>
            )}
          </div>
        </div>
      )}

    </div>
  );
}

export default RegisterPage
