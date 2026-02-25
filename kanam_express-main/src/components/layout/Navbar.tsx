import { useState } from 'react';
import { ChevronDown, Menu, X } from 'lucide-react';
import { Link, useLocation } from 'react-router-dom';
import { useLanguage } from '@/contexts/LanguageContext';
import { cn } from '@/lib/utils';

const navItems = [
  { key: 'home', href: '/', hasSubmenu: false },
  { 
    key: 'gujarat', 
    href: '/gujarat',
    hasSubmenu: true,
    submenu: [
      { key: 'ahmedabad', label: 'અમદાવાદ', labelEn: 'Ahmedabad', labelHi: 'अहमदाबाद', href: '/gujarat/ahmedabad' },
      { key: 'surat', label: 'સુરત', labelEn: 'Surat', labelHi: 'सूरत', href: '/gujarat/surat' },
      { key: 'vadodara', label: 'વડોદરા', labelEn: 'Vadodara', labelHi: 'वडोदरा', href: '/gujarat/vadodara' },
      { key: 'rajkot', label: 'રાજકોટ', labelEn: 'Rajkot', labelHi: 'राजकोट', href: '/gujarat/rajkot' },
    ]
  },
  { key: 'national', href: '/national', hasSubmenu: false },
  { key: 'international', href: '/international', hasSubmenu: false },
  { key: 'business', href: '/business', hasSubmenu: false },
  { key: 'sports', href: '/sports', hasSubmenu: false },
  { key: 'entertainment', href: '/entertainment', hasSubmenu: false },
  { key: 'technology', href: '/technology', hasSubmenu: false },
  { key: 'videos', href: '/videos', hasSubmenu: false },
  { key: 'reels', href: '/reels', hasSubmenu: false },
];

export function Navbar() {
  const { t, language } = useLanguage();
  const location = useLocation();
  const [activeMenu, setActiveMenu] = useState<string | null>(null);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  const getSubmenuLabel = (item: { label: string; labelEn: string; labelHi: string }) => {
    if (language === 'en') return item.labelEn;
    if (language === 'hi') return item.labelHi;
    return item.label;
  };

  const isActive = (href: string) => {
    if (href === '/') return location.pathname === '/';
    return location.pathname.startsWith(href);
  };

  return (
    <nav className="bg-nav sticky top-0 z-50 shadow-nav">
      <div className="container mx-auto px-4">
        {/* Desktop Navigation */}
        <div className="hidden lg:flex items-center justify-center">
          {navItems.map((item) => (
            <div
              key={item.key}
              className="relative group"
              onMouseEnter={() => item.hasSubmenu && setActiveMenu(item.key)}
              onMouseLeave={() => setActiveMenu(null)}
            >
              <Link
                to={item.href}
                className={cn(
                  "nav-link flex items-center gap-1",
                  isActive(item.href) && "nav-link-active"
                )}
              >
                {t(item.key)}
                {item.hasSubmenu && <ChevronDown className="w-3.5 h-3.5" />}
              </Link>

              {/* Mega Menu */}
              {item.hasSubmenu && activeMenu === item.key && (
                <div className="absolute left-0 top-full bg-card shadow-elevated rounded-b-lg min-w-[200px] py-2 animate-fade-in">
                  {item.submenu?.map((subItem) => (
                    <Link
                      key={subItem.key}
                      to={subItem.href}
                      className="block px-4 py-2.5 text-sm text-foreground hover:bg-secondary hover:text-primary transition-colors"
                    >
                      {getSubmenuLabel(subItem)}
                    </Link>
                  ))}
                </div>
              )}
            </div>
          ))}
        </div>

        {/* Mobile Navigation - Horizontal Scroll */}
        <div className="lg:hidden overflow-x-auto scrollbar-hide">
          <div className="flex items-center gap-1 py-2 min-w-max">
            {navItems.map((item) => (
              <Link
                key={item.key}
                to={item.href}
                className={cn(
                  "px-4 py-2 text-sm font-medium rounded-full whitespace-nowrap transition-colors",
                  isActive(item.href) 
                    ? "bg-accent text-accent-foreground" 
                    : "text-primary-foreground/90 hover:text-primary-foreground hover:bg-nav-hover"
                )}
              >
                {t(item.key)}
              </Link>
            ))}
          </div>
        </div>
      </div>
    </nav>
  );
}
