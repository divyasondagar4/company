import { Link } from 'react-router-dom';
import { Play, Search, X } from 'lucide-react';
import { useLanguage } from '@/contexts/LanguageContext';
import { useState } from 'react';
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { EpaperDialog } from './EpaperDialog';
import logo from '@/assets/logo.png';

export function Header() {
  const { t, language } = useLanguage();
  const [searchOpen, setSearchOpen] = useState(false);

  return (
    <header className="bg-card border-b border-border py-3 md:py-4">
      <div className="container mx-auto px-2 sm:px-4">
        <div className="flex items-center justify-between gap-2">
          {/* Left: Search */}
          <div className="flex-shrink-0">
            <Dialog open={searchOpen} onOpenChange={setSearchOpen}>
              <DialogTrigger asChild>
                <button className="flex items-center justify-center w-10 h-10 sm:w-auto sm:h-auto sm:px-4 sm:py-2 text-sm font-medium bg-secondary text-secondary-foreground hover:bg-primary hover:text-primary-foreground rounded-full transition-colors">
                  <Search className="w-4 h-4 sm:mr-2" />
                  <span className="hidden sm:inline">{t('search')}</span>
                </button>
              </DialogTrigger>
              <DialogContent className="sm:max-w-2xl p-0 gap-0 [&>button]:hidden">
                <div className="relative p-4">
                  <Search className="absolute left-9 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" />
                  <Input
                    placeholder={t('search') + '...'}
                    className="pl-12 pr-12 h-14 text-lg border-2 border-primary/20 focus:border-primary rounded-full"
                    autoFocus
                  />
                 <button 
  onClick={() => setSearchOpen(false)}
  className="absolute right-8 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full hover:bg-secondary transition-colors"
>
  <X className="w-5 h-5 text-muted-foreground" />
</button>

                </div>
                <div className="px-4 pb-4">
                  <p className="text-sm text-muted-foreground mb-2">{t('trending')}:</p>
                  <div className="flex flex-wrap gap-2">
                    {['#ગુજરાત', '#Cricket', '#Budget2024', '#Election'].map((tag) => (
                      <button key={tag} className="trending-tag">
                        {tag}
                      </button>
                    ))}
                  </div>
                </div>
              </DialogContent>
            </Dialog>
          </div>

          {/* Center: Logo */}
          <Link
  to="/"
  className="flex-1 flex justify-center items-center h-20 w-30"
>
  <img
    src={logo}
    alt="Kanam Express"
    className="h-full scale-125 sm:scale-140 md:scale-150 w-auto object-contain"
  />
</Link>


          {/* Right: ePaper + Live TV */}
          <div className="flex-shrink-0 flex items-center gap-1 sm:gap-2 md:gap-3">
            <EpaperDialog />
            <Link to="/videos" className="live-badge group text-xs sm:text-sm">
              <span className="live-dot" />Live
              <Play className="w-3 h-3 sm:w-3.5 sm:h-3.5 fill-current" />
              <span className="hidden xs:inline">{t('live_tv')}</span>
            </Link>
          </div>
     
        </div>
      </div>
    </header>
  );
}
