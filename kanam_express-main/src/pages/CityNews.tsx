import { useParams, Link } from 'react-router-dom';
import { PageLayout } from '@/components/layout/PageLayout';
import { useLanguage } from '@/contexts/LanguageContext';
import { Search, MapPin, Clock, X } from 'lucide-react';
import { useState } from 'react';

const cityData: Record<string, { name: string; nameGu: string; region: string }> = {
  // Gujarat cities
  'ahmedabad': { name: 'Ahmedabad', nameGu: 'અમદાવાદ', region: 'gujarat' },
  'surat': { name: 'Surat', nameGu: 'સુરત', region: 'gujarat' },
  'vadodara': { name: 'Vadodara', nameGu: 'વડોદરા', region: 'gujarat' },
  'rajkot': { name: 'Rajkot', nameGu: 'રાજકોટ', region: 'gujarat' },
  'bhavnagar': { name: 'Bhavnagar', nameGu: 'ભાવનગર', region: 'gujarat' },
  'jamnagar': { name: 'Jamnagar', nameGu: 'જામનગર', region: 'gujarat' },
  'junagadh': { name: 'Junagadh', nameGu: 'જુનાગઢ', region: 'gujarat' },
  'gandhinagar': { name: 'Gandhinagar', nameGu: 'ગાંધીનગર', region: 'gujarat' },
  'anand': { name: 'Anand', nameGu: 'આણંદ', region: 'gujarat' },
  'bharuch': { name: 'Bharuch', nameGu: 'ભરૂચ', region: 'gujarat' },
  'mehsana': { name: 'Mehsana', nameGu: 'મહેસાણા', region: 'gujarat' },
  'morbi': { name: 'Morbi', nameGu: 'મોરબી', region: 'gujarat' },
  'nadiad': { name: 'Nadiad', nameGu: 'નડિયાદ', region: 'gujarat' },
  'porbandar': { name: 'Porbandar', nameGu: 'પોરબંદર', region: 'gujarat' },
  'vapi': { name: 'Vapi', nameGu: 'વાપી', region: 'gujarat' },
  'jambusar': { name: 'Jambusar', nameGu: 'જંબુસર', region: 'gujarat' },
  // National cities
  'delhi': { name: 'Delhi', nameGu: 'દિલ્હી', region: 'national' },
  'mumbai': { name: 'Mumbai', nameGu: 'મુંબઈ', region: 'national' },
  'bangalore': { name: 'Bangalore', nameGu: 'બેંગલુરુ', region: 'national' },
  'chennai': { name: 'Chennai', nameGu: 'ચેન્નાઈ', region: 'national' },
  'kolkata': { name: 'Kolkata', nameGu: 'કોલકાતા', region: 'national' },
  'hyderabad': { name: 'Hyderabad', nameGu: 'હૈદરાબાદ', region: 'national' },
  'pune': { name: 'Pune', nameGu: 'પુણે', region: 'national' },
  'jaipur': { name: 'Jaipur', nameGu: 'જયપુર', region: 'national' },
  'lucknow': { name: 'Lucknow', nameGu: 'લખનૌ', region: 'national' },
  'kanpur': { name: 'Kanpur', nameGu: 'કાનપુર', region: 'national' },
  // International cities
  'usa': { name: 'USA', nameGu: 'અમેરિકા', region: 'international' },
  'uk': { name: 'UK', nameGu: 'યુકે', region: 'international' },
  'dubai': { name: 'Dubai', nameGu: 'દુબઈ', region: 'international' },
  'singapore': { name: 'Singapore', nameGu: 'સિંગાપોર', region: 'international' },
  'australia': { name: 'Australia', nameGu: 'ઓસ્ટ્રેલિયા', region: 'international' },
  'canada': { name: 'Canada', nameGu: 'કેનેડા', region: 'international' },
  'china': { name: 'China', nameGu: 'ચીન', region: 'international' },
  'japan': { name: 'Japan', nameGu: 'જાપાન', region: 'international' },
  'pakistan': { name: 'Pakistan', nameGu: 'પાકિસ્તાન', region: 'international' },
  'russia': { name: 'Russia', nameGu: 'રશિયા', region: 'international' },
};

