import { useState } from 'react';
import { Clock, TrendingUp, Search, X } from 'lucide-react';
import { PageLayout } from '@/components/layout/PageLayout';
import { NewsCard } from '@/components/news/NewsCard';
import { TrendingSidebar } from '@/components/news/TrendingSidebar';
import { useLanguage } from '@/contexts/LanguageContext';
import { Input } from '@/components/ui/input';

const categories = [
  { id: 'all', name: 'All', nameGu: 'બધા' },
  { id: 'gujarat', name: 'Gujarat', nameGu: 'ગુજરાત' },
  { id: 'national', name: 'National', nameGu: 'રાષ્ટ્રીય' },
  { id: 'international', name: 'International', nameGu: 'આંતરરાષ્ટ્રીય' },
  { id: 'sports', name: 'Sports', nameGu: 'રમતગમત' },
  { id: 'business', name: 'Business', nameGu: 'બિઝનેસ' },
  { id: 'entertainment', name: 'Entertainment', nameGu: 'મનોરંજન' },
  { id: 'technology', name: 'Technology', nameGu: 'ટેકનોલોજી' },
];

const latestNews = [
  {
    image: 'https://images.unsplash.com/photo-1495020689067-958852a7765e?w=600',
    headline: 'બ્રેકિંગ: ગુજરાત વિધાનસભામાં મહત્વનો નિર્ણય',
    headlineEn: 'Breaking: Important decision in Gujarat Assembly',
    category: 'gujarat',
    time: '5 minutes ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1540747913346-19e32dc3e97e?w=600',
    headline: 'IPL 2024: ગુજરાત ટાઇટન્સની શાનદાર જીત',
    headlineEn: 'IPL 2024: Gujarat Titans spectacular victory',
    category: 'sports',
    time: '15 minutes ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600',
    headline: 'Budget 2024: નાણામંત્રીની મહત્વની જાહેરાત',
    headlineEn: 'Budget 2024: Important announcement by FM',
    category: 'business',
    time: '30 minutes ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=600',
    headline: 'AI Revolution: ભારતમાં ટેકનોલોજીનું ભવિષ્ય',
    headlineEn: 'AI Revolution: Future of technology in India',
    category: 'technology',
    time: '45 minutes ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1529665253569-6d01c0eaf7b6?w=600',
    headline: 'એક્સક્લુઝિવ: બોલીવુડ સ્ટાર સાથે ખાસ વાતચીત',
    headlineEn: 'Exclusive: Special chat with Bollywood star',
    category: 'entertainment',
    time: '1 hour ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1532375810709-75b1da00537c?w=800',
    headline: 'સંસદનું શિયાળુ સત્ર: મહત્વના બિલો પર ચર્ચા',
    headlineEn: 'Parliament Winter Session: Discussion on important bills',
    category: 'national',
    time: '1.5 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1485738422979-f5c462d49f74?w=600',
    headline: 'અમેરિકામાં રાષ્ટ્રપતિ ચૂંટણી: તાજેતરના અપડેટ્સ',
    headlineEn: 'US Presidential Election: Latest Updates',
    category: 'international',
    time: '2 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1567157577867-05ccb1388e66?w=600',
    headline: 'અમદાવાદ: BRTS કોરિડોરનું વિસ્તરણ',
    headlineEn: 'Ahmedabad: BRTS corridor expansion',
    category: 'gujarat',
    time: '2.5 hours ago',
  },
];

const LatestNews = () => {
  const { language } = useLanguage();
  const [selectedCategory, setSelectedCategory] = useState('all');
  const [searchQuery, setSearchQuery] = useState('');
  const [showSearch, setShowSearch] = useState(false);

  const filteredNews = latestNews.filter(news => {
    const matchesCategory = selectedCategory === 'all' || news.category === selectedCategory;
    const matchesSearch = searchQuery === '' || 
      news.headline.toLowerCase().includes(searchQuery.toLowerCase()) ||
      news.headlineEn.toLowerCase().includes(searchQuery.toLowerCase());
    return matchesCategory && matchesSearch;
  });

  return (
    <PageLayout>
      <div className="container mx-auto px-4 py-8">
        {/* Page Header */}
        <div className="flex flex-col gap-4 mb-6">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div className="flex items-center gap-3">
              <div className="w-12 h-12 flex items-center justify-center bg-primary/10 rounded-full">
                <Clock className="w-6 h-6 text-primary" />
              </div>
              <div>
                <h1 className="headline-primary text-foreground">
                  {language === 'en' ? 'Latest News' : 'તાજા સમાચાર'}
                </h1>
                <p className="text-muted-foreground text-sm">
                  {language === 'en' 
                    ? 'Stay updated with the latest happenings' 
                    : 'તાજેતરની ઘટનાઓ સાથે અપડેટ રહો'}
                </p>
              </div>
            </div>
            
            {/* Search Button */}
            <button
              onClick={() => setShowSearch(!showSearch)}
              className="flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded-full hover:bg-primary/90 transition-colors self-start sm:self-center"
            >
              <Search className="w-4 h-4" />
              <span>{language === 'en' ? 'Search' : 'શોધો'}</span>
            </button>
          </div>

          {/* Search Box */}
          {showSearch && (
            <div className="relative animate-fade-in">
              <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" />
              <Input
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder={language === 'en' ? 'Search news...' : 'સમાચાર શોધો...'}
                className="pl-12 pr-12 h-12 text-base border-2 border-primary/20 focus:border-primary"
                autoFocus
              />
              {searchQuery && (
                <button
                  onClick={() => setSearchQuery('')}
                  className="absolute right-4 top-1/2 -translate-y-1/2"
                >
                  <X className="w-5 h-5 text-muted-foreground hover:text-foreground" />
                </button>
              )}
            </div>
          )}
        </div>

        {/* Category Filter - Horizontal Scroll */}
        <div className="overflow-x-auto scrollbar-hide mb-8">
          <div className="flex items-center gap-2 min-w-max pb-2">
            {categories.map((cat) => (
              <button
                key={cat.id}
                onClick={() => setSelectedCategory(cat.id)}
                className={`px-4 py-2 text-sm font-medium rounded-full transition-colors whitespace-nowrap ${
                  selectedCategory === cat.id
                    ? 'bg-primary text-primary-foreground'
                    : 'bg-secondary text-secondary-foreground hover:bg-primary/10'
                }`}
              >
                {language === 'en' ? cat.name : cat.nameGu}
              </button>
            ))}
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Main Content */}
          <div className="lg:col-span-2">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
              {filteredNews.map((news, index) => (
                <NewsCard
                  key={index}
                  image={news.image}
                  category={categories.find(c => c.id === news.category)?.name || 'News'}
                  headline={language === 'en' ? news.headlineEn : news.headline}
                  time={news.time}
                />
              ))}
            </div>
            {filteredNews.length === 0 && (
              <div className="text-center py-12 text-muted-foreground">
                {language === 'en' ? 'No news found' : 'કોઈ સમાચાર મળ્યા નથી'}
              </div>
            )}
          </div>

          {/* Sidebar */}
          <div className="lg:col-span-1">
            <TrendingSidebar />
          </div>
        </div>
      </div>
    </PageLayout>
  );
};

export default LatestNews;
