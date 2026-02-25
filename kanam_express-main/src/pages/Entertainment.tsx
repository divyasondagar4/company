import { useState } from 'react';
import { Film, Tv, Music, Star, Sparkles } from 'lucide-react';
import { PageLayout } from '@/components/layout/PageLayout';
import { NewsCard } from '@/components/news/NewsCard';
import { useLanguage } from '@/contexts/LanguageContext';

const categories = [
  { id: 'all', name: 'All', nameGu: 'બધા', icon: Sparkles },
  { id: 'bollywood', name: 'Bollywood', nameGu: 'બોલીવુડ', icon: Film },
  { id: 'hollywood', name: 'Hollywood', nameGu: 'હોલીવુડ', icon: Star },
  { id: 'tv', name: 'TV Shows', nameGu: 'ટીવી શો', icon: Tv },
  { id: 'music', name: 'Music', nameGu: 'સંગીત', icon: Music },
];

const entertainmentNews = [
  {
    image: 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=600',
    headline: 'શાહરુખ ખાનની નવી ફિલ્મની જાહેરાત',
    headlineEn: 'Shah Rukh Khan announces new film',
    category: 'bollywood',
    time: '1 hour ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=600',
    headline: 'ઓસ્કાર 2024: ભારતીય ફિલ્મને નોમિનેશન',
    headlineEn: 'Oscar 2024: Indian film gets nomination',
    category: 'hollywood',
    time: '2 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1522869635100-9f4c5e86aa37?w=600',
    headline: 'બિગ બોસ: આ અઠવાડિયે કોણ થશે eliminate?',
    headlineEn: 'Bigg Boss: Who will be eliminated this week?',
    category: 'tv',
    time: '3 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=600',
    headline: 'અરિજિત સિંહનો નવો ગીત વાયરલ',
    headlineEn: 'Arijit Singh new song goes viral',
    category: 'music',
    time: '4 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1478720568477-152d9b164e26?w=600',
    headline: 'આલિયા ભટ્ટ હોલીવુડ ફિલ્મમાં',
    headlineEn: 'Alia Bhatt in Hollywood film',
    category: 'bollywood',
    time: '5 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1440404653325-ab127d49abc1?w=600',
    headline: 'Netflix ની નવી વેબ સિરીઝ',
    headlineEn: 'Netflix new web series',
    category: 'tv',
    time: '6 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=600',
    headline: 'ગ્રેમી એવોર્ડ્સ: ભારતીય કલાકારોનું પ્રદર્શન',
    headlineEn: 'Grammy Awards: Indian artists perform',
    category: 'music',
    time: '7 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1485846234645-a62644f84728?w=600',
    headline: 'મોટી જાહેરાત: નવી માર્વેલ ફિલ્મ',
    headlineEn: 'Big announcement: New Marvel film',
    category: 'hollywood',
    time: '8 hours ago',
  },
];

const Entertainment = () => {
  const { language } = useLanguage();
  const [selectedCategory, setSelectedCategory] = useState('all');

  const filteredNews = selectedCategory === 'all'
    ? entertainmentNews
    : entertainmentNews.filter(n => n.category === selectedCategory);

  return (
    <PageLayout>
      <div className="container mx-auto px-4 py-8">
        {/* Page Header */}
        <div className="bg-gradient-to-r from-accent/20 via-primary/10 to-accent/20 rounded-2xl p-8 mb-8">
          <div className="flex items-center gap-3 mb-4">
            <Sparkles className="w-8 h-8 text-accent" />
            <h1 className="headline-primary text-foreground">
              {language === 'en' ? 'Entertainment' : 'મનોરંજન'}
            </h1>
          </div>
          <p className="text-muted-foreground">
            {language === 'en' 
              ? 'Bollywood, Hollywood, TV Shows, Music and more' 
              : 'બોલીવુડ, હોલીવુડ, ટીવી શો, સંગીત અને વધુ'}
          </p>

          {/* Category Filter */}
          <div className="flex flex-wrap gap-3 mt-6">
            {categories.map((cat) => {
              const Icon = cat.icon;
              return (
                <button
                  key={cat.id}
                  onClick={() => setSelectedCategory(cat.id)}
                  className={`flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium transition-all ${
                    selectedCategory === cat.id
                      ? 'bg-primary text-primary-foreground'
                      : 'bg-card text-foreground hover:bg-primary/10'
                  }`}
                >
                  <Icon className="w-4 h-4" />
                  {language === 'en' ? cat.name : cat.nameGu}
                </button>
              );
            })}
          </div>
        </div>

        {/* Featured Celebrity */}
        <div className="bg-card rounded-xl p-6 mb-8 shadow-card">
          <h2 className="section-title mb-4">
            {language === 'en' ? 'Featured' : 'ફીચર્ડ'}
          </h2>
          <div className="flex flex-col md:flex-row gap-6">
            <img
              src="https://images.unsplash.com/photo-1529665253569-6d01c0eaf7b6?w=400"
              alt="Featured"
              className="w-full md:w-48 h-48 object-cover rounded-xl"
            />
            <div className="flex-1">
              <span className="category-tag">Exclusive</span>
              <h3 className="headline-secondary mt-2">
                {language === 'en' 
                  ? 'Exclusive Interview: Deepika Padukone on her upcoming projects' 
                  : 'એક્સક્લુઝિવ ઇન્ટરવ્યૂ: દીપિકા પાદુકોણ તેના આગામી પ્રોજેક્ટ્સ વિશે'}
              </h3>
              <p className="text-muted-foreground mt-2">
                {language === 'en'
                  ? 'The actress opens up about her journey, challenges, and what fans can expect next.'
                  : 'અભિનેત્રી તેની યાત્રા, પડકારો અને ચાહકો આગળ શું અપેક્ષા રાખી શકે તે વિશે વાત કરે છે.'}
              </p>
            </div>
          </div>
        </div>

        {/* News Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {filteredNews.map((news, index) => (
            <NewsCard
              key={index}
              image={news.image}
              category={categories.find(c => c.id === news.category)?.name || 'Entertainment'}
              headline={language === 'en' ? news.headlineEn : news.headline}
              time={news.time}
            />
          ))}
        </div>
      </div>
    </PageLayout>
  );
};

export default Entertainment;
