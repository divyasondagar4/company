import { AlertTriangle } from 'lucide-react';
import { useLanguage } from '@/contexts/LanguageContext';

const breakingNews = [
  { id: 1, text: 'અમદાવાદમાં નવી મેટ્રો લાઇન માટે મંજૂરી, ૨૦૨૫ સુધીમાં પૂર્ણ થશે', textEn: 'New metro line approved for Ahmedabad, to be completed by 2025', textHi: 'अहमदाबाद में नई मेट्रो लाइन मंजूर, 2025 तक पूरी होगी' },
  { id: 2, text: 'ક્રિકેટ: ભારત vs ઓસ્ટ્રેલિયા ટેસ્ટ મેચમાં રોમાંચક મુકાબલો', textEn: 'Cricket: Thrilling contest in India vs Australia Test match', textHi: 'क्रिकेट: भारत vs ऑस्ट्रेलिया टेस्ट मैच में रोमांचक मुकाबला' },
  { id: 3, text: 'બજેટ ૨૦૨૪: નાણામંત્રીની મહત્વની જાહેરાતો, મધ્યમ વર્ગને રાહત', textEn: 'Budget 2024: Finance Minister announces major relief for middle class', textHi: 'बजट 2024: वित्त मंत्री की अहम घोषणाएं, मध्य वर्ग को राहत' },
  { id: 4, text: 'ગુજરાત: સૌરાષ્ટ્રમાં ભારે વરસાદની આગાહી, તંત્ર સજ્જ', textEn: 'Gujarat: Heavy rain forecast for Saurashtra, administration on alert', textHi: 'गुजरात: सौराष्ट्र में भारी बारिश की चेतावनी, प्रशासन तैयार' },
];

export function BreakingTicker() {
  const { t, language } = useLanguage();

  const getLocalizedText = (item: typeof breakingNews[0]) => {
    if (language === 'en') return item.textEn;
    if (language === 'hi') return item.textHi;
    return item.text;
  };

  return (
    <div className="bg-ticker overflow-hidden">
      <div className="container mx-auto px-4">
        <div className="flex items-center h-10">
          {/* Breaking Badge */}
          <div className="flex-shrink-0 flex items-center gap-2 bg-primary text-primary-foreground px-4 py-2 font-bold text-sm uppercase tracking-wider">
            <AlertTriangle className="w-4 h-4 animate-pulse" />
            <span>{t('breaking_news')}</span>
          </div>

          {/* Ticker Content */}
          <div className="relative flex-1 overflow-hidden ml-4">
            <div className="ticker-scroll flex items-center gap-12 whitespace-nowrap">
              {[...breakingNews, ...breakingNews].map((item, index) => (
                <span
                  key={`${item.id}-${index}`}
                  className="text-sm font-medium text-breaking cursor-pointer hover:underline"
                >
                  {getLocalizedText(item)}
                </span>
              ))}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
