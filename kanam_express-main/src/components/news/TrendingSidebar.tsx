import { TrendingUp, ArrowRight } from 'lucide-react';
import { Link } from 'react-router-dom';
import { useLanguage } from '@/contexts/LanguageContext';
import { NewsCard } from './NewsCard';

const trendingTopics = [
  { tag: '#ગુજરાત', tagEn: '#Gujarat', tagHi: '#गुजरात', count: '12.5K', href: '/gujarat' },
  { tag: '#Budget2024', tagEn: '#Budget2024', tagHi: '#बजट2024', count: '8.2K', href: '/business' },
  { tag: '#Cricket', tagEn: '#Cricket', tagHi: '#क्रिकेट', count: '6.8K', href: '/sports' },
  { tag: '#Election', tagEn: '#Election', tagHi: '#चुनाव', count: '5.4K', href: '/national' },
  { tag: '#Technology', tagEn: '#Technology', tagHi: '#टेक्नोलॉजी', count: '4.1K', href: '/technology' },
];

const latestNews = [
  {
    image: 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=200',
    headline: 'નવી શિક્ષણ નીતિ લાગુ કરવા માટે રાજ્ય સરકાર તૈયાર',
    headlineEn: 'State government ready to implement new education policy',
    time: '5 min ago',
    href: '/national',
  },
  {
    image: 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=200',
    headline: 'સ્ટોક માર્કેટમાં તેજી, સેન્સેક્સ નવી ઊંચાઈએ',
    headlineEn: 'Stock market rally, Sensex hits new high',
    time: '12 min ago',
    href: '/business',
  },
  {
    image: 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=200',
    headline: 'IPL 2024: ગુજરાત ટાઇટન્સની શાનદાર જીત',
    headlineEn: 'IPL 2024: Gujarat Titans win spectacularly',
    time: '25 min ago',
    href: '/sports',
  },
  {
    image: 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=200',
    headline: 'AI ટેકનોલોજીમાં ભારતની આગેકૂચ',
    headlineEn: 'India leads in AI technology',
    time: '1 hour ago',
    href: '/technology',
  },
];

export function TrendingSidebar() {
  const { t, language } = useLanguage();

  const getTag = (item: typeof trendingTopics[0]) => {
    if (language === 'en') return item.tagEn;
    if (language === 'hi') return item.tagHi;
    return item.tag;
  };

  const getHeadline = (item: typeof latestNews[0]) => {
    return language === 'en' ? item.headlineEn : item.headline;
  };

  return (
    <aside className="space-y-8">
      {/* Trending Topics */}
      <div className="bg-card rounded-xl p-5 shadow-card">
        <div className="flex items-center justify-between mb-4">
          <div className="flex items-center gap-2">
            <TrendingUp className="w-5 h-5 text-primary" />
            <h3 className="section-title text-lg">{t('trending')}</h3>
          </div>
          <Link to="/trending" className="text-primary text-sm font-medium flex items-center gap-1 hover:underline">
            {language === 'gu' ? 'બધા જુઓ' : 'View All'}
            <ArrowRight className="w-4 h-4" />
          </Link>
        </div>
        
        <div className="space-y-3">
          {trendingTopics.map((topic, index) => (
            <Link
              key={topic.tag}
              to={topic.href}
              className="flex items-center justify-between py-2 border-b border-border last:border-0 cursor-pointer group"
            >
              <div className="flex items-center gap-3">
                <span className="w-6 h-6 flex items-center justify-center bg-primary/10 text-primary text-xs font-bold rounded">
                  {index + 1}
                </span>
                <span className="font-medium text-foreground group-hover:text-primary transition-colors">
                  {getTag(topic)}
                </span>
              </div>
              <span className="text-xs text-muted-foreground">{topic.count}</span>
            </Link>
          ))}
        </div>
      </div>

      {/* Latest News */}
      <div className="bg-card rounded-xl p-5 shadow-card">
        <div className="flex items-center justify-between mb-4">
          <h3 className="section-title text-lg">{t('latest_news')}</h3>
          <Link to="/latest" className="text-primary text-sm font-medium flex items-center gap-1 hover:underline">
            {language === 'gu' ? 'બધા જુઓ' : 'View All'}
            <ArrowRight className="w-4 h-4" />
          </Link>
        </div>
        
        <div>
          {latestNews.map((news, index) => (
            <NewsCard
              key={index}
              image={news.image}
              category=""
              headline={getHeadline(news)}
              time={news.time}
              variant="compact"
              href={news.href}
            />
          ))}
        </div>
      </div>

      {/* Ad Space */}
      <div className="bg-secondary rounded-xl p-6 text-center">
        <p className="text-xs text-muted-foreground uppercase tracking-wider mb-2">Advertisement</p>
        <div className="aspect-[4/5] bg-muted rounded-lg flex items-center justify-center">
          <span className="text-muted-foreground">Ad Space</span>
        </div>
      </div>
    </aside>
  );
}
