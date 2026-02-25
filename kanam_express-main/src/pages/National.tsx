import { useState } from 'react';
import { Search, X, MapPin, Clock } from 'lucide-react';
import { Link } from 'react-router-dom';
import { PageLayout } from '@/components/layout/PageLayout';
import { NewsCard } from '@/components/news/NewsCard';
import { TrendingSidebar } from '@/components/news/TrendingSidebar';
import { useLanguage } from '@/contexts/LanguageContext';
import { Input } from '@/components/ui/input';

const states = [
  { id: 'all', name: 'બધા', nameEn: 'All' },
  { id: 'delhi', name: 'દિલ્હી', nameEn: 'Delhi' },
  { id: 'mumbai', name: 'મુંબઈ', nameEn: 'Mumbai' },
  { id: 'bangalore', name: 'બેંગલુરુ', nameEn: 'Bangalore' },
  { id: 'chennai', name: 'ચેન્નાઈ', nameEn: 'Chennai' },
  { id: 'kolkata', name: 'કોલકાતા', nameEn: 'Kolkata' },
  { id: 'hyderabad', name: 'હૈદરાબાદ', nameEn: 'Hyderabad' },
  { id: 'pune', name: 'પુણે', nameEn: 'Pune' },
  { id: 'jaipur', name: 'જયપુર', nameEn: 'Jaipur' },
  { id: 'lucknow', name: 'લખનૌ', nameEn: 'Lucknow' },
  { id: 'kanpur', name: 'કાનપુર', nameEn: 'Kanpur' },
  { id: 'bhopal', name: 'ભોપાલ', nameEn: 'Bhopal' },
  { id: 'patna', name: 'પટના', nameEn: 'Patna' },
  { id: 'chandigarh', name: 'ચંડીગઢ', nameEn: 'Chandigarh' },
  { id: 'kochi', name: 'કોચી', nameEn: 'Kochi' },
];

const nationalNews = [
  {
    image: 'https://images.unsplash.com/photo-1532375810709-75b1da00537c?w=800',
    headline: 'સંસદનું શિયાળુ સત્ર: મહત્વના બિલો પર ચર્ચા',
    headlineEn: 'Parliament Winter Session: Discussion on important bills',
    excerpt: 'સંસદના શિયાળુ સત્રમાં અનેક મહત્વના બિલો રજૂ કરવામાં આવશે.',
    excerptEn: 'Several important bills will be tabled in the winter session of Parliament.',
    category: 'Politics',
    state: 'delhi',
    time: '1 hour ago',
    isLead: true,
  },
  {
    image: 'https://images.unsplash.com/photo-1569025743873-ea3a9ber?w=600',
    headline: 'કેન્દ્ર સરકારની નવી આર્થિક નીતિ',
    headlineEn: 'Central government new economic policy',
    category: 'Economy',
    state: 'delhi',
    time: '2 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=600',
    headline: 'મુંબઈમાં મેટ્રો લાઇનનું ઉદ્ઘાટન',
    headlineEn: 'Metro line inaugurated in Mumbai',
    category: 'Infrastructure',
    state: 'mumbai',
    time: '3 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=600',
    headline: 'બેંગલુરુમાં IT ક્ષેત્રે મોટું રોકાણ',
    headlineEn: 'Major investment in IT sector in Bangalore',
    category: 'Technology',
    state: 'bangalore',
    time: '4 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600',
    headline: 'રેલવે બજેટમાં નવી ટ્રેનોની જાહેરાત',
    headlineEn: 'New trains announced in railway budget',
    category: 'Infrastructure',
    state: 'delhi',
    time: '5 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=600',
    headline: 'સુપ્રીમ કોર્ટનો મહત્વનો ચુકાદો',
    headlineEn: 'Supreme Court important verdict',
    category: 'Judiciary',
    state: 'delhi',
    time: '6 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=600',
    headline: 'લખનૌમાં નવી ઔદ્યોગિક નીતિ',
    headlineEn: 'New industrial policy in Lucknow',
    category: 'Industry',
    state: 'lucknow',
    time: '7 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=600',
    headline: 'ચેન્નાઈમાં IT ક્ષેત્રે મોટું રોકાણ',
    headlineEn: 'Major investment in IT sector in Chennai',
    category: 'Technology',
    state: 'chennai',
    time: '8 hours ago',
  },
];

const timelineNews = [
  { time: '10:30 AM', text: 'PM addresses nation on economic reforms', textGu: 'PM આર્થિક સુધારા પર રાષ્ટ્રને સંબોધન' },
  { time: '09:45 AM', text: 'Cabinet meeting concludes at Parliament', textGu: 'સંસદમાં કેબિનેટ બેઠક પૂર્ણ' },
  { time: '08:30 AM', text: 'Stock markets open with gains', textGu: 'શેરબજાર તેજી સાથે ખુલ્યું' },
  { time: '07:00 AM', text: 'Weather alert issued for North India', textGu: 'ઉત્તર ભારત માટે હવામાન ચેતવણી' },
];

