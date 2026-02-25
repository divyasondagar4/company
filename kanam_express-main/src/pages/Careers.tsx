import { PageLayout } from '@/components/layout/PageLayout';
import { useLanguage } from '@/contexts/LanguageContext';
import { Briefcase, MapPin, Clock, ChevronRight } from 'lucide-react';
import { Link } from 'react-router-dom';

const Careers = () => {
  const { language } = useLanguage();

  const openings = [
    {
      title: language === 'en' ? 'Senior Reporter' : 'સિનિયર રિપોર્ટર',
      department: language === 'en' ? 'Editorial' : 'સંપાદકીય',
      location: language === 'en' ? 'Ahmedabad' : 'અમદાવાદ',
      type: language === 'en' ? 'Full-time' : 'ફુલ-ટાઇમ',
    },
    {
      title: language === 'en' ? 'Video Editor' : 'વિડિયો એડિટર',
      department: language === 'en' ? 'Digital Media' : 'ડિજિટલ મીડિયા',
      location: language === 'en' ? 'Surat' : 'સુરત',
      type: language === 'en' ? 'Full-time' : 'ફુલ-ટાઇમ',
    },
    {
      title: language === 'en' ? 'Social Media Manager' : 'સોશિયલ મીડિયા મેનેજર',
      department: language === 'en' ? 'Marketing' : 'માર્કેટિંગ',
      location: language === 'en' ? 'Ahmedabad' : 'અમદાવાદ',
      type: language === 'en' ? 'Full-time' : 'ફુલ-ટાઇમ',
    },
    {
      title: language === 'en' ? 'News Anchor' : 'ન્યૂઝ એન્કર',
      department: language === 'en' ? 'Broadcast' : 'બ્રોડકાસ્ટ',
      location: language === 'en' ? 'Ahmedabad' : 'અમદાવાદ',
      type: language === 'en' ? 'Full-time' : 'ફુલ-ટાઇમ',
    },
    {
      title: language === 'en' ? 'Graphic Designer' : 'ગ્રાફિક ડિઝાઇનર',
      department: language === 'en' ? 'Creative' : 'ક્રિએટિવ',
      location: language === 'en' ? 'Remote' : 'રિમોટ',
      type: language === 'en' ? 'Contract' : 'કોન્ટ્રેક્ટ',
    },
  ];

  return (
    <PageLayout showTicker={false}>
      <div className="container mx-auto px-4 py-8">
        {/* Hero Section */}
        <div className="bg-gradient-to-r from-primary/10 via-accent/5 to-primary/10 rounded-2xl p-8 md:p-12 mb-12 text-center">
          <h1 className="headline-display text-foreground mb-4">
            {language === 'en' ? 'Join Our Team' : 'અમારી ટીમમાં જોડાઓ'}
          </h1>
          <p className="text-muted-foreground max-w-2xl mx-auto text-lg">
            {language === 'en'
              ? 'Be part of Gujarat\'s most trusted news organization. We\'re always looking for talented individuals who are passionate about journalism.'
              : 'ગુજરાતની સૌથી વિશ્વસનીય સમાચાર સંસ્થાનો ભાગ બનો. અમે હંમેશા પ્રતિભાશાળી વ્યક્તિઓ શોધીએ છીએ જેઓ પત્રકારત્વ પ્રત્યે ઉત્સાહી છે.'}
          </p>
        </div>

        {/* Current Openings */}
        <div className="mb-12">
          <h2 className="headline-secondary text-foreground mb-6">
            {language === 'en' ? 'Current Openings' : 'વર્તમાન ખાલી જગ્યાઓ'}
          </h2>
          <div className="space-y-4">
            {openings.map((job, index) => (
              <div key={index} className="bg-card rounded-xl p-6 shadow-card hover:shadow-elevated transition-shadow">
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                  <div>
                    <h3 className="text-lg font-semibold text-foreground">{job.title}</h3>
                    <div className="flex flex-wrap gap-3 mt-2 text-sm text-muted-foreground">
                      <span className="flex items-center gap-1">
                        <Briefcase className="w-4 h-4" />
                        {job.department}
                      </span>
                      <span className="flex items-center gap-1">
                        <MapPin className="w-4 h-4" />
                        {job.location}
                      </span>
                      <span className="flex items-center gap-1">
                        <Clock className="w-4 h-4" />
                        {job.type}
                      </span>
                    </div>
                  </div>
                  <button className="flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded-full text-sm font-medium hover:bg-primary/90 transition-colors self-start sm:self-center">
                    {language === 'en' ? 'Apply Now' : 'અરજી કરો'}
                    <ChevronRight className="w-4 h-4" />
                  </button>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Why Join Us */}
        <div className="grid md:grid-cols-3 gap-6 mb-12">
          <div className="bg-card rounded-xl p-6 shadow-card">
            <div className="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-4">
              <span className="text-2xl">🚀</span>
            </div>
            <h3 className="font-semibold text-foreground mb-2">
              {language === 'en' ? 'Growth Opportunities' : 'વિકાસની તકો'}
            </h3>
            <p className="text-sm text-muted-foreground">
              {language === 'en'
                ? 'Continuous learning and career advancement opportunities.'
                : 'સતત શીખવાની અને કારકિર્દી આગળ વધારવાની તકો.'}
            </p>
          </div>
          <div className="bg-card rounded-xl p-6 shadow-card">
            <div className="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-4">
              <span className="text-2xl">💪</span>
            </div>
            <h3 className="font-semibold text-foreground mb-2">
              {language === 'en' ? 'Great Benefits' : 'ઉત્તમ લાભો'}
            </h3>
            <p className="text-sm text-muted-foreground">
              {language === 'en'
                ? 'Competitive salary, health insurance, and other perks.'
                : 'સ્પર્ધાત્મક પગાર, આરોગ્ય વીમો અને અન્ય લાભો.'}
            </p>
          </div>
          <div className="bg-card rounded-xl p-6 shadow-card">
            <div className="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-4">
              <span className="text-2xl">🎯</span>
            </div>
            <h3 className="font-semibold text-foreground mb-2">
              {language === 'en' ? 'Impactful Work' : 'પ્રભાવશાળી કાર્ય'}
            </h3>
            <p className="text-sm text-muted-foreground">
              {language === 'en'
                ? 'Make a difference by informing millions of readers.'
                : 'લાખો વાચકોને માહિતગાર કરીને ફરક પાડો.'}
            </p>
          </div>
        </div>

        {/* Contact CTA */}
        <div className="bg-secondary rounded-2xl p-8 text-center">
          <h2 className="text-xl font-bold text-foreground mb-2">
            {language === 'en' ? "Don't see a role that fits?" : 'તમને અનુકૂળ ભૂમિકા નથી દેખાતી?'}
          </h2>
          <p className="text-muted-foreground mb-4">
            {language === 'en'
              ? 'Send us your resume and we\'ll keep you in mind for future opportunities.'
              : 'અમને તમારો રેઝ્યુમે મોકલો અને ભવિષ્યની તકો માટે અમે તમને ધ્યાનમાં રાખીશું.'}
          </p>
          <Link
            to="/contact"
            className="inline-flex items-center gap-2 px-6 py-3 bg-primary text-primary-foreground rounded-full font-medium hover:bg-primary/90 transition-colors"
          >
            {language === 'en' ? 'Contact HR' : 'HR નો સંપર્ક કરો'}
          </Link>
        </div>
      </div>
    </PageLayout>
  );
};

export default Careers;
