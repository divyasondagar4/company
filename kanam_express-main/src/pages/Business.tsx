import { TrendingUp, TrendingDown, DollarSign, BarChart3 } from 'lucide-react';
import { PageLayout } from '@/components/layout/PageLayout';
import { NewsCard } from '@/components/news/NewsCard';
import { useLanguage } from '@/contexts/LanguageContext';

const marketData = [
  { name: 'SENSEX', value: '72,456.89', change: '+1.24%', isUp: true },
  { name: 'NIFTY 50', value: '21,890.45', change: '+0.98%', isUp: true },
  { name: 'BANK NIFTY', value: '45,678.12', change: '-0.34%', isUp: false },
  { name: 'USD/INR', value: '83.12', change: '+0.12%', isUp: true },
  { name: 'GOLD', value: '₹62,450', change: '+0.56%', isUp: true },
];

const businessNews = [
  {
    image: 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=600',
    headline: 'શેરબજાર: સેન્સેક્સ નવી ઊંચાઈએ, IT શેરોમાં તેજી',
    headlineEn: 'Stock Market: Sensex hits new high, IT stocks rally',
    category: 'Markets',
    time: '30 min ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=600',
    headline: 'RBI: વ્યાજ દરમાં ફેરફારની શક્યતા',
    headlineEn: 'RBI: Interest rate change likely',
    category: 'Banking',
    time: '1 hour ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=600',
    headline: 'સ્ટાર્ટઅપ ફંડિંગ: Q4માં રેકોર્ડ રોકાણ',
    headlineEn: 'Startup Funding: Record investment in Q4',
    category: 'Startups',
    time: '2 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600',
    headline: 'ટાટા ગ્રુપ: નવી કંપનીઓમાં રોકાણની યોજના',
    headlineEn: 'Tata Group: Plans to invest in new companies',
    category: 'Corporate',
    time: '3 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600',
    headline: 'ક્રિપ્ટો માર્કેટ: બિટકોઈન $50,000 પાર',
    headlineEn: 'Crypto Market: Bitcoin crosses $50,000',
    category: 'Crypto',
    time: '4 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=600',
    headline: 'રિયલ એસ્ટેટ: મુંબઈમાં ભાવ વધારો',
    headlineEn: 'Real Estate: Price increase in Mumbai',
    category: 'Real Estate',
    time: '5 hours ago',
  },
];

const Business = () => {
  const { language } = useLanguage();

  return (
    <PageLayout>
      <div className="container mx-auto px-4 py-8">
        {/* Page Header */}
        <div className="mb-8">
          <h1 className="headline-primary text-foreground flex items-center gap-3">
            <BarChart3 className="w-8 h-8 text-primary" />
            {language === 'en' ? 'Business & Markets' : 'બિઝનેસ અને માર્કેટ'}
          </h1>
        </div>

        {/* Market Ticker */}
        <div className="bg-card rounded-xl p-4 mb-8 shadow-card overflow-x-auto">
          <div className="flex gap-6 min-w-max">
            {marketData.map((item, index) => (
              <div key={index} className="flex items-center gap-3 px-4 py-2 border-r border-border last:border-0">
                <span className="font-medium text-foreground">{item.name}</span>
                <span className="font-bold text-lg">{item.value}</span>
                <span className={`flex items-center gap-1 text-sm font-medium ${
                  item.isUp ? 'text-green-600' : 'text-red-600'
                }`}>
                  {item.isUp ? <TrendingUp className="w-4 h-4" /> : <TrendingDown className="w-4 h-4" />}
                  {item.change}
                </span>
              </div>
            ))}
          </div>
        </div>

        {/* Quick Stats */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
          {[
            { label: language === 'en' ? 'Market Cap' : 'માર્કેટ કેપ', value: '₹350L Cr', icon: DollarSign },
            { label: language === 'en' ? 'Top Gainer' : 'ટોપ ગેઇનર', value: 'HDFC +5.2%', icon: TrendingUp },
            { label: language === 'en' ? 'Top Loser' : 'ટોપ લૂઝર', value: 'ICICI -2.1%', icon: TrendingDown },
            { label: language === 'en' ? 'FII Flow' : 'FII ફ્લો', value: '+₹2,450 Cr', icon: BarChart3 },
          ].map((stat, index) => (
            <div key={index} className="bg-card rounded-xl p-4 shadow-card">
              <div className="flex items-center gap-2 text-muted-foreground mb-2">
                <stat.icon className="w-4 h-4" />
                <span className="text-sm">{stat.label}</span>
              </div>
              <p className="text-xl font-bold text-foreground">{stat.value}</p>
            </div>
          ))}
        </div>

        {/* News Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {businessNews.map((news, index) => (
            <NewsCard
              key={index}
              image={news.image}
              category={news.category}
              headline={language === 'en' ? news.headlineEn : news.headline}
              time={news.time}
            />
          ))}
        </div>
      </div>
    </PageLayout>
  );
};

export default Business;
