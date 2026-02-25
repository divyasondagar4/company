import { useState } from 'react';
import { Play, Filter, Grid, List } from 'lucide-react';
import { PageLayout } from '@/components/layout/PageLayout';
import { VideoCard } from '@/components/news/VideoCard';
import { useLanguage } from '@/contexts/LanguageContext';

const videoCategories = [
  { id: 'all', name: 'All Videos', nameGu: 'બધા વિડિયો' },
  { id: 'news', name: 'News Videos', nameGu: 'સમાચાર વિડિયો' },
  { id: 'interviews', name: 'Interviews', nameGu: 'ઇન્ટરવ્યૂ' },
  { id: 'reports', name: 'Ground Reports', nameGu: 'ગ્રાઉન્ડ રિપોર્ટ' },
  { id: 'explainers', name: 'Explainers', nameGu: 'એક્સપ્લેનર્સ' },
  { id: 'sports', name: 'Sports', nameGu: 'રમતગમત' },
];

const videos = [
  {
    thumbnail: 'https://images.unsplash.com/photo-1495020689067-958852a7765e?w=600',
    title: 'બ્રેકિંગ: ગુજરાત વિધાનસભામાં ઐતિહાસિક બિલ પસાર',
    titleEn: 'Breaking: Historic bill passed in Gujarat Assembly',
    duration: '15:30',
    views: '125K',
    category: 'news',
  },
  {
    thumbnail: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600',
    title: 'એક્સક્લુઝિવ ઇન્ટરવ્યૂ: મુખ્યમંત્રી સાથે વાતચીત',
    titleEn: 'Exclusive Interview: Conversation with Chief Minister',
    duration: '22:45',
    views: '89K',
    category: 'interviews',
  },
  {
    thumbnail: 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=600',
    title: 'ગ્રાઉન્ડ રિપોર્ટ: સુરતના ડાયમંડ ઉદ્યોગની સ્થિતિ',
    titleEn: 'Ground Report: Status of Surat diamond industry',
    duration: '18:20',
    views: '67K',
    category: 'reports',
  },
  {
    thumbnail: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600',
    title: 'Budget 2024 સમજો સરળ ભાષામાં',
    titleEn: 'Understand Budget 2024 in simple language',
    duration: '12:15',
    views: '234K',
    category: 'explainers',
  },
  {
    thumbnail: 'https://images.unsplash.com/photo-1540747913346-19e32dc3e97e?w=600',
    title: 'IPL 2024: ગુજરાત ટાઇટન્સ vs મુંબઈ ઇન્ડિયન્સ હાઇલાઇટ્સ',
    titleEn: 'IPL 2024: Gujarat Titans vs Mumbai Indians Highlights',
    duration: '10:30',
    views: '456K',
    category: 'sports',
  },
  {
    thumbnail: 'https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?w=600',
    title: 'સ્પેશિયલ રિપોર્ટ: ગુજરાતના વિકાસ મોડલની સમીક્ષા',
    titleEn: 'Special Report: Review of Gujarat development model',
    duration: '25:00',
    views: '78K',
    category: 'reports',
  },
  {
    thumbnail: 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=600',
    title: 'AI અને ભવિષ્ય: ટેકનોલોજી એક્સપ્લેનર',
    titleEn: 'AI and Future: Technology Explainer',
    duration: '14:45',
    views: '156K',
    category: 'explainers',
  },
  {
    thumbnail: 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=600',
    title: 'ભારત vs ઓસ્ટ્રેલિયા: ટેસ્ટ મેચ ડે 3 હાઇલાઇટ્સ',
    titleEn: 'India vs Australia: Test match Day 3 Highlights',
    duration: '8:45',
    views: '389K',
    category: 'sports',
  },
  {
    thumbnail: 'https://images.unsplash.com/photo-1532375810709-75b1da00537c?w=600',
    title: 'રાજકીય વિશ્લેષણ: ચૂંટણી પરિણામોની સમીક્ષા',
    titleEn: 'Political Analysis: Review of election results',
    duration: '20:00',
    views: '123K',
    category: 'news',
  },
  {
    thumbnail: 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=600',
    title: 'બિઝનેસ ન્યૂઝ: શેરબજારનું સાપ્તાહિક વિશ્લેષણ',
    titleEn: 'Business News: Weekly stock market analysis',
    duration: '16:30',
    views: '92K',
    category: 'news',
  },
  {
    thumbnail: 'https://images.unsplash.com/photo-1517649763962-0c623066013b?w=600',
    title: 'એક્સક્લુઝિવ: ક્રિકેટ સ્ટાર સાથે ખાસ વાતચીત',
    titleEn: 'Exclusive: Special conversation with cricket star',
    duration: '28:00',
    views: '567K',
    category: 'interviews',
  },
  {
    thumbnail: 'https://images.unsplash.com/photo-1526628953301-3e589a6a8b74?w=600',
    title: 'ટેક્નોલોજી ટ્રેન્ડ્સ 2024: શું છે નવું?',
    titleEn: 'Technology Trends 2024: What is new?',
    duration: '11:20',
    views: '145K',
    category: 'explainers',
  },
];

