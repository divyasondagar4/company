import { Play, ArrowRight } from 'lucide-react';
import { Link } from 'react-router-dom';
import { useLanguage } from '@/contexts/LanguageContext';

const reels = [
  {
    id: 1,
    thumbnail: 'https://images.unsplash.com/photo-1495020689067-958852a7765e?w=600',
    title: 'બ્રેકિંગ: ગુજરાત વિધાનસભામાં મહત્વનો નિર્ણય',
    titleEn: 'Breaking: Important decision in Gujarat Assembly',
    views: '12.5K',
    author: 'સંદેશ ન્યૂઝ',
  },
  {
    id: 2,
    thumbnail: 'https://images.unsplash.com/photo-1540747913346-19e32dc3e97e?w=600',
    title: 'IPL 2024: ગુજરાત ટાઇટન્સની શાનદાર જીત',
    titleEn: 'IPL 2024: Gujarat Titans spectacular victory',
    views: '45.2K',
    author: 'સ્પોર્ટ્સ ડેસ્ક',
  },
  {
    id: 3,
    thumbnail: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600',
    title: 'Budget 2024: નાણામંત્રીની મહત્વની જાહેરાત',
    titleEn: 'Budget 2024: Important announcement by FM',
    views: '23.8K',
    author: 'બિઝનેસ ડેસ્ક',
  },
  {
    id: 4,
    thumbnail: 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=600',
    title: 'AI Revolution: ભારતમાં ટેકનોલોજીનું ભવિષ્ય',
    titleEn: 'AI Revolution: Future of technology in India',
    views: '34.1K',
    author: 'ટેક ડેસ્ક',
  },
];

export function ReelsSection() {
  const { t, language } = useLanguage();

  return (
    <section className="py-8 mt-8">
      <div className="container mx-auto">
        <div className="flex items-center justify-between mb-6">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 flex items-center justify-center bg-gradient-to-br from-primary to-accent rounded-full">
              <Play className="w-5 h-5 text-primary-foreground fill-current" />
            </div>
            <h2 className="section-title">{language === 'gu' ? 'રીલ્સ' : 'Reels'}</h2>
          </div>
          <Link 
            to="/reels" 
            className="text-primary text-sm font-medium flex items-center gap-1 hover:underline"
          >
            {language === 'gu' ? 'બધા રીલ્સ જુઓ' : 'View All Reels'}
            <ArrowRight className="w-4 h-4" />
          </Link>
        </div>

        {/* Mobile: Horizontal Scroll, Desktop: Grid */}
        <div className="flex gap-4 overflow-x-auto pb-4 scrollbar-hide md:grid md:grid-cols-4 md:overflow-x-visible md:pb-0">
          {reels.map((reel) => (
            <Link
              key={reel.id}
              to="/reels"
              className="flex-shrink-0 w-48 md:w-auto group cursor-pointer"
            >
              <div className="relative aspect-[9/16] rounded-xl overflow-hidden bg-secondary">
                <img
                  src={reel.thumbnail}
                  alt=""
                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />
                
                {/* Play Button Overlay */}
                <div className="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                  <div className="w-14 h-14 flex items-center justify-center bg-primary/90 rounded-full">
                    <Play className="w-7 h-7 text-primary-foreground fill-current ml-1" />
                  </div>
                </div>

                {/* Content */}
                <div className="absolute bottom-0 left-0 right-0 p-4">
                  <p className="text-white text-sm font-medium line-clamp-2 mb-2">
                    {language === 'en' ? reel.titleEn : reel.title}
                  </p>
                  <div className="flex items-center gap-2 text-white/70 text-xs">
                    <span>{reel.author}</span>
                    <span>•</span>
                    <span>{reel.views} {language === 'en' ? 'views' : 'વ્યૂઝ'}</span>
                  </div>
                </div>
              </div>
            </Link>
          ))}
        </div>
      </div>
    </section>
  );
}
