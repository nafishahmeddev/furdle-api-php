
import { BrowserRouter, Routes, Route } from "react-router";
import RegisterPage from "./pages/register/RegisterPage";
import { HomePage } from "./pages/home/HomePage";
export default function Router() {
  return <BrowserRouter >
    <Routes>
      <Route path="/" element={<HomePage />} />
      <Route path="/register" element={<RegisterPage />} />
    </Routes>

  </BrowserRouter>
}