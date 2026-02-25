import { useState } from 'react';
import { Cpu, Smartphone, Globe, Zap, Shield, Bot } from 'lucide-react';
import { PageLayout } from '@/components/layout/PageLayout';
import { NewsCard } from '@/components/news/NewsCard';
import { useLanguage } from '@/contexts/LanguageContext';

const techCategories = [
  { id: 'all', name: 'All', nameGu: 'બધા', icon: Cpu },
  { id: 'ai', name: 'AI & ML', nameGu: 'AI અને ML', icon: Bot },
  { id: 'gadgets', name: 'Gadgets', nameGu: 'ગેજેટ્સ', icon: Smartphone },
  { id: 'internet', name: 'Internet', nameGu: 'ઇન્ટરનેટ', icon: Globe },
  { id: 'startups', name: 'Startups', nameGu: 'સ્ટાર્ટઅપ', icon: Zap },
  { id: 'security', name: 'Security', nameGu: 'સિક્યુરિટી', icon: Shield },
];

const techNews = [
  {
    image: 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=600',
    headline: 'ChatGPT-5 લોન્ચ: AI ની નવી ક્રાંતિ',
    headlineEn: 'ChatGPT-5 Launch: New AI Revolution',
    category: 'ai',
    time: '30 min ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=600',
    headline: 'iPhone 16: Apple ની નવી જાહેરાત',
    headlineEn: 'iPhone 16: Apple new announcement',
    category: 'gadgets',
    time: '1 hour ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=600',
    headline: '5G નેટવર્ક: ભારતમાં વિસ્તરણ',
    headlineEn: '5G Network: Expansion in India',
    category: 'internet',
    time: '2 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1559136555-9303baea8ebd?w=600',
    headline: 'ભારતીય સ્ટાર્ટઅપે $100M ફંડિંગ મેળવ્યું',
    headlineEn: 'Indian startup raises $100M funding',
    category: 'startups',
    time: '3 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=600',
    headline: 'સાયબર સિક્યુરિટી: નવા ખતરાઓની ચેતવણી',
    headlineEn: 'Cybersecurity: Warning of new threats',
    category: 'security',
    time: '4 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=600',
    headline: 'Tesla Robot: ભવિષ્યનું ટેકનોલોજી',
    headlineEn: 'Tesla Robot: Technology of the future',
    category: 'ai',
    time: '5 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1593508512255-86ab42a8e620?w=600',
    headline: 'Meta Quest 4: VR ની નવી દુનિયા',
    headlineEn: 'Meta Quest 4: New world of VR',
    category: 'gadgets',
    time: '6 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=600',
    headline: 'SpaceX: મંગળ મિશનની તૈયારી',
    headlineEn: 'SpaceX: Preparing for Mars mission',
    category: 'startups',
    time: '7 hours ago',
  },
];

const Technology = () => {
  const { language } = useLanguage();
  const [selectedCategory, setSelectedCategory] = useState('all');

  const filteredNews = selectedCategory === 'all'
    ? techNews
    : techNews.filter(n => n.category === selectedCategory);

  return (
    <PageLayout>
      <div className="container mx-auto px-4 py-8">
        {/* Page Header */}
        <div className="bg-gradient-to-br from-primary via-primary/95 to-accent/90 rounded-2xl p-8 mb-8">
          <div className="flex items-center gap-3 mb-4">
            <Cpu className="w-8 h-8 text-primary-foreground" />
            <h1 className="headline-primary text-primary-foreground">
              {language === 'en' ? 'Technology' : 'ટેકનોલોજી'}
            </h1>
          </div>
          <p className="text-primary-foreground/80 max-w-2xl">
            {language === 'en' 
              ? 'Latest in AI, gadgets, startups, and digital innovation' 
              : 'AI, ગેજેટ્સ, સ્ટાર્ટઅપ અને ડિજિટલ ઇનોવેશનમાં તાજેતરનું'}
          </p>

          {/* Category Filter */}
          <div className="flex flex-wrap gap-3 mt-6">
            {techCategories.map((cat) => {
              const Icon = cat.icon;
              return (
                <button
                  key={cat.id}
                  onClick={() => setSelectedCategory(cat.id)}
                  className={`flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium transition-all ${
                    selectedCategory === cat.id
                      ? 'bg-accent text-accent-foreground'
                      : 'bg-primary-foreground/20 text-primary-foreground hover:bg-primary-foreground/30'
                  }`}
                >
                  <Icon className="w-4 h-4" />
                  {language === 'en' ? cat.name : cat.nameGu}
                </button>
              );
            })}
          </div>
        </div>

        {/* Quick Stats */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
          {[
            { icon: Bot, label: 'AI Tools', value: '2.5K+', color: 'text-blue-500' },
            { icon: Smartphone, label: 'New Gadgets', value: '150+', color: 'text-green-500' },
            { icon: Zap, label: 'Startups Funded', value: '$2.1B', color: 'text-yellow-500' },
            { icon: Shield, label: 'Security Alerts', value: '24', color: 'text-red-500' },
          ].map((stat, index) => (
            <div key={index} className="bg-card rounded-xl p-4 shadow-card text-center">
              <stat.icon className={`w-8 h-8 mx-auto mb-2 ${stat.color}`} />
              <p className="text-2xl font-bold text-foreground">{stat.value}</p>
              <p className="text-sm text-muted-foreground">{stat.label}</p>
            </div>
          ))}
        </div>

        {/* News Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {filteredNews.map((news, index) => (
            <NewsCard
              key={index}
              image={news.image}
              category={techCategories.find(c => c.id === news.category)?.name || 'Tech'}
              headline={language === 'en' ? news.headlineEn : news.headline}
              time={news.time}
            />
          ))}
        </div>
      </div>
    </PageLayout>
  );
};

export default Technology;
