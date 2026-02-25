import { useState } from 'react';
import { Globe, MapPin, Search, X } from 'lucide-react';
import { PageLayout } from '@/components/layout/PageLayout';
import { NewsCard } from '@/components/news/NewsCard';
import { useLanguage } from '@/contexts/LanguageContext';
import { Input } from '@/components/ui/input';

const regions = [
  { id: 'all', name: 'All', nameGu: 'બધા' },
  { id: 'usa', name: 'USA', nameGu: 'અમેરિકા' },
  { id: 'europe', name: 'Europe', nameGu: 'યુરોપ' },
  { id: 'asia', name: 'Asia', nameGu: 'એશિયા' },
  { id: 'middle-east', name: 'Middle East', nameGu: 'મધ્ય પૂર્વ' },
  { id: 'africa', name: 'Africa', nameGu: 'આફ્રિકા' },
  { id: 'uk', name: 'UK', nameGu: 'યુકે' },
  { id: 'russia', name: 'Russia', nameGu: 'રશિયા' },
  { id: 'china', name: 'China', nameGu: 'ચીન' },
  { id: 'japan', name: 'Japan', nameGu: 'જાપાન' },
  { id: 'australia', name: 'Australia', nameGu: 'ઓસ્ટ્રેલિયા' },
  { id: 'canada', name: 'Canada', nameGu: 'કેનેડા' },
  { id: 'south-america', name: 'South America', nameGu: 'દક્ષિણ અમેરિકા' },
];

const internationalNews = [
  {
    image: 'https://images.unsplash.com/photo-1485738422979-f5c462d49f74?w=600',
    headline: 'અમેરિકામાં રાષ્ટ્રપતિ ચૂંટણી: તાજેતરના અપડેટ્સ',
    headlineEn: 'US Presidential Election: Latest Updates',
    region: 'usa',
    time: '1 hour ago',
    isBreaking: true,
  },
  {
    image: 'https://images.unsplash.com/photo-1529180979161-06b8b6d6f2be?w=600',
    headline: 'યુરોપિયન યુનિયનની નવી આર્થિક નીતિ',
    headlineEn: 'European Union new economic policy',
    region: 'europe',
    time: '2 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1536599018102-9f803c140fc1?w=600',
    headline: 'ચીન-જાપાન વેપાર સમજૂતી પર હસ્તાક્ષર',
    headlineEn: 'China-Japan trade agreement signed',
    region: 'asia',
    time: '3 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1547234935-80c7145ec969?w=600',
    headline: 'મધ્ય પૂર્વમાં શાંતિ વાર્તા ફરી શરૂ',
    headlineEn: 'Peace talks resume in Middle East',
    region: 'middle-east',
    time: '4 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1509099836639-18ba1795216d?w=600',
    headline: 'આફ્રિકન યુનિયન સમિટ: મહત્વની જાહેરાતો',
    headlineEn: 'African Union Summit: Major announcements',
    region: 'africa',
    time: '5 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1551634979-2b11f8c946fe?w=600',
    headline: 'UK Brexit પછીની નવી વેપાર નીતિ',
    headlineEn: 'UK post-Brexit new trade policy',
    region: 'uk',
    time: '6 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?w=600',
    headline: 'રશિયા-યુક્રેન: તાજેતરની પરિસ્થિતિ',
    headlineEn: 'Russia-Ukraine: Latest situation',
    region: 'russia',
    time: '7 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1494145904049-0dca59b4bbad?w=600',
    headline: 'ઓસ્ટ્રેલિયામાં ક્લાઇમેટ સમિટ',
    headlineEn: 'Climate Summit in Australia',
    region: 'australia',
    time: '8 hours ago',
  },
];

const International = () => {
  const { language } = useLanguage();
  const [selectedRegion, setSelectedRegion] = useState('all');
  const [searchQuery, setSearchQuery] = useState('');
  const [showSearch, setShowSearch] = useState(false);

  const filteredNews = internationalNews.filter(news => {
    const matchesRegion = selectedRegion === 'all' || news.region === selectedRegion;
    const matchesSearch = searchQuery === '' || 
      news.headline.toLowerCase().includes(searchQuery.toLowerCase()) ||
      news.headlineEn.toLowerCase().includes(searchQuery.toLowerCase());
    return matchesRegion && matchesSearch;
  });

  return (
    <PageLayout>
      <div className="container mx-auto px-4 py-8">
        {/* Page Header with World Map Feel */}
        <div className="bg-gradient-to-r from-primary/10 via-accent/5 to-primary/10 rounded-2xl p-6 md:p-8 mb-8">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div className="flex items-center gap-3">
              <Globe className="w-8 h-8 text-primary" />
              <h1 className="headline-primary text-foreground">
                {language === 'en' ? 'International News' : 'આંતરરાષ્ટ્રીય સમાચાર'}
              </h1>
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
          
          <p className="text-muted-foreground max-w-2xl mb-4">
            {language === 'en' 
              ? 'Breaking news and analysis from around the world' 
              : 'વિશ્વભરમાંથી બ્રેકિંગ ન્યૂઝ અને વિશ્લેષણ'}
          </p>

          {/* Search Box */}
          {showSearch && (
            <div className="relative animate-fade-in mb-4">
              <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" />
              <Input
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder={language === 'en' ? 'Search news...' : 'સમાચાર શોધો...'}
                className="pl-12 pr-12 h-12 text-base border-2 border-primary/20 focus:border-primary bg-background"
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

          {/* Region Filter */}
          <div className="overflow-x-auto scrollbar-hide">
            <div className="flex flex-wrap gap-2 min-w-max">
              {regions.map((region) => (
                <button
                  key={region.id}
                  onClick={() => setSelectedRegion(region.id)}
                  className={`px-4 py-2 text-sm font-medium rounded-full transition-all whitespace-nowrap ${
                    selectedRegion === region.id
                      ? 'bg-primary text-primary-foreground shadow-lg'
                      : 'bg-card text-foreground hover:bg-primary/10'
                  }`}
                >
                  {language === 'en' ? region.name : region.nameGu}
                </button>
              ))}
            </div>
          </div>
        </div>

        {/* Breaking Alert */}
        {filteredNews.some(n => n.isBreaking) && (
          <div className="bg-primary text-primary-foreground rounded-lg p-4 mb-6 flex items-center gap-3">
            <span className="live-dot bg-accent" />
            <span className="font-medium">
              {language === 'en' ? 'Breaking: ' : 'બ્રેકિંગ: '}
              {language === 'en' 
                ? filteredNews.find(n => n.isBreaking)?.headlineEn 
                : filteredNews.find(n => n.isBreaking)?.headline}
            </span>
          </div>
        )}

        {/* News Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          {filteredNews.map((news, index) => (
            <NewsCard
              key={index}
              image={news.image}
              category={regions.find(r => r.id === news.region)?.name || 'World'}
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
    </PageLayout>
  );
};

export default International;