const National = () => {
  const { language } = useLanguage();
  const [selectedState, setSelectedState] = useState('all');
  const [searchQuery, setSearchQuery] = useState('');
  const [showSearch, setShowSearch] = useState(false);

  const filteredNews = nationalNews.filter(news => {
    const matchesState = selectedState === 'all' || news.state === selectedState;
    const matchesSearch = searchQuery === '' || 
      news.headline.toLowerCase().includes(searchQuery.toLowerCase()) ||
      news.headlineEn.toLowerCase().includes(searchQuery.toLowerCase());
    return matchesState && matchesSearch;
  });

  const leadStory = filteredNews.find(n => n.isLead) || filteredNews[0];
  const otherNews = filteredNews.filter(n => n !== leadStory);

  return (
    <PageLayout>
      <div className="container mx-auto px-4 py-8">
        {/* Page Header */}
        <div className="flex flex-col gap-4 mb-6">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <h1 className="headline-primary text-foreground">
                {language === 'en' ? 'National News' : 'રાષ્ટ્રીય સમાચાર'}
              </h1>
              <p className="text-muted-foreground mt-1">
                {language === 'en' 
                  ? 'Politics, governance, economy and more from across India' 
                  : 'સમગ્ર ભારતમાંથી રાજકારણ, શાસન, અર્થતંત્ર અને વધુ'}
              </p>
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
                placeholder={language === 'en' ? 'Search news or city...' : 'સમાચાર અથવા શહેર શોધો...'}
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

        {/* State Filter - Horizontal Scroll */}
        <div className="overflow-x-auto scrollbar-hide mb-8">
          <div className="flex items-center gap-2 min-w-max pb-2">
            {states.map((state) => (
              state.id === 'all' ? (
                <button
                  key={state.id}
                  onClick={() => setSelectedState(state.id)}
                  className={`px-4 py-2 text-sm font-medium rounded-full transition-colors whitespace-nowrap ${
                    selectedState === state.id
                      ? 'bg-primary text-primary-foreground'
                      : 'bg-secondary text-secondary-foreground hover:bg-primary/10'
                  }`}
                >
                  <span className="flex items-center gap-1">
                    <MapPin className="w-3 h-3" />
                    {language === 'en' ? state.nameEn : state.name}
                  </span>
                </button>
              ) : (
                <Link
                  key={state.id}
                  to={`/national/${state.id}`}
                  onClick={() => setSelectedState(state.id)}
                  className={`px-4 py-2 text-sm font-medium rounded-full transition-colors whitespace-nowrap ${
                    selectedState === state.id
                      ? 'bg-primary text-primary-foreground'
                      : 'bg-secondary text-secondary-foreground hover:bg-primary/10'
                  }`}
                >
                  <span className="flex items-center gap-1">
                    <MapPin className="w-3 h-3" />
                    {language === 'en' ? state.nameEn : state.name}
                  </span>
                </Link>
              )
            ))}
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Main Content */}
          <div className="lg:col-span-2 space-y-8">
            {/* Lead Story */}
            {leadStory && (
              <Link to={`/national/${leadStory.state}`}>
                <article className="news-card overflow-hidden cursor-pointer group">
                  <div className="aspect-[16/9] overflow-hidden">
                    <img
                      src={leadStory.image}
                      alt={leadStory.headline}
                      className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                    />
                  </div>
                  <div className="p-6">
                    <span className="category-tag">{leadStory.category}</span>
                    <h2 className="headline-secondary mt-3 group-hover:text-primary transition-colors">
                      {language === 'en' ? leadStory.headlineEn : leadStory.headline}
                    </h2>
                    {leadStory.excerpt && (
                      <p className="text-muted-foreground mt-3">
                        {language === 'en' ? leadStory.excerptEn : leadStory.excerpt}
                      </p>
                    )}
                    <div className="flex items-center gap-2 mt-4 text-sm text-muted-foreground">
                      <Clock className="w-4 h-4" />
                      {leadStory.time}
                    </div>
                  </div>
                </article>
              </Link>
            )}

            {/* News Grid */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
              {otherNews.map((news, index) => (
                <NewsCard
                  key={index}
                  image={news.image}
                  category={news.category}
                  headline={language === 'en' ? news.headlineEn : news.headline}
                  time={news.time}
                  href={`/national/${news.state}`}
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
          <div className="lg:col-span-1 space-y-6">
            {/* Live Timeline */}
            <div className="bg-card rounded-xl p-5 shadow-card">
              <div className="flex items-center gap-2 mb-4">
                <div className="live-dot" />
                <h3 className="font-semibold">
                  {language === 'en' ? 'Live Updates' : 'લાઇવ અપડેટ્સ'}
                </h3>
              </div>
              <div className="space-y-4">
                {timelineNews.map((item, index) => (
                  <div key={index} className="flex gap-3 pb-4 border-b border-border last:border-0 last:pb-0">
                    <span className="text-xs font-medium text-primary whitespace-nowrap">
                      {item.time}
                    </span>
                    <p className="text-sm text-foreground">
                      {language === 'en' ? item.text : item.textGu}
                    </p>
                  </div>
                ))}
              </div>
            </div>

            <TrendingSidebar />
          </div>
        </div>
      </div>
    </PageLayout>
  );
};

export default National;
