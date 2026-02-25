import { useState } from 'react';
import { MapPin, Search, X } from 'lucide-react';
import { Link } from 'react-router-dom';
import { PageLayout } from '@/components/layout/PageLayout';
import { NewsCard } from '@/components/news/NewsCard';
import { TrendingSidebar } from '@/components/news/TrendingSidebar';
import { useLanguage } from '@/contexts/LanguageContext';
import { Input } from '@/components/ui/input';

const cities = [
  { id: 'all', name: 'બધા', nameEn: 'All' },
  { id: 'ahmedabad', name: 'અમદાવાદ', nameEn: 'Ahmedabad' },
  { id: 'surat', name: 'સુરત', nameEn: 'Surat' },
  { id: 'vadodara', name: 'વડોદરા', nameEn: 'Vadodara' },
  { id: 'rajkot', name: 'રાજકોટ', nameEn: 'Rajkot' },
  { id: 'bhavnagar', name: 'ભાવનગર', nameEn: 'Bhavnagar' },
  { id: 'jamnagar', name: 'જામનગર', nameEn: 'Jamnagar' },
  { id: 'gandhinagar', name: 'ગાંધીનગર', nameEn: 'Gandhinagar' },
  { id: 'junagadh', name: 'જૂનાગઢ', nameEn: 'Junagadh' },
  { id: 'anand', name: 'આણંદ', nameEn: 'Anand' },
  { id: 'bharuch', name: 'ભરૂચ', nameEn: 'Bharuch' },
  { id: 'jambusar', name: 'જંબુસર', nameEn: 'Jambusar' },
  { id: 'navsari', name: 'નવસારી', nameEn: 'Navsari' },
  { id: 'mehsana', name: 'મહેસાણા', nameEn: 'Mehsana' },
  { id: 'kutch', name: 'કચ્છ', nameEn: 'Kutch' },
  { id: 'patan', name: 'પાટણ', nameEn: 'Patan' },
  { id: 'morbi', name: 'મોરબી', nameEn: 'Morbi' },
  { id: 'porbandar', name: 'પોરબંદર', nameEn: 'Porbandar' },
  { id: 'vapi', name: 'વાપી', nameEn: 'Vapi' },
];

const gujaratNews = [
  {
    image: 'https://images.unsplash.com/photo-1567157577867-05ccb1388e66?w=600',
    headline: 'અમદાવાદ: BRTS કોરિડોરનું વિસ્તરણ, નવા 15 કિ.મી. રૂટ મંજૂર',
    headlineEn: 'Ahmedabad: BRTS corridor expansion, new 15 km route approved',
    city: 'ahmedabad',
    time: '1 hour ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?w=600',
    headline: 'સુરત: ડાયમંડ ઉદ્યોગમાં નવી તેજી, નિકાસમાં 20% વધારો',
    headlineEn: 'Surat: New boom in diamond industry, 20% increase in exports',
    city: 'surat',
    time: '2 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=600',
    headline: 'વડોદરા: MSU માં નવી રિસર્ચ લેબનું ઉદ્ઘાટન',
    headlineEn: 'Vadodara: New research lab inaugurated at MSU',
    city: 'vadodara',
    time: '3 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=600',
    headline: 'રાજકોટ: સ્માર્ટ સિટી પ્રોજેક્ટ હેઠળ નવા પાર્ક',
    headlineEn: 'Rajkot: New parks under Smart City project',
    city: 'rajkot',
    time: '4 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=600',
    headline: 'ગુજરાત: નવા ઔદ્યોગિક નીતિમાં MSMEsને વિશેષ લાભ',
    headlineEn: 'Gujarat: Special benefits for MSMEs in new industrial policy',
    city: 'all',
    time: '5 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=600',
    headline: 'ભાવનગર: પોર્ટના વિસ્તરણ માટે ₹500 કરોડની મંજૂરી',
    headlineEn: 'Bhavnagar: Rs 500 crore approved for port expansion',
    city: 'bhavnagar',
    time: '6 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?w=600',
    headline: 'જામનગર: રિફાઇનરી વિસ્તરણથી 5000 નવી નોકરીઓ',
    headlineEn: 'Jamnagar: Refinery expansion to create 5000 new jobs',
    city: 'jamnagar',
    time: '7 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=600',
    headline: 'જંબુસર: સ્થાનિક બજારમાં તેજી, ખેડૂતોને ફાયદો',
    headlineEn: 'Jambusar: Local market boom, farmers benefit',
    city: 'jambusar',
    time: '8 hours ago',
  },
];

const Gujarat = () => {
  const { language } = useLanguage();
  const [selectedCity, setSelectedCity] = useState('all');
  const [searchQuery, setSearchQuery] = useState('');
  const [showSearch, setShowSearch] = useState(false);

  const filteredNews = gujaratNews.filter(news => {
    const matchesCity = selectedCity === 'all' || news.city === selectedCity || news.city === 'all';
    const matchesSearch = searchQuery === '' || 
      news.headline.toLowerCase().includes(searchQuery.toLowerCase()) ||
      news.headlineEn.toLowerCase().includes(searchQuery.toLowerCase());
    return matchesCity && matchesSearch;
  });

  return (
    <PageLayout>
      <div className="container mx-auto px-4 py-8">
        {/* Page Header */}
        <div className="flex flex-col gap-4 mb-6">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <h1 className="headline-primary text-foreground">
                {language === 'en' ? 'Gujarat News' : 'ગુજરાત સમાચાર'}
              </h1>
              <p className="text-muted-foreground mt-1">
                {language === 'en' 
                  ? 'Latest news from across Gujarat' 
                  : 'સમગ્ર ગુજરાતના તાજા સમાચાર'}
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
                placeholder={language === 'en' ? 'Search Gujarat news or city...' : 'ગુજરાત સમાચાર અથવા શહેર શોધો...'}
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

        {/* City Filter - Horizontal Scroll */}
        <div className="overflow-x-auto scrollbar-hide mb-8">
          <div className="flex items-center gap-2 min-w-max pb-2">
            {cities.map((city) => (
              city.id === 'all' ? (
                <button
                  key={city.id}
                  onClick={() => setSelectedCity(city.id)}
                  className={`px-4 py-2 text-sm font-medium rounded-full transition-colors whitespace-nowrap ${
                    selectedCity === city.id
                      ? 'bg-primary text-primary-foreground'
                      : 'bg-secondary text-secondary-foreground hover:bg-primary/10'
                  }`}
                >
                  <span className="flex items-center gap-1">
                    <MapPin className="w-3 h-3" />
                    {language === 'en' ? city.nameEn : city.name}
                  </span>
                </button>
              ) : (
                <Link
                  key={city.id}
                  to={`/gujarat/${city.id}`}
                  onClick={() => setSelectedCity(city.id)}
                  className={`px-4 py-2 text-sm font-medium rounded-full transition-colors whitespace-nowrap ${
                    selectedCity === city.id
                      ? 'bg-primary text-primary-foreground'
                      : 'bg-secondary text-secondary-foreground hover:bg-primary/10'
                  }`}
                >
                  <span className="flex items-center gap-1">
                    <MapPin className="w-3 h-3" />
                    {language === 'en' ? city.nameEn : city.name}
                  </span>
                </Link>
              )
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
                  category={language === 'en' ? 'Gujarat' : 'ગુજરાત'}
                  headline={language === 'en' ? news.headlineEn : news.headline}
                  time={news.time}
                  href={`/gujarat/${news.city === 'all' ? 'ahmedabad' : news.city}`}
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

export default Gujarat;
