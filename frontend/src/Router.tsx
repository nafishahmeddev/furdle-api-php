
import { BrowserRouter, Routes, Route } from "react-router";
import React, { Suspense } from "react";
const HomePage = React.lazy(() => import("./pages/home/HomePage"));
const RegisterPage = React.lazy(() => import("./pages/register/RegisterPage"));
const DownloadPage = React.lazy(() => import("./pages/download/DownloadPage"));
const NotFoundPage = React.lazy(() => import("./pages/__404"));

function LoadingFallback() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-base-100">
      <div className="text-center">
        <span className="loading loading-spinner loading-lg text-primary"></span>
        <p className="mt-4 text-base-content/70">Loading...</p>
      </div>
    </div>
  );
}

export default function Router() {
  return (
    <BrowserRouter>
      <Suspense fallback={<LoadingFallback />}>
        <Routes>
          <Route path="/" element={<HomePage />} />
          <Route path="/register" element={<RegisterPage />} />
          <Route path="/download" element={<DownloadPage />} />
          <Route path="*" element={<NotFoundPage />} />
        </Routes>
      </Suspense>
    </BrowserRouter>
  );
}