const generateNews = (cityName: string) => [
  {
    id: 1,
    image: 'https://images.unsplash.com/photo-1495020689067-958852a7765e?w=600',
    headline: `${cityName}માં નવા ડેવલપમેન્ટ પ્રોજેક્ટની જાહેરાત`,
    headlineEn: `New development project announced in ${cityName}`,
    time: '30 min ago',
  },
  {
    id: 2,
    image: 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=600',
    headline: `${cityName}: શિક્ષણ ક્ષેત્રે મોટી પહેલ`,
    headlineEn: `${cityName}: Major initiative in education sector`,
    time: '1 hour ago',
  },
  {
    id: 3,
    image: 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=600',
    headline: `${cityName}ના બજારોમાં તેજી, વ્યાપારીઓ ખુશ`,
    headlineEn: `Markets boom in ${cityName}, traders happy`,
    time: '2 hours ago',
  },
  {
    id: 4,
    image: 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=600',
    headline: `${cityName}: રમતગમત સ્પર્ધામાં ઉત્કૃષ્ટ પ્રદર્શન`,
    headlineEn: `${cityName}: Excellent performance in sports competition`,
    time: '3 hours ago',
  },
  {
    id: 5,
    image: 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=600',
    headline: `${cityName}માં ટેકનોલોજી હબ બનશે`,
    headlineEn: `${cityName} to become technology hub`,
    time: '4 hours ago',
  },
  {
    id: 6,
    image: 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?w=600',
    headline: `${cityName}: સામાજિક કાર્યક્રમનું આયોજન`,
    headlineEn: `${cityName}: Social event organized`,
    time: '5 hours ago',
  },
];

const CityNews = () => {
  const { city } = useParams<{ city: string }>();
  const { language } = useLanguage();
  const [searchQuery, setSearchQuery] = useState('');

  const cityInfo = city ? cityData[city.toLowerCase()] : null;
  const cityName = cityInfo 
    ? (language === 'en' ? cityInfo.name : cityInfo.nameGu)
    : city || 'City';
  
  const news = generateNews(cityInfo?.name || city || 'City');

  const filteredNews = news.filter(item => {
    const headline = language === 'en' ? item.headlineEn : item.headline;
    return headline.toLowerCase().includes(searchQuery.toLowerCase());
  });

  const getBackLink = () => {
    if (cityInfo?.region === 'national') return '/national';
    if (cityInfo?.region === 'international') return '/international';
    return '/gujarat';
  };

  return (
    <PageLayout showTicker={true}>
      <div className="container mx-auto px-4 py-8">
        {/* Page Header */}
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
          <div className="flex items-center gap-3">
            <div className="w-12 h-12 flex items-center justify-center bg-primary rounded-full">
              <MapPin className="w-6 h-6 text-primary-foreground" />
            </div>
            <div>
              <h1 className="text-3xl md:text-4xl font-bold text-foreground">
                {cityName}
              </h1>
              <Link to={getBackLink()} className="text-primary text-sm hover:underline">
                ← {language === 'en' ? 'Back to all news' : 'પાછા જાઓ'}
              </Link>
            </div>
          </div>

          {/* Search */}
          <div className="relative max-w-md w-full">
            <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" />
            <input
              type="text"
              placeholder={language === 'en' ? `Search ${cityName} news...` : `${cityName} સમાચાર શોધો...`}
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full pl-12 pr-10 py-3 rounded-full border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-primary"
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
        </div>

        {/* News Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredNews.map((item) => (
            <article key={item.id} className="news-card group cursor-pointer">
              <div className="aspect-[16/10] overflow-hidden">
                <img
                  src={item.image}
                  alt={item.headline}
                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                />
              </div>
              <div className="p-4">
                <span className="text-xs font-semibold text-primary uppercase tracking-wider">
                  {cityName}
                </span>
                <h3 className="headline-card text-foreground group-hover:text-primary transition-colors mt-2 line-clamp-2">
                  {language === 'en' ? item.headlineEn : item.headline}
                </h3>
                <span className="flex items-center gap-1 text-xs text-muted-foreground mt-3">
                  <Clock className="w-3 h-3" />
                  {item.time}
                </span>
              </div>
            </article>
          ))}
        </div>

        {filteredNews.length === 0 && (
          <div className="text-center py-12">
            <p className="text-muted-foreground text-lg">
              {language === 'en' ? 'No news found' : 'કોઈ સમાચાર મળ્યા નથી'}
            </p>
          </div>
        )}
      </div>
    </PageLayout>
  );
};

export default CityNews;
