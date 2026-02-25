import { Link } from 'react-router-dom';
import { Facebook, Twitter, Instagram, Youtube, Mail, Phone, MapPin } from 'lucide-react';
import { useLanguage } from '@/contexts/LanguageContext';

export function Footer() {
  const { t, language } = useLanguage();

  const footerLinks = {
    categories: [
      { key: 'gujarat', href: '/gujarat' },
      { key: 'national', href: '/national' },
      { key: 'international', href: '/international' },
      { key: 'business', href: '/business' },
      { key: 'sports', href: '/sports' },
      { key: 'entertainment', href: '/entertainment' },
    ],
    company: [
      { key: 'about', label: 'About Us', labelGu: 'અમારા વિશે', labelHi: 'हमारे बारे में', href: '/about' },
      { key: 'contact', label: 'Contact', labelGu: 'સંપર્ક', labelHi: 'संपर्क', href: '/contact' },
      { key: 'careers', label: 'Careers', labelGu: 'કારકિર્દી', labelHi: 'करियर', href: '/careers' },
      { key: 'advertise', label: 'Advertise', labelGu: 'જાહેરાત', labelHi: 'विज्ञापन', href: '/advertise' },
    ],
    legal: [
      { key: 'privacy', label: 'Privacy Policy', labelGu: 'ગોપનીયતા નીતિ', labelHi: 'गोपनीयता नीति', href: '/privacy' },
      { key: 'terms', label: 'Terms of Use', labelGu: 'ઉપયોગની શરતો', labelHi: 'उपयोग की शर्तें', href: '/terms' },
    ],
  };

  const getLabel = (item: { label: string; labelGu: string; labelHi: string }) => {
    if (language === 'hi') return item.labelHi;
    if (language === 'gu') return item.labelGu;
    return item.label;
  };

  return (
    <footer className="bg-foreground text-background">
      {/* Main Footer */}
      <div className="container mx-auto px-4 py-12">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          {/* Brand */}
          <div>
            <Link to="/">
              <h2 className="font-serif text-3xl font-bold text-accent mb-4">કાનમ એક્સપ્રેસ</h2>
            </Link>
            <p className="text-background/70 text-sm leading-relaxed mb-4">
              {language === 'gu' 
                ? 'નિડર અને નિષ્પક્ષ - ગુજરાતનું સૌથી વિશ્વસનીય સાપ્તાહિક સમાચાર પત્ર.'
                : language === 'hi'
                ? 'निडर और निष्पक्ष - गुजरात का सबसे विश्वसनीय साप्ताहिक समाचार पत्र।'
                : "Fearless and Unbiased - Gujarat's most trusted weekly newspaper."}
            </p>
            <div className="flex gap-3">
              {[
                { Icon: Facebook, href: 'https://facebook.com/kanamexpress' },
                { Icon: Twitter, href: 'https://twitter.com/kanamexpress' },
                { Icon: Instagram, href: 'https://instagram.com/kanam_express' },
                { Icon: Youtube, href: 'https://youtube.com/kanamexpress' },
              ].map(({ Icon, href }, index) => (
                <a
                  key={index}
                  href={href}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="w-10 h-10 flex items-center justify-center bg-background/10 hover:bg-accent hover:text-accent-foreground rounded-full transition-colors"
                >
                  <Icon className="w-5 h-5" />
                </a>
              ))}
            </div>
          </div>

          {/* Categories */}
          <div>
            <h3 className="font-semibold text-lg mb-4 text-accent">
              {language === 'gu' ? 'વિભાગો' : language === 'hi' ? 'श्रेणियाँ' : 'Categories'}
            </h3>
            <ul className="space-y-2">
              {footerLinks.categories.map((cat) => (
                <li key={cat.key}>
                  <Link to={cat.href} className="text-background/70 hover:text-accent text-sm transition-colors">
                    {t(cat.key)}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Company */}
          <div>
            <h3 className="font-semibold text-lg mb-4 text-accent">
              {language === 'gu' ? 'કંપની' : language === 'hi' ? 'कंपनी' : 'Company'}
            </h3>
            <ul className="space-y-2">
              {footerLinks.company.map((item) => (
                <li key={item.key}>
                  <Link to={item.href} className="text-background/70 hover:text-accent text-sm transition-colors">
                    {getLabel(item)}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Contact */}
          <div>
            <h3 className="font-semibold text-lg mb-4 text-accent">
              {language === 'gu' ? 'સંપર્ક' : language === 'hi' ? 'संपर्क' : 'Contact'}
            </h3>
            <ul className="space-y-3">
              <li className="flex items-start gap-3 text-sm text-background/70">
                <MapPin className="w-4 h-4 mt-0.5 flex-shrink-0" />
                <span>H.O. Gokul Lalani Khadki, Jawahar Bazaar, Jambusar, District: Bharuch, Gujarat-391150</span>
              </li>
              <li className="flex items-center gap-3 text-sm text-background/70">
                <Phone className="w-4 h-4 flex-shrink-0" />
                <div className="flex flex-col">
                  <a href="tel:+919824749413" className="hover:text-accent transition-colors">
                    +91 98247 49413
                  </a>
                  <a href="tel:+917623046498" className="hover:text-accent transition-colors">
                    +91 76230 46498
                  </a>
                </div>
              </li>
              <li className="flex items-center gap-3 text-sm text-background/70">
                <Mail className="w-4 h-4 flex-shrink-0" />
                <a href="mailto:kanamexpress@gmail.com" className="hover:text-accent transition-colors">
                  kanamexpress@gmail.com
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      {/* Bottom Bar */}
      <div className="border-t border-background/10">
        <div className="container mx-auto px-4 py-4">
          <div className="flex flex-col sm:flex-row items-center justify-between gap-4">
            <p className="text-sm text-background/50">
              © 2024 Kanam Express. {language === 'gu' ? 'બધા હક્કો આરક્ષિત.' : language === 'hi' ? 'सर्वाधिकार सुरक्षित।' : 'All rights reserved.'}
            </p>
            <div className="flex gap-4">
              {footerLinks.legal.map((item) => (
                <Link key={item.key} to={item.href} className="text-xs text-background/50 hover:text-accent transition-colors">
                  {getLabel(item)}
                </Link>
              ))}
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
}
