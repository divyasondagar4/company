import { useState } from 'react';
import { Play, Heart, MessageCircle, Share2, Eye, ArrowLeft } from 'lucide-react';
import { Link } from 'react-router-dom';
import { useLanguage } from '@/contexts/LanguageContext';
import { PageLayout } from '@/components/layout/PageLayout';
import { useIsMobile } from '@/hooks/use-mobile';

const reels = [
  {
    id: 1,
    thumbnail: 'https://images.unsplash.com/photo-1495020689067-958852a7765e?w=600',
    headline: 'બ્રેકિંગ: ગુજરાત વિધાનસભામાં મહત્વનો નિર્ણય',
    headlineEn: 'Breaking: Important decision in Gujarat Assembly',
    likes: '12.5K',
    comments: '234',
    shares: '89',
    views: '45K',
    author: 'સંદેશ ન્યૂઝ',
  },
  {
    id: 2,
    thumbnail: 'https://images.unsplash.com/photo-1540747913346-19e32dc3e97e?w=600',
    headline: 'IPL 2024: ગુજરાત ટાઇટન્સની શાનદાર જીત',
    headlineEn: 'IPL 2024: Gujarat Titans spectacular victory',
    likes: '45.2K',
    comments: '1.2K',
    shares: '567',
    views: '120K',
    author: 'સ્પોર્ટ્સ ડેસ્ક',
  },
  {
    id: 3,
    thumbnail: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600',
    headline: 'Budget 2024: નાણામંત્રીની મહત્વની જાહેરાત',
    headlineEn: 'Budget 2024: Important announcement by FM',
    likes: '23.8K',
    comments: '456',
    shares: '234',
    views: '89K',
    author: 'બિઝનેસ ડેસ્ક',
  },
  {
    id: 4,
    thumbnail: 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=600',
    headline: 'AI Revolution: ભારતમાં ટેકનોલોજીનું ભવિષ્ય',
    headlineEn: 'AI Revolution: Future of technology in India',
    likes: '34.1K',
    comments: '789',
    shares: '345',
    views: '156K',
    author: 'ટેક ડેસ્ક',
  },
  {
    id: 5,
    thumbnail: 'https://images.unsplash.com/photo-1529665253569-6d01c0eaf7b6?w=600',
    headline: 'એક્સક્લુઝિવ: બોલીવુડ સ્ટાર સાથે ખાસ વાતચીત',
    headlineEn: 'Exclusive: Special chat with Bollywood star',
    likes: '67.3K',
    comments: '2.3K',
    shares: '890',
    views: '234K',
    author: 'એન્ટરટેઈનમેન્ટ',
  },
  {
    id: 6,
    thumbnail: 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=600',
    headline: 'ક્રિકેટ: ભારત vs ઓસ્ટ્રેલિયા મેચ હાઇલાઇટ્સ',
    headlineEn: 'Cricket: India vs Australia match highlights',
    likes: '78.5K',
    comments: '3.4K',
    shares: '1.2K',
    views: '345K',
    author: 'સ્પોર્ટ્સ ડેસ્ક',
  },
  {
    id: 7,
    thumbnail: 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=600',
    headline: 'શેરબજાર: સેન્સેક્સમાં મોટો ઉછાળો',
    headlineEn: 'Stock Market: Sensex sees big jump',
    likes: '15.2K',
    comments: '189',
    shares: '67',
    views: '67K',
    author: 'બિઝનેસ ડેસ્ક',
  },
  {
    id: 8,
    thumbnail: 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=600',
    headline: 'રાષ્ટ્રીય સમાચાર: દિલ્હીમાં મહત્વની બેઠક',
    headlineEn: 'National News: Important meeting in Delhi',
    likes: '28.9K',
    comments: '567',
    shares: '234',
    views: '98K',
    author: 'સંદેશ ન્યૂઝ',
  },
];

