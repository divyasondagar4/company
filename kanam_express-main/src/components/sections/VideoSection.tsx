import { Play, ArrowRight } from 'lucide-react';
import { Link } from 'react-router-dom';
import { useLanguage } from '@/contexts/LanguageContext';
import { VideoCard } from '@/components/news/VideoCard';

const videos = [
  {
    thumbnail: 'https://images.unsplash.com/photo-1495020689067-958852a7765e?w=600',
    title: 'બ્રેકિંગ: અમદાવાદમાં નવા ઇન્ફ્રાસ્ટ્રક્ચર પ્રોજેક્ટની જાહેરાત',
    titleEn: 'Breaking: New infrastructure project announced in Ahmedabad',
    duration: '12:45',
    views: '45K',
    category: 'Gujarat',
  },
  {
    thumbnail: 'https://images.unsplash.com/photo-1540747913346-19e32dc3e97e?w=600',
    title: 'IPL 2024: ગુજરાત ટાઇટન્સ vs મુંબઈ ઇન્ડિયન્સ હાઇલાઇટ્સ',
    titleEn: 'IPL 2024: Gujarat Titans vs Mumbai Indians Highlights',
    duration: '8:30',
    views: '120K',
    category: 'Sports',
  },
  {
    thumbnail: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600',
    title: 'Budget 2024: નાણામંત્રીની મહત્વની જાહેરાતો',
    titleEn: 'Budget 2024: Key announcements by Finance Minister',
    duration: '15:20',
    views: '89K',
    category: 'Business',
  },
  {
    thumbnail: 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=600',
    title: 'Tech Talk: AI કેવી રીતે બદલી રહ્યું છે આપણું જીવન',
    titleEn: 'Tech Talk: How AI is changing our lives',
    duration: '10:15',
    views: '67K',
    category: 'Technology',
  },
];

export function VideoSection() {
  const { t, language } = useLanguage();

  return (
    <section className="py-8 bg-secondary/50 -mx-4 px-4 lg:-mx-8 lg:px-8">
      <div className="container mx-auto">
        <div className="flex items-center justify-between mb-6">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 flex items-center justify-center bg-primary rounded-full">
              <Play className="w-5 h-5 text-primary-foreground fill-current" />
            </div>
            <h2 className="section-title">{t('videos')}</h2>
          </div>
          <Link 
            to="/videos"
            className="text-primary text-sm font-medium flex items-center gap-1 hover:underline"
          >
            {language === 'gu' ? 'બધા વિડિયો' : 'All Videos'}
            <ArrowRight className="w-4 h-4" />
          </Link>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {videos.map((video, index) => (
            <VideoCard
              key={index}
              thumbnail={video.thumbnail}
              title={language === 'en' ? video.titleEn : video.title}
              duration={video.duration}
              views={video.views}
              category={video.category}
              href="/videos"
            />
          ))}
        </div>
      </div>
    </section>
  );
}
