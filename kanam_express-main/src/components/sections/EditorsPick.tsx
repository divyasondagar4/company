import { Star } from 'lucide-react';
import { useLanguage } from '@/contexts/LanguageContext';
import { NewsCard } from '@/components/news/NewsCard';

const editorsPicks = [
  {
    image: 'https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?w=600',
    category: 'ઓપિનિયન',
    categoryEn: 'Opinion',
    headline: 'ગુજરાતના વિકાસ મોડલની સફળતા: એક વિશ્લેષણ',
    headlineEn: 'Success of Gujarat Development Model: An Analysis',
    excerpt: 'છેલ્લા બે દાયકામાં ગુજરાતે આર્થિક વિકાસમાં નોંધપાત્ર સિદ્ધિઓ હાંસલ કરી છે.',
    excerptEn: 'Gujarat has achieved remarkable economic growth in the last two decades.',
    time: '3 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1526628953301-3e589a6a8b74?w=600',
    category: 'ટેકનોલોજી',
    categoryEn: 'Technology',
    headline: 'AI Revolution: ભારતમાં ટેકનોલોજીનું ભવિષ્ય',
    headlineEn: 'AI Revolution: The Future of Technology in India',
    excerpt: 'આર્ટિફિશિયલ ઇન્ટેલિજન્સ કેવી રીતે ભારતના ઉદ્યોગોને બદલી રહી છે.',
    excerptEn: 'How artificial intelligence is transforming industries in India.',
    time: '5 hours ago',
  },
  {
    image: 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?w=600',
    category: 'બિઝનેસ',
    categoryEn: 'Business',
    headline: 'સ્ટાર્ટઅપ ઇકોસિસ્ટમ: ગુજરાતના નવા ઉદ્યોગસાહસિકો',
    headlineEn: 'Startup Ecosystem: New Entrepreneurs of Gujarat',
    excerpt: 'યુવા ઉદ્યોગસાહસિકો કેવી રીતે ગુજરાતને સ્ટાર્ટઅપ હબ બનાવી રહ્યા છે.',
    excerptEn: 'How young entrepreneurs are making Gujarat a startup hub.',
    time: '8 hours ago',
  },
];

export function EditorsPick() {
  const { t, language } = useLanguage();

  return (
    <section className="py-8">
      <div className="flex items-center gap-2 mb-6">
        <Star className="w-5 h-5 text-accent fill-accent" />
        <h2 className="section-title">{t('editors_pick')}</h2>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {editorsPicks.map((article, index) => (
          <NewsCard
            key={index}
            image={article.image}
            category={language === 'en' ? article.categoryEn : article.category}
            headline={language === 'en' ? article.headlineEn : article.headline}
            excerpt={language === 'en' ? article.excerptEn : article.excerpt}
            time={article.time}
          />
        ))}
      </div>
    </section>
  );
}
