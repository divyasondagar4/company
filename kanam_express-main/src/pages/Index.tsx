import { Link } from 'react-router-dom';
import { ChevronRight } from 'lucide-react';
import { PageLayout } from '@/components/layout/PageLayout';
import { LeadStory } from '@/components/news/LeadStory';
import { NewsCard } from '@/components/news/NewsCard';
import { TrendingSidebar } from '@/components/news/TrendingSidebar';
import { EditorsPick } from '@/components/sections/EditorsPick';
import { VideoSection } from '@/components/sections/VideoSection';
import { ReelsSection } from '@/components/sections/ReelsSection';
import { useLanguage } from '@/contexts/LanguageContext';

const mainGridNews = [
  {
    image: 'https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?w=600',
    category: 'ગુજરાત',
    categoryEn: 'Gujarat',
    headline: 'સુરતમાં નવા IT પાર્કનું ઉદ્ઘાટન, 10,000 રોજગારી સર્જાશે',
    headlineEn: 'New IT park inaugurated in Surat, to create 10,000 jobs',
    time: '2 hours ago',
    href: '/gujarat/surat',
  },
  {
    image: 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=600',
    category: 'રાષ્ટ્રીય',
    categoryEn: 'National',
    headline: 'સંસદમાં નવા બિલ પર ચર્ચા, વિપક્ષનો વિરોધ',
    headlineEn: 'Parliament debates new bill, opposition protests',
    time: '3 hours ago',
    href: '/national/delhi',
  },
  {
    image: 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=600',
    category: 'રમતગમત',
    categoryEn: 'Sports',
    headline: 'ક્રિકેટ: ભારતે ઓસ્ટ્રેલિયાને 200 રનથી હરાવ્યું',
    headlineEn: 'Cricket: India defeats Australia by 200 runs',
    time: '4 hours ago',
    href: '/sports',
  },
  {
    image: 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=600',
    category: 'બિઝનેસ',
    categoryEn: 'Business',
    headline: 'શેરબજાર: સેન્સેક્સ 500 પોઇન્ટ ઉછળ્યો',
    headlineEn: 'Stock Market: Sensex jumps 500 points',
    time: '5 hours ago',
    href: '/business',
  },
  {
    image: 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=600',
    category: 'ટેકનોલોજી',
    categoryEn: 'Technology',
    headline: 'નવી AI ટેકનોલોજી: ભારતીય સ્ટાર્ટઅપની સિદ્ધિ',
    headlineEn: 'New AI technology: Indian startup achievement',
    time: '6 hours ago',
    href: '/technology',
  },
  {
    image: 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?w=600',
    category: 'આંતરરાષ્ટ્રીય',
    categoryEn: 'International',
    headline: 'G20 સમિટ: વૈશ્વિક નેતાઓની મહત્વની બેઠક',
    headlineEn: 'G20 Summit: Important meeting of global leaders',
    time: '7 hours ago',
    href: '/international',
  },
];

const Index = () => {
  const { language } = useLanguage();

  return (
    <PageLayout>
      <div className="container mx-auto px-4 py-8">
        {/* Lead Story */}
        <LeadStory
          image="https://images.unsplash.com/photo-1495020689067-958852a7765e?w=1200"
          category={language === 'en' ? 'Breaking' : 'બ્રેકિંગ'}
          headline={
            language === 'en'
              ? 'Gujarat Assembly: Historic bill passed with unanimous support'
              : 'ગુજરાત વિધાનસભા: સર્વાનુમતે ઐતિહાસિક બિલ પસાર'
          }
          excerpt={
            language === 'en'
              ? 'In a historic session, the Gujarat Legislative Assembly passed a landmark bill that will transform the state\'s education and healthcare sectors.'
              : 'એક ઐતિહાસિક સત્રમાં, ગુજરાત વિધાનસભાએ એક મહત્વપૂર્ણ બિલ પસાર કર્યું જે રાજ્યના શિક્ષણ અને આરોગ્ય ક્ષેત્રોને પરિવર્તિત કરશે.'
          }
          author={language === 'en' ? 'Kanam Express Bureau' : 'કાનમ એક્સપ્રેસ બ્યુરો'}
          time={language === 'en' ? '30 minutes ago' : '30 મિનિટ પહેલા'}
          href="/gujarat"
        />

        {/* Top News Heading */}
        <div className="mt-8 mb-6 flex items-center justify-between">
          <h2 className="section-title">
            {language === 'en' ? 'Top News' : 'ટોચના સમાચાર'}
          </h2>
          <Link 
            to="/latest" 
            className="flex items-center gap-1 text-sm font-medium text-primary hover:text-primary/80 transition-colors"
          >
            {language === 'en' ? 'View All' : 'બધા જુઓ'}
            <ChevronRight className="w-4 h-4" />
          </Link>
        </div>

        {/* Main Content Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Main News Column */}
          <div className="lg:col-span-2">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
              {mainGridNews.slice(0, 4).map((news, index) => (
                <NewsCard
                  key={index}
                  image={news.image}
                  category={language === 'en' ? news.categoryEn : news.category}
                  headline={language === 'en' ? news.headlineEn : news.headline}
                  time={news.time}
                  href={news.href}
                />
              ))}
            </div>

            {/* Horizontal Cards */}
            <div className="mt-6 space-y-4">
              {mainGridNews.slice(4).map((news, index) => (
                <NewsCard
                  key={index}
                  image={news.image}
                  category={language === 'en' ? news.categoryEn : news.category}
                  headline={language === 'en' ? news.headlineEn : news.headline}
                  time={news.time}
                  variant="horizontal"
                  href={news.href}
                />
              ))}
            </div>

            {/* Editor's Pick */}
            <EditorsPick />
          </div>

          {/* Sidebar */}
          <div className="lg:col-span-1">
            <TrendingSidebar />
          </div>
        </div>

        {/* Video Section */}
        <VideoSection />

        {/* Reels Section */}
        <ReelsSection />
      </div>
    </PageLayout>
  );
};

export default Index;