const Reels = () => {
  const { language } = useLanguage();
  const isMobile = useIsMobile();
  const [likedReels, setLikedReels] = useState<number[]>([]);
  const [activeReel, setActiveReel] = useState<number | null>(null);

  const toggleLike = (id: number, e: React.MouseEvent) => {
    e.stopPropagation();
    setLikedReels(prev => 
      prev.includes(id) ? prev.filter(r => r !== id) : [...prev, id]
    );
  };

  // Mobile: Full screen scrollable view
  if (isMobile) {
    return (
      <div className="fixed inset-0 bg-black overflow-y-auto snap-y snap-mandatory">
        {/* Back Button */}
        <Link 
          to="/"
          className="fixed top-4 left-4 z-50 text-white bg-white/10 backdrop-blur px-4 py-2 rounded-full text-sm font-medium hover:bg-white/20 transition-colors flex items-center gap-2"
        >
          <ArrowLeft className="w-4 h-4" />
          {language === 'en' ? 'Back' : 'પાછા'}
        </Link>

        {/* Logo */}
        <div className="fixed top-4 right-4 z-50">
          <span className="text-white font-serif text-2xl font-bold"></span>
        </div>

        {reels.map((reel) => (
          <div
            key={reel.id}
            className="h-screen w-full snap-start relative flex items-center justify-center"
          >
            <img
              src={reel.thumbnail}
              alt=""
              className="absolute inset-0 w-full h-full object-cover"
            />
            <div className="absolute inset-0 bg-gradient-to-b from-black/30 via-transparent to-black/80" />

            {/* Play Button */}
            <div className="w-20 h-20 flex items-center justify-center bg-white/20 backdrop-blur rounded-full cursor-pointer hover:bg-white/30 transition-colors">
              <Play className="w-10 h-10 text-white fill-white ml-1" />
            </div>

            {/* Content */}
            <div className="absolute bottom-20 left-0 right-16 p-6">
              <div className="flex items-center gap-3 mb-4">
                <div className="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
                  <span className="text-primary-foreground font-bold text-sm">
                    {reel.author.charAt(0)}
                  </span>
                </div>
                <span className="text-white font-medium">{reel.author}</span>
              </div>
              <h2 className="text-white text-lg font-bold leading-tight">
                {language === 'en' ? reel.headlineEn : reel.headline}
              </h2>
            </div>

            {/* Action Buttons */}
            <div className="absolute right-4 bottom-32 flex flex-col gap-6">
              <button 
                onClick={(e) => toggleLike(reel.id, e)}
                className="flex flex-col items-center"
              >
                <div className={`w-12 h-12 rounded-full flex items-center justify-center ${
                  likedReels.includes(reel.id) ? 'bg-red-500' : 'bg-white/10 backdrop-blur'
                }`}>
                  <Heart className={`w-6 h-6 ${
                    likedReels.includes(reel.id) ? 'text-white fill-white' : 'text-white'
                  }`} />
                </div>
                <span className="text-white text-xs mt-1">{reel.likes}</span>
              </button>

              <button className="flex flex-col items-center">
                <div className="w-12 h-12 rounded-full bg-white/10 backdrop-blur flex items-center justify-center">
                  <MessageCircle className="w-6 h-6 text-white" />
                </div>
                <span className="text-white text-xs mt-1">{reel.comments}</span>
              </button>

              <button className="flex flex-col items-center">
                <div className="w-12 h-12 rounded-full bg-white/10 backdrop-blur flex items-center justify-center">
                  <Share2 className="w-6 h-6 text-white" />
                </div>
                <span className="text-white text-xs mt-1">{reel.shares}</span>
              </button>
            </div>
          </div>
        ))}
      </div>
    );
  }

  // Desktop: Grid layout like video section
  return (
    <PageLayout>
      <div className="container mx-auto px-4 py-8">
        {/* Page Header */}
        <div className="flex items-center gap-3 mb-8">
          <div className="w-12 h-12 flex items-center justify-center bg-gradient-to-br from-primary to-accent rounded-full">
            <Play className="w-6 h-6 text-primary-foreground fill-current" />
          </div>
          <div>
            <h1 className="headline-primary text-foreground">
              {language === 'en' ? 'Reels' : 'રીલ્સ'}
            </h1>
            <p className="text-muted-foreground text-sm">
              {language === 'en' ? 'Watch short news videos' : 'ટૂંકા સમાચાર વિડિયો જુઓ'}
            </p>
          </div>
        </div>

        {/* Reels Grid */}
        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          {reels.map((reel) => (
            <div
              key={reel.id}
              className="group cursor-pointer"
              onClick={() => setActiveReel(activeReel === reel.id ? null : reel.id)}
            >
              <div className="relative aspect-[9/16] rounded-xl overflow-hidden bg-secondary">
                <img
                  src={reel.thumbnail}
                  alt=""
                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />
                
                {/* Play Button Overlay */}
                <div className="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                  <div className="w-16 h-16 flex items-center justify-center bg-primary/90 rounded-full">
                    <Play className="w-8 h-8 text-primary-foreground fill-current ml-1" />
                  </div>
                </div>

                {/* Views Badge */}
                <div className="absolute top-3 right-3 flex items-center gap-1 bg-black/50 backdrop-blur px-2 py-1 rounded-full">
                  <Eye className="w-3 h-3 text-white" />
                  <span className="text-white text-xs font-medium">{reel.views}</span>
                </div>

                {/* Content */}
                <div className="absolute bottom-0 left-0 right-0 p-4">
                  <div className="flex items-center gap-2 mb-2">
                    <div className="w-6 h-6 bg-primary rounded-full flex items-center justify-center">
                      <span className="text-primary-foreground font-bold text-xs">
                        {reel.author.charAt(0)}
                      </span>
                    </div>
                    <span className="text-white text-xs font-medium">{reel.author}</span>
                  </div>
                  <p className="text-white text-sm font-medium line-clamp-2">
                    {language === 'en' ? reel.headlineEn : reel.headline}
                  </p>
                </div>

                {/* Stats */}
                <div className="absolute bottom-4 left-4 right-4 flex items-center gap-4 text-white/70 text-xs mt-2 opacity-0 group-hover:opacity-100 transition-opacity translate-y-2 group-hover:translate-y-0">
                  <span className="flex items-center gap-1">
                    <Heart className="w-3 h-3" /> {reel.likes}
                  </span>
                  <span className="flex items-center gap-1">
                    <MessageCircle className="w-3 h-3" /> {reel.comments}
                  </span>
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* Load More */}
        <div className="text-center mt-8">
          <button className="px-8 py-3 bg-primary text-primary-foreground rounded-full font-medium hover:bg-primary/90 transition-colors">
            {language === 'en' ? 'Load More Reels' : 'વધુ રીલ્સ લોડ કરો'}
          </button>
        </div>
      </div>
    </PageLayout>
  );
};

export default Reels;