const Videos = () => {
  const { language } = useLanguage();
  const [selectedCategory, setSelectedCategory] = useState('all');
  const [viewMode, setViewMode] = useState<'grid' | 'list'>('grid');

  const filteredVideos = selectedCategory === 'all'
    ? videos
    : videos.filter(v => v.category === selectedCategory);

  return (
    <PageLayout showTicker={true}>
      <div className="container mx-auto px-4 py-8">
        {/* Page Header */}
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
          <div className="flex items-center gap-3">
            <div className="w-12 h-12 flex items-center justify-center bg-primary rounded-full">
              <Play className="w-6 h-6 text-primary-foreground fill-current" />
            </div>
            <div>
              <h1 className="headline-primary text-foreground">
                {language === 'en' ? 'Videos' : 'વિડિયો'}
              </h1>
              <p className="text-muted-foreground text-sm">
                {language === 'en' ? 'Watch latest news videos' : 'તાજા સમાચાર વિડિયો જુઓ'}
              </p>
            </div>
          </div>

          {/* View Toggle */}
          <div className="flex items-center gap-2 bg-secondary rounded-lg p-1">
            <button
              onClick={() => setViewMode('grid')}
              className={`p-2 rounded ${viewMode === 'grid' ? 'bg-card shadow' : ''}`}
            >
              <Grid className="w-5 h-5" />
            </button>
            <button
              onClick={() => setViewMode('list')}
              className={`p-2 rounded ${viewMode === 'list' ? 'bg-card shadow' : ''}`}
            >
              <List className="w-5 h-5" />
            </button>
          </div>
        </div>

        {/* Category Filter */}
        <div className="flex flex-wrap gap-3 mb-8">
          {videoCategories.map((cat) => (
            <button
              key={cat.id}
              onClick={() => setSelectedCategory(cat.id)}
              className={`px-4 py-2 rounded-full text-sm font-medium transition-all ${
                selectedCategory === cat.id
                  ? 'bg-primary text-primary-foreground'
                  : 'bg-secondary text-secondary-foreground hover:bg-primary/10'
              }`}
            >
              {language === 'en' ? cat.name : cat.nameGu}
            </button>
          ))}
        </div>

        {/* Video Grid */}
        <div className={`grid gap-6 ${
          viewMode === 'grid' 
            ? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4' 
            : 'grid-cols-1'
        }`}>
          {filteredVideos.map((video, index) => (
            <VideoCard
              key={index}
              thumbnail={video.thumbnail}
              title={language === 'en' ? video.titleEn : video.title}
              duration={video.duration}
              views={video.views}
              category={videoCategories.find(c => c.id === video.category)?.name}
            />
          ))}
        </div>

        {/* Load More */}
        <div className="text-center mt-8">
          <button className="px-8 py-3 bg-primary text-primary-foreground rounded-full font-medium hover:bg-primary/90 transition-colors">
            {language === 'en' ? 'Load More Videos' : 'વધુ વિડિયો લોડ કરો'}
          </button>
        </div>
      </div>
    </PageLayout>
  );
};

export default Videos;
