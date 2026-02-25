import { ReactNode } from 'react';
import { TopBar } from './TopBar';
import { Header } from './Header';
import { BreakingTicker } from './BreakingTicker';
import { Navbar } from './Navbar';
import { Footer } from './Footer';

interface PageLayoutProps {
  children: ReactNode;
  showTicker?: boolean;
}

export function PageLayout({ children, showTicker = true }: PageLayoutProps) {
  return (
    <div className="min-h-screen bg-background">
      <TopBar />
      <Header />
      {showTicker && <BreakingTicker />}
      <Navbar />
      <main>{children}</main>
      <Footer />
    </div>
  );
}
