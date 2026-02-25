import { useState } from 'react';
import { Trophy, Timer, Users } from 'lucide-react';
import { PageLayout } from '@/components/layout/PageLayout';
import { NewsCard } from '@/components/news/NewsCard';
import { useLanguage } from '@/contexts/LanguageContext';

const sportCategories = [
  { id: 'all', name: 'All', nameGu: 'બધા', icon: '🏆' },
  { id: 'cricket', name: 'Cricket', nameGu: 'ક્રિકેટ', icon: '🏏' },
  { id: 'football', name: 'Football', nameGu: 'ફૂટબોલ', icon: '⚽' },
  { id: 'hockey', name: 'Hockey', nameGu: 'હોકી', icon: '🏑' },
  { id: 'tennis', name: 'Tennis', nameGu: 'ટેનિસ', icon: '🎾' },
  { id: 'kabaddi', name: 'Kabaddi', nameGu: 'કબડ્ડી', icon: '🤼' },
];

const liveMatches = [
  {
    sport: 'cricket',
    team1: { name: 'India', score: '287/4', flag: '🇮🇳' },
    team2: { name: 'Australia', score: '0/0', flag: '🇦🇺' },
    status: 'Day 1, Session 3',
    isLive: true,
  },
  {
    sport: 'football',
    team1: { name: 'Man City', score: '2', flag: '🏴󠁧󠁢󠁥󠁮󠁧󠁿' },
    team2: { name: 'Liverpool', score: '1', flag: '🏴󠁧󠁢󠁥󠁮󠁧󠁿' },
    status: '78\'',
    isLive: true,
  },
];

const sportsNews = [
  {
    image: 'https://images.unsplash.com/photo-1531415074968-036ba1b575da?w=600',
    headline: 'IPL 2024: ગુજરાત ટાઇટન્સનો શાનદાર વિજય',
    headlineEn: 'IPL 2024: Gujarat Titans spectacular victory',
    category: 'cricket',
    time: '1 hour ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=600',
    headline: 'ભારત vs ઓસ્ટ્રેલિયા: ટેસ્ટ મેચમાં ભારતનો દબદબો',
    headlineEn: 'India vs Australia: India dominates Test match',
    category: 'cricket',
    time: '2 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?w=600',
    headline: 'ISL: મુંબઈ સિટી FCની જીત',
    headlineEn: 'ISL: Mumbai City FC wins',
    category: 'football',
    time: '3 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1587280501635-68a0e82cd5ff?w=600',
    headline: 'હોકી: ભારતની ઓલિમ્પિક તૈયારીઓ',
    headlineEn: 'Hockey: India Olympic preparations',
    category: 'hockey',
    time: '4 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1554068865-24cecd4e34b8?w=600',
    headline: 'ટેનિસ: સાનિયા મિર્ઝાની વિદાય મેચ',
    headlineEn: 'Tennis: Sania Mirza farewell match',
    category: 'tennis',
    time: '5 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1517649763962-0c623066013b?w=600',
    headline: 'PKL: પાટણા પાયરેટ્સની હાર',
    headlineEn: 'PKL: Patna Pirates loses',
    category: 'kabaddi',
    time: '6 hours ago',
  },
];

const Sports = () => {
  const { language } = useLanguage();
  const [selectedSport, setSelectedSport] = useState('all');

  const filteredNews = selectedSport === 'all'
    ? sportsNews
    : sportsNews.filter(n => n.category === selectedSport);

  return (
    <PageLayout>
      <div className="container mx-auto px-4 py-8">
        {/* Page Header */}
        <div className="flex items-center gap-3 mb-6">
          <Trophy className="w-8 h-8 text-accent" />
          <h1 className="headline-primary text-foreground">
            {language === 'en' ? 'Sports' : 'રમતગમત'}
          </h1>
        </div>

        {/* Live Matches */}
        <div className="bg-gradient-to-r from-primary to-primary/80 rounded-2xl p-6 mb-8 text-primary-foreground">
          <div className="flex items-center gap-2 mb-4">
            <div className="live-dot bg-accent" />
            <h2 className="font-bold text-lg">
              {language === 'en' ? 'Live Matches' : 'લાઇવ મેચ'}
            </h2>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {liveMatches.map((match, index) => (
              <div key={index} className="bg-white/10 backdrop-blur rounded-xl p-4">
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-3">
                    <span className="text-2xl">{match.team1.flag}</span>
                    <div>
                      <p className="font-semibold">{match.team1.name}</p>
                      <p className="text-xl font-bold">{match.team1.score}</p>
                    </div>
                  </div>
                  <div className="text-center">
                    <p className="text-xs uppercase opacity-80">vs</p>
                    <p className="text-sm mt-1 bg-accent text-accent-foreground px-2 py-1 rounded">
                      {match.status}
                    </p>
                  </div>
                  <div className="flex items-center gap-3 text-right">
                    <div>
                      <p className="font-semibold">{match.team2.name}</p>
                      <p className="text-xl font-bold">{match.team2.score}</p>
                    </div>
                    <span className="text-2xl">{match.team2.flag}</span>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Sport Category Filter */}
        <div className="flex flex-wrap gap-3 mb-8">
          {sportCategories.map((sport) => (
            <button
              key={sport.id}
              onClick={() => setSelectedSport(sport.id)}
              className={`flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium transition-all ${
                selectedSport === sport.id
                  ? 'bg-primary text-primary-foreground'
                  : 'bg-secondary text-secondary-foreground hover:bg-primary/10'
              }`}
            >
              <span>{sport.icon}</span>
              {language === 'en' ? sport.name : sport.nameGu}
            </button>
          ))}
        </div>

        {/* News Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredNews.map((news, index) => (
            <NewsCard
              key={index}
              image={news.image}
              category={sportCategories.find(s => s.id === news.category)?.name || 'Sports'}
              headline={language === 'en' ? news.headlineEn : news.headline}
              time={news.time}
            />
          ))}
        </div>
      </div>
    </PageLayout>
  );
};

export default Sports;
