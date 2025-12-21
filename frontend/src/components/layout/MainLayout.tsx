import { Outlet } from 'react-router';
import Header from './Header';
import Footer from './Footer';

export default function MainLayout() {
    return (
        <div className="flex flex-col min-h-screen bg-base-100">
            <Header />
            {/* 
        Add top padding to prevent content from being hidden behind the fixed header.
        Header height is h-16 (4rem / 64px).
      */}
            <main className="flex-grow pt-16">
                <Outlet />
            </main>
            <Footer />
        </div>
    );
}
