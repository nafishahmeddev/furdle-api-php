
import { BrowserRouter, Routes, Route } from "react-router";
import React, { Suspense } from "react";
import MainLayout from "./components/layout/MainLayout";
const HomePage = React.lazy(() => import("./pages/home/HomePage"));
const RegisterPage = React.lazy(() => import("./pages/register/RegisterPage"));
const DownloadPage = React.lazy(() => import("./pages/download/DownloadPage"));
const PrivacyPage = React.lazy(() => import("./pages/privacy/PrivacyPage"));
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
          <Route element={<MainLayout />}>
            <Route path="/" element={<HomePage />} />
            <Route path="/download" element={<DownloadPage />} />
            <Route path="/furdle/privacy" element={<PrivacyPage />} />
            <Route path="*" element={<NotFoundPage />} />
          </Route>
          <Route path="/register" element={<RegisterPage />} />
        </Routes>
      </Suspense>
    </BrowserRouter>
  );
}