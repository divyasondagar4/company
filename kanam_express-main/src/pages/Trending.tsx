import { PageLayout } from '@/components/layout/PageLayout';
import { useLanguage } from '@/contexts/LanguageContext';
import { TrendingUp, Clock, Eye } from 'lucide-react';
import { Link } from 'react-router-dom';

const trendingNews = [
  {
    id: 1,
    image: 'https://images.unsplash.com/photo-1495020689067-958852a7765e?w=600',
    category: 'ગુજરાત',
    categoryEn: 'Gujarat',
    headline: 'ગુજરાત વિધાનસભામાં ઐતિહાસિક બિલ પસાર',
    headlineEn: 'Historic bill passed in Gujarat Assembly',
    time: '30 min ago',
    trending: '15.2K',
    href: '/gujarat',
  },
  {
    id: 2,
    image: 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=600',
    category: 'રમતગમત',
    categoryEn: 'Sports',
    headline: 'IPL 2024: ગુજરાત ટાઇટન્સની શાનદાર જીત',
    headlineEn: 'IPL 2024: Gujarat Titans spectacular victory',
    time: '1 hour ago',
    trending: '45.2K',
    href: '/sports',
  },
  {
    id: 3,
    image: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600',
    category: 'બિઝનેસ',
    categoryEn: 'Business',
    headline: 'Budget 2024: નાણામંત્રીની મહત્વની જાહેરાત',
    headlineEn: 'Budget 2024: Important announcement by Finance Minister',
    time: '2 hours ago',
    trending: '23.8K',
    href: '/business',
  },
  {
    id: 4,
    image: 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=600',
    category: 'ટેકનોલોજી',
    categoryEn: 'Technology',
    headline: 'AI Revolution: ભારતમાં ટેકનોલોજીનું ભવિષ્ય',
    headlineEn: 'AI Revolution: Future of technology in India',
    time: '3 hours ago',
    trending: '34.1K',
    href: '/technology',
  },
  {
    id: 5,
    image: 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=600',
    category: 'રાષ્ટ્રીય',
    categoryEn: 'National',
    headline: 'સંસદમાં નવા બિલ પર ચર્ચા, વિપક્ષનો વિરોધ',
    headlineEn: 'Parliament debates new bill, opposition protests',
    time: '4 hours ago',
    trending: '18.5K',
    href: '/national',
  },
  {
    id: 6,
    image: 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=600',
    category: 'બિઝનેસ',
    categoryEn: 'Business',
    headline: 'શેરબજાર: સેન્સેક્સ 500 પોઇન્ટ ઉછળ્યો',
    headlineEn: 'Stock Market: Sensex jumps 500 points',
    time: '5 hours ago',
    trending: '12.3K',
    href: '/business',
  },
  {
    id: 7,
    image: 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=600',
    category: 'ટેકનોલોજી',
    categoryEn: 'Technology',
    headline: 'નવી AI ટેકનોલોજી: ભારતીય સ્ટાર્ટઅપની સિદ્ધિ',
    headlineEn: 'New AI technology: Indian startup achievement',
    time: '6 hours ago',
    trending: '9.8K',
    href: '/technology',
  },
  {
    id: 8,
    image: 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?w=600',
    category: 'આંતરરાષ્ટ્રીય',
    categoryEn: 'International',
    headline: 'G20 સમિટ: વૈશ્વિક નેતાઓની મહત્વની બેઠક',
    headlineEn: 'G20 Summit: Important meeting of global leaders',
    time: '7 hours ago',
    trending: '8.5K',
    href: '/international',
  },
];

const Trending = () => {
  const { language } = useLanguage();

  return (
    <PageLayout showTicker={true}>
      <div className="container mx-auto px-4 py-8">
        {/* Page Header */}
        <div className="flex items-center gap-3 mb-8">
          <div className="w-12 h-12 flex items-center justify-center bg-primary rounded-full">
            <TrendingUp className="w-6 h-6 text-primary-foreground" />
          </div>
          <div>
            <h1 className="text-3xl md:text-4xl font-bold text-foreground">
              {language === 'en' ? 'Trending Now' : 'ટ્રેન્ડિંગ'}
            </h1>
            <p className="text-muted-foreground">
              {language === 'en' ? 'Most popular stories right now' : 'અત્યારની સૌથી લોકપ્રિય સ્ટોરીઝ'}
            </p>
          </div>
        </div>

        {/* Trending List */}
        <div className="space-y-6">
          {trendingNews.map((news, index) => (
            <Link
              key={news.id}
              to={news.href}
              className="flex gap-4 md:gap-6 p-4 bg-card rounded-xl shadow-card hover:shadow-elevated transition-shadow group"
            >
              {/* Rank */}
              <div className="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-primary/10 text-primary text-xl font-bold rounded-full">
                {index + 1}
              </div>

              {/* Image */}
              <div className="flex-shrink-0 w-24 h-20 md:w-40 md:h-28 overflow-hidden rounded-lg">
                <img
                  src={news.image}
                  alt={news.headline}
                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
              </div>

              {/* Content */}
              <div className="flex-1 flex flex-col justify-center">
                <span className="text-xs font-semibold text-primary uppercase tracking-wider mb-1">
                  {language === 'en' ? news.categoryEn : news.category}
                </span>
                <h3 className="text-lg md:text-xl font-semibold text-foreground group-hover:text-primary transition-colors line-clamp-2 mb-2">
                  {language === 'en' ? news.headlineEn : news.headline}
                </h3>
                <div className="flex items-center gap-4 text-sm text-muted-foreground">
                  <span className="flex items-center gap-1">
                    <Clock className="w-4 h-4" />
                    {news.time}
                  </span>
                  <span className="flex items-center gap-1 text-primary font-medium">
                    <TrendingUp className="w-4 h-4" />
                    {news.trending}
                  </span>
                </div>
              </div>
            </Link>
          ))}
        </div>
      </div>
    </PageLayout>
  );
};

export default Trending;
