import { useState } from 'react';
import { PageLayout } from '@/components/layout/PageLayout';
import { useLanguage } from '@/contexts/LanguageContext';
import { Mail, Phone, MapPin, Clock, Send, Facebook, Twitter, Instagram, Youtube, Globe, User } from 'lucide-react';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';

const Contact = () => {
  const { language } = useLanguage();
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: '',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    console.log('Form submitted:', formData);
  };

  return (
    <PageLayout showTicker={false}>
      <div className="container mx-auto px-4 py-8">
        {/* Header */}
        <div className="text-center mb-12">
          <h1 className="text-4xl md:text-5xl font-bold text-foreground mb-4">
            {language === 'en' ? 'Contact Us' : 'સંપર્ક કરો'}
          </h1>
          <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
            {language === 'en' 
              ? 'Get in touch with Kanam Express. We\'d love to hear from you!'
              : 'કાનમ એક્સપ્રેસ સાથે સંપર્ક કરો. અમને તમારા તરફથી સાંભળવું ગમશે!'}
          </p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
          {/* Contact Info */}
          <div className="space-y-8">
            {/* Editor Info */}
            <div className="bg-card rounded-xl p-6 shadow-card">
              <div className="flex items-center gap-4 mb-4">
                <div className="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center">
                  <User className="w-8 h-8 text-primary" />
                </div>
                <div>
                  <h2 className="text-xl font-bold text-foreground">Japan A. Shah</h2>
                  <p className="text-primary font-medium">
                    {language === 'en' ? 'Editor in Chief & Owner' : 'મુખ્ય સંપાદક અને માલિક'}
                  </p>
                </div>
              </div>
              <p className="text-muted-foreground">
                {language === 'en' ? 'Organization:' : 'સંસ્થા:'} કાનમ એક્સપ્રેસ (Kanam Express) – Weekly Newspaper
              </p>
            </div>

            {/* Contact Details */}
            <div className="bg-card rounded-xl p-6 shadow-card space-y-6">
              <h2 className="text-xl font-bold text-foreground mb-4">
                {language === 'en' ? 'Contact Information' : 'સંપર્ક માહિતી'}
              </h2>
              
              <div className="flex items-start gap-4">
                <div className="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                  <Phone className="w-6 h-6 text-primary" />
                </div>
                <div>
                  <h3 className="font-semibold text-foreground mb-1">
                    {language === 'en' ? 'Phone' : 'ફોન'}
                  </h3>
                  <a href="tel:+919824749413" className="text-muted-foreground hover:text-primary transition-colors block">
                    +91 98247 49413
                  </a>
                  <a href="tel:+917623046498" className="text-muted-foreground hover:text-primary transition-colors block">
                    +91 76230 46498
                  </a>
                </div>
              </div>

              <div className="flex items-start gap-4">
                <div className="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                  <Mail className="w-6 h-6 text-primary" />
                </div>
                <div>
                  <h3 className="font-semibold text-foreground mb-1">
                    {language === 'en' ? 'Email' : 'ઈમેઇલ'}
                  </h3>
                  <a href="mailto:kanamexpress@gmail.com" className="text-muted-foreground hover:text-primary transition-colors">
                    kanamexpress@gmail.com
                  </a>
                </div>
              </div>

              <div className="flex items-start gap-4">
                <div className="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                  <Globe className="w-6 h-6 text-primary" />
                </div>
                <div>
                  <h3 className="font-semibold text-foreground mb-1">
                    {language === 'en' ? 'Website' : 'વેબસાઈટ'}
                  </h3>
                  <a href="https://www.kanamexpress.com" target="_blank" rel="noopener noreferrer" className="text-muted-foreground hover:text-primary transition-colors">
                    www.kanamexpress.com
                  </a>
                </div>
              </div>

              <div className="flex items-start gap-4">
                <div className="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                  <MapPin className="w-6 h-6 text-primary" />
                </div>
                <div>
                  <h3 className="font-semibold text-foreground mb-1">
                    {language === 'en' ? 'Office Address' : 'ઓફિસ સરનામું'}
                  </h3>
                  <p className="text-muted-foreground">
                    H.O. Gokul Lalani Khadki,<br />
                    Jawahar Bazaar, Jambusar,<br />
                    District: Bharuch,<br />
                    Gujarat - 391150
                  </p>
                </div>
              </div>
            </div>

            {/* Social Media */}
            <div className="bg-card rounded-xl p-6 shadow-card">
              <h2 className="text-xl font-bold text-foreground mb-4">
                {language === 'en' ? 'Follow Us' : 'અમને ફોલો કરો'}
              </h2>
              <div className="flex flex-wrap gap-3">
                <a 
                  href="https://facebook.com/kanamexpress" 
                  target="_blank" 
                  rel="noopener noreferrer"
                  className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors text-sm"
                >
                  <Facebook className="w-4 h-4" />
                  <span>Facebook</span>
                </a>
                <a 
                  href="https://instagram.com/kanam_express" 
                  target="_blank" 
                  rel="noopener noreferrer"
                  className="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-full hover:opacity-90 transition-opacity text-sm"
                >
                  <Instagram className="w-4 h-4" />
                  <span>@kanam_express</span>
                </a>
                <a 
                  href="https://twitter.com/kanamexpress" 
                  target="_blank" 
                  rel="noopener noreferrer"
                  className="flex items-center gap-2 px-4 py-2 bg-sky-500 text-white rounded-full hover:bg-sky-600 transition-colors text-sm"
                >
                  <Twitter className="w-4 h-4" />
                  <span>@kanamexpress</span>
                </a>
                <a 
                  href="https://youtube.com/kanamexpress" 
                  target="_blank" 
                  rel="noopener noreferrer"
                  className="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-full hover:bg-red-700 transition-colors text-sm"
                >
                  <Youtube className="w-4 h-4" />
                  <span>YouTube</span>
                </a>
              </div>
            </div>

            {/* Working Hours */}
            <div className="bg-card rounded-xl p-6 shadow-card">
              <h3 className="font-semibold text-foreground mb-4 flex items-center gap-2">
                <Clock className="w-5 h-5 text-primary" />
                {language === 'en' ? 'Working Hours' : 'કામકાજના કલાકો'}
              </h3>
              <div className="space-y-2 text-sm">
                <div className="flex justify-between">
                  <span className="text-muted-foreground">{language === 'en' ? 'Mon - Fri' : 'સોમ - શુક્ર'}</span>
                  <span className="text-foreground">9:00 AM - 6:00 PM</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-muted-foreground">{language === 'en' ? 'Saturday' : 'શનિવાર'}</span>
                  <span className="text-foreground">10:00 AM - 4:00 PM</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-muted-foreground">{language === 'en' ? 'Sunday' : 'રવિવાર'}</span>
                  <span className="text-foreground">{language === 'en' ? 'Closed' : 'બંધ'}</span>
                </div>
              </div>
            </div>
          </div>

          {/* Contact Form */}
          <div className="bg-card rounded-xl p-8 shadow-card h-fit">
            <h2 className="text-2xl font-bold text-foreground mb-6">
              {language === 'en' ? 'Send us a Message' : 'અમને સંદેશ મોકલો'}
            </h2>
            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-foreground mb-2">
                    {language === 'en' ? 'Your Name' : 'તમારું નામ'}
                  </label>
                  <Input 
                    value={formData.name}
                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                    placeholder={language === 'en' ? 'Enter your name' : 'તમારું નામ દાખલ કરો'} 
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-foreground mb-2">
                    {language === 'en' ? 'Email' : 'ઈમેઇલ'}
                  </label>
                  <Input 
                    type="email" 
                    value={formData.email}
                    onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                    placeholder={language === 'en' ? 'Enter your email' : 'તમારો ઈમેઇલ દાખલ કરો'} 
                  />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-foreground mb-2">
                  {language === 'en' ? 'Phone' : 'ફોન'}
                </label>
                <Input 
                  type="tel" 
                  value={formData.phone}
                  onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                  placeholder={language === 'en' ? 'Enter your phone number' : 'તમારો ફોન નંબર દાખલ કરો'} 
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-foreground mb-2">
                  {language === 'en' ? 'Subject' : 'વિષય'}
                </label>
                <Input 
                  value={formData.subject}
                  onChange={(e) => setFormData({ ...formData, subject: e.target.value })}
                  placeholder={language === 'en' ? 'Enter subject' : 'વિષય દાખલ કરો'} 
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-foreground mb-2">
                  {language === 'en' ? 'Message' : 'સંદેશ'}
                </label>
                <Textarea 
                  rows={5} 
                  value={formData.message}
                  onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                  placeholder={language === 'en' ? 'Write your message here...' : 'તમારો સંદેશ અહીં લખો...'} 
                />
              </div>
              <Button type="submit" className="w-full">
                <Send className="w-4 h-4 mr-2" />
                {language === 'en' ? 'Send Message' : 'સંદેશ મોકલો'}
              </Button>
            </form>
          </div>
        </div>

        {/* Map */}
        <div className="mt-12 bg-secondary rounded-xl overflow-hidden h-80">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3691.5!2d72.95!3d21.9!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sJambusar%2C%20Gujarat!5e0!3m2!1sen!2sin!4v1234567890"
            width="100%"
            height="100%"
            style={{ border: 0 }}
            allowFullScreen
            loading="lazy"
            referrerPolicy="no-referrer-when-downgrade"
          />
        </div>
      </div>
    </PageLayout>
  );
};

export default Contact;
