<?php
// =============================================
// Multi-Language Support — English, Hindi, Gujarati
// =============================================

// Set language from GET parameter or session
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'hi', 'gu'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'gu'; // Default Gujarati

// Language labels for switcher
$langLabels = [
    'en' => 'English',
    'hi' => 'हिंदी',
    'gu' => 'ગુજરાતી'
];

// Translation dictionary
$translations = [
    // ---- Navigation ----
    'astro_panchang' => ['en' => 'Astro Panchang', 'hi' => 'ज्योतिष पंचांग', 'gu' => 'જ્યોતિષ પંચાંગ'],
    'home' => ['en' => 'Home', 'hi' => 'होम', 'gu' => 'હોમ'],
    'panchang' => ['en' => 'Panchang', 'hi' => 'पंचांग', 'gu' => 'પંચાંગ'],
    'muhurat' => ['en' => 'Muhurat', 'hi' => 'मुहूर्त', 'gu' => 'મુહૂર્ત'],
    'vastu' => ['en' => 'Vastu', 'hi' => 'वास्तु', 'gu' => 'વાસ્તુ'],
    'temples' => ['en' => 'Temples', 'hi' => 'मंदिर', 'gu' => 'મંદિરો'],
    'festivals' => ['en' => 'Festivals', 'hi' => 'त्योहार', 'gu' => 'તહેવારો'],
    'gallery' => ['en' => 'Gallery', 'hi' => 'गैलरी', 'gu' => 'ગેલેરી'],
    'contact' => ['en' => 'Contact', 'hi' => 'संपर्क', 'gu' => 'સંપર્ક'],
    'login' => ['en' => 'Login', 'hi' => 'लॉगिन', 'gu' => 'લોગિન'],
    'logout' => ['en' => 'Logout', 'hi' => 'लॉगआउट', 'gu' => 'લોગઆઉટ'],
    'admin_panel' => ['en' => 'Admin Panel', 'hi' => 'एडमिन पैनल', 'gu' => 'એડમિન પેનલ'],
    'register' => ['en' => 'Register', 'hi' => 'रजिस्टर', 'gu' => 'રજિસ્ટર'],
    'subscribe' => ['en' => 'Subscribe', 'hi' => 'सब्सक्राइब', 'gu' => 'સબ્સ્ક્રાઇબ'],

    // ---- Hero ----
    'your_divine_guide' => ['en' => 'Your Divine Guide', 'hi' => 'आपका दिव्य मार्गदर्शक', 'gu' => 'તમારું દિવ્ય માર્ગદર્શન'],
    'astro_panchang' => ['en' => 'Astro Panchang', 'hi' => 'ज्योतिष पंचांग', 'gu' => 'જ્યોતિષ પંચાંગ'],
    'hero_desc' => [
        'en' => 'Discover daily Panchang details, auspicious Muhurat timings, Vastu Shastra wisdom, sacred temple information, and the complete Hindu festival calendar — all in one place.',
        'hi' => 'दैनिक पंचांग विवरण, शुभ मुहूर्त समय, वास्तु शास्त्र ज्ञान, पवित्र मंदिर जानकारी और संपूर्ण हिंदू त्योहार कैलेंडर — सब एक जगह।',
        'gu' => 'દૈનિક પંચાંગ વિગતો, શુભ મુહૂર્ત સમય, વાસ્તુ શાસ્ત્ર જ્ઞાન, પવિત્ર મંદિર માહિતી અને સંપૂર્ણ હિંદુ તહેવાર કેલેન્ડર — બધું એક જ જગ્યાએ.'
    ],
    'view_panchang' => ['en' => 'View Panchang', 'hi' => 'पंचांग देखें', 'gu' => 'પંચાંગ જુઓ'],
    'muhurat_calendar' => ['en' => 'Muhurat Calendar', 'hi' => 'मुहूर्त कैलेंडर', 'gu' => 'મુહૂર્ત કેલેન્ડર'],

    // ---- Panchang ----
    'todays_panchang' => ['en' => "Today's Panchang", 'hi' => 'आज का पंचांग', 'gu' => 'આજનું પંચાંગ'],
    'panchang_details' => ['en' => 'Panchang Details', 'hi' => 'पंचांग विवरण', 'gu' => 'પંચાંગ વિગતો'],
    'date' => ['en' => 'Date', 'hi' => 'तारीख', 'gu' => 'તારીખ'],
    'day' => ['en' => 'Day', 'hi' => 'दिन', 'gu' => 'દિવસ'],
    'sunrise' => ['en' => 'Sunrise', 'hi' => 'सूर्योदय', 'gu' => 'સૂર્યોદય'],
    'sunset' => ['en' => 'Sunset', 'hi' => 'सूर्यास्त', 'gu' => 'સૂર્યાસ્ત'],
    'tithi' => ['en' => 'Tithi', 'hi' => 'तिथि', 'gu' => 'તિથિ'],
    'nakshatra' => ['en' => 'Nakshatra', 'hi' => 'नक्षत्र', 'gu' => 'નક્ષત્ર'],
    'yoga' => ['en' => 'Yoga', 'hi' => 'योग', 'gu' => 'યોગ'],
    'karana' => ['en' => 'Karana', 'hi' => 'करण', 'gu' => 'કરણ'],
    'rahu_kaal' => ['en' => 'Rahu Kaal', 'hi' => 'राहु काल', 'gu' => 'રાહુ કાળ'],
    'gulika_kaal' => ['en' => 'Gulika Kaal', 'hi' => 'गुलिक काल', 'gu' => 'ગુલિકા કાળ'],
    'yama_gandam' => ['en' => 'Yama Gandam', 'hi' => 'यम गंडम', 'gu' => 'યમ ગંડમ'],
    'vikram_samvat' => ['en' => 'Vikram Samvat', 'hi' => 'विक्रम संवत', 'gu' => 'વિક્રમ સંવત'],
    'details' => ['en' => 'Details', 'hi' => 'विवरण', 'gu' => 'વિગતો'],
    'search_by_date' => ['en' => 'Search by Date', 'hi' => 'तारीख से खोजें', 'gu' => 'તારીખ દ્વારા શોધો'],
    'search_by_month' => ['en' => 'Search by Month', 'hi' => 'महीने से खोजें', 'gu' => 'મહિના દ્વારા શોધો'],
    'search' => ['en' => 'Search', 'hi' => 'खोजें', 'gu' => 'શોધો'],
    'previous' => ['en' => 'Previous', 'hi' => 'पिछला', 'gu' => 'પાછલું'],
    'next' => ['en' => 'Next', 'hi' => 'अगला', 'gu' => 'આગળનું'],
    'browse_all' => ['en' => 'Browse All Panchang', 'hi' => 'सभी पंचांग देखें', 'gu' => 'બધા પંચાંગ જુઓ'],
    'view_full_details' => ['en' => 'View Full Details', 'hi' => 'पूरा विवरण देखें', 'gu' => 'સંપૂર્ણ વિગતો જુઓ'],
    'download_pdf' => ['en' => 'Download PDF', 'hi' => 'PDF डाउनलोड', 'gu' => 'PDF ડાઉનલોડ'],
    'no_panchang_today' => ['en' => 'Panchang data for today is not yet available.', 'hi' => 'आज का पंचांग डेटा अभी उपलब्ध नहीं है।', 'gu' => 'આજનો પંચાંગ ડેટા હજુ ઉપલબ્ધ નથી.'],
    'no_data' => ['en' => 'No data found.', 'hi' => 'कोई डेटा नहीं मिला।', 'gu' => 'કોઈ ડેટા મળ્યો નથી.'],
    'page' => ['en' => 'Page', 'hi' => 'पृष्ठ', 'gu' => 'પૃષ્ઠ'],
    'of' => ['en' => 'of', 'hi' => 'का', 'gu' => 'માંથી'],
    'showing' => ['en' => 'Showing', 'hi' => 'दिखा रहा है', 'gu' => 'બતાવી રહ્યું છે'],
    'records' => ['en' => 'records', 'hi' => 'रिकॉर्ड', 'gu' => 'રેકોર્ડ'],
    'pdf' => ['en' => 'PDF', 'hi' => 'PDF', 'gu' => 'PDF'],
    'actions' => ['en' => 'Actions', 'hi' => 'कार्य', 'gu' => 'ક્રિયાઓ'],

    // ---- Muhurat ----
    'upcoming_muhurat' => ['en' => 'Upcoming Muhurat', 'hi' => 'आगामी मुहूर्त', 'gu' => 'આગામી મુહૂર્ત'],
    'auspicious_timings' => ['en' => 'Auspicious timings for important ceremonies and events', 'hi' => 'महत्वपूर्ण समारोहों के लिए शुभ समय', 'gu' => 'મહત્વપૂર્ણ પ્રસંગો માટે શુભ સમય'],
    'all_types' => ['en' => 'All', 'hi' => 'सभी', 'gu' => 'બધા'],
    'marriage' => ['en' => 'Marriage', 'hi' => 'विवाह', 'gu' => 'લગ્ન'],
    'griha_pravesh' => ['en' => 'Griha Pravesh', 'hi' => 'गृह प्रवेश', 'gu' => 'ગૃહ પ્રવેશ'],
    'temple_sthapna' => ['en' => 'Temple Sthapna', 'hi' => 'मंदिर स्थापना', 'gu' => 'મંદિર સ્થાપના'],
    'view_full_calendar' => ['en' => 'View Full Calendar', 'hi' => 'पूरा कैलेंडर देखें', 'gu' => 'સંપૂર્ણ કેલેન્ડર જુઓ'],

    // ---- Temples ----
    'sacred_temples' => ['en' => 'Sacred Temples', 'hi' => 'पवित्र मंदिर', 'gu' => 'પવિત્ર મંદિરો'],
    'temple_info' => ['en' => 'Temple Information', 'hi' => 'मंदिर जानकारी', 'gu' => 'મંદિર માહિતી'],
    'view_all_temples' => ['en' => 'View All Temples', 'hi' => 'सभी मंदिर देखें', 'gu' => 'બધા મંદિરો જુઓ'],

    // ---- Festivals ----
    'upcoming_festivals' => ['en' => 'Upcoming Festivals', 'hi' => 'आगामी त्योहार', 'gu' => 'આગામી તહેવારો'],
    'festival_calendar' => ['en' => 'Festival Calendar', 'hi' => 'त्योहार कैलेंडर', 'gu' => 'તહેવાર કેલેન્ડર'],

    // ---- Gallery ----
    'divine_moments' => ['en' => 'Divine Moments', 'hi' => 'दिव्य क्षण', 'gu' => 'દિવ્ય ક્ષણો'],
    'gallery_desc' => ['en' => 'A collection of sacred and spiritual imagery', 'hi' => 'पवित्र और आध्यात्मिक छवियों का संग्रह', 'gu' => 'પવિત્ર અને આધ્યાત્મિક છબીઓનો સંગ્રહ'],
    'gallery_coming_soon' => ['en' => 'Gallery Coming Soon', 'hi' => 'गैलरी जल्द आ रही है', 'gu' => 'ગેલેરી ટૂંક સમયમાં આવશે'],

    // ---- Contact ----
    'contact_us' => ['en' => 'Contact Us', 'hi' => 'हमसे संपर्क करें', 'gu' => 'અમારો સંપર્ક કરો'],
    'send_message' => ['en' => 'Send Us a Message', 'hi' => 'हमें संदेश भेजें', 'gu' => 'અમને સંદેશ મોકલો'],
    'full_name' => ['en' => 'Full Name', 'hi' => 'पूरा नाम', 'gu' => 'પૂરું નામ'],
    'email' => ['en' => 'Email', 'hi' => 'ईमेल', 'gu' => 'ઈમેલ'],
    'phone' => ['en' => 'Phone', 'hi' => 'फोन', 'gu' => 'ફોન'],
    'message' => ['en' => 'Message', 'hi' => 'संदेश', 'gu' => 'સંદેશ'],
    'send' => ['en' => 'Send Message', 'hi' => 'संदेश भेजें', 'gu' => 'સંદેશ મોકલો'],
    'get_in_touch' => ['en' => 'Get in Touch', 'hi' => 'संपर्क करें', 'gu' => 'સંપર્ક કરો'],
    'address' => ['en' => 'Address', 'hi' => 'पता', 'gu' => 'સરનામું'],
    'working_hours' => ['en' => 'Working Hours', 'hi' => 'कार्य समय', 'gu' => 'કામકાજના કલાકો'],
    'msg_sent_success' => ['en' => 'Thank you! Your message has been sent successfully.', 'hi' => 'धन्यवाद! आपका संदेश सफलतापूर्वक भेजा गया।', 'gu' => 'આભાર! તમારો સંદેશ સફળતાપૂર્વક મોકલાયો છે.'],

    // ---- Vastu ----
    'vastu_shastra' => ['en' => 'Vastu Shastra', 'hi' => 'वास्तु शास्त्र', 'gu' => 'વાસ્તુ શાસ્ત્ર'],
    'vastu_desc' => ['en' => 'Ancient Indian science of architecture and spatial geometry', 'hi' => 'वास्तुकला और स्थानिक ज्यामिति का प्राचीन भारतीय विज्ञान', 'gu' => 'સ્થાપત્ય અને અવકાશી ભૂમિતિનું પ્રાચીન ભારતીય વિજ્ઞાન'],

    // ---- Subscribe ----
    'subscribe_premium' => ['en' => 'Subscribe for Premium Access', 'hi' => 'प्रीमियम एक्सेस के लिए सब्सक्राइब करें', 'gu' => 'પ્રીમિયમ એક્સેસ માટે સબ્સ્ક્રાઇબ કરો'],
    'choose_plan' => ['en' => 'Choose Your Plan', 'hi' => 'अपना प्लान चुनें', 'gu' => 'તમારો પ્લાન પસંદ કરો'],
    'monthly' => ['en' => 'Monthly', 'hi' => 'मासिक', 'gu' => 'માસિક'],
    'yearly' => ['en' => 'Yearly', 'hi' => 'वार्षिक', 'gu' => 'વાર્ષિક'],

    // ---- Auth ----
    'welcome_back' => ['en' => 'Welcome Back', 'hi' => 'वापस स्वागत है', 'gu' => 'પાછા આવ્યા, સ્વાગત છે'],
    'create_account' => ['en' => 'Create Account', 'hi' => 'खाता बनाएं', 'gu' => 'એકાઉન્ટ બનાવો'],
    'password' => ['en' => 'Password', 'hi' => 'पासवर्ड', 'gu' => 'પાસવર્ડ'],
    'confirm_password' => ['en' => 'Confirm Password', 'hi' => 'पासवर्ड की पुष्टि', 'gu' => 'પાસવર્ડ ની પુષ્ટિ'],

    // ---- Footer ----
    'quick_links' => ['en' => 'Quick Links', 'hi' => 'त्वरित लिंक', 'gu' => 'ઝડપી લિંક્સ'],
    'follow_us' => ['en' => 'Follow Us', 'hi' => 'हमें फॉलो करें', 'gu' => 'અમને ફોલો કરો'],
    'all_rights' => ['en' => 'All rights reserved.', 'hi' => 'सर्वाधिकार सुरक्षित।', 'gu' => 'સર્વ હક્ક સુરક્ષિત.'],
    'more' => ['en' => 'More', 'hi' => 'अधिक', 'gu' => 'વધુ'],
    'contact_info' => ['en' => 'Contact Info', 'hi' => 'संपर्क जानकारी', 'gu' => 'સંપર્ક માહિતી'],
    'daily_panchang' => ['en' => 'Daily Panchang', 'hi' => 'दैनिक पंचांग', 'gu' => 'દૈનિક પંચાંગ'],
    'footer_desc' => ['en' => 'Your trusted guide for daily Panchang, auspicious Muhurat timings, Vastu Shastra guidance, and comprehensive Hindu festival calendar.', 'hi' => 'दैनिक पंचांग, शुभ मुहूर्त समय, वास्तु शास्त्र मार्गदर्शन और संपूर्ण हिंदू त्योहार कैलेंडर के लिए आपका विश्वसनीय मार्गदर्शक।', 'gu' => 'દૈનિક પંચાંગ, શુભ મુહૂર્ત સમય, વાસ્તુ શાસ્ત્ર માર્ગદર્શન અને સંપૂર્ણ હિંદુ તહેવાર કેલેન્ડર માટે તમારું વિશ્વાસુ માર્ગદર્શન.'],
    'crafted_with_love' => ['en' => 'Crafted with', 'hi' => 'बनाया गया', 'gu' => 'બનાવેલ'],
    'for_divine' => ['en' => 'for divine guidance.', 'hi' => 'दिव्य मार्गदर्शन के लिए।', 'gu' => 'દિવ્ય માર્ગદર્શન માટે.'],

    // ---- Hero Section ----
    'welcome_user' => ['en' => 'Welcome,', 'hi' => 'स्वागत है,', 'gu' => 'સ્વાગત છે,'],
    'panchang_for' => ['en' => 'Panchang details for', 'hi' => 'पंचांग विवरण', 'gu' => 'પંચાંગ વિગતો'],

    // ---- Subscribe Section ----
    'thank_you_subscribing' => ['en' => 'Thank You For Subscribing!', 'hi' => 'सब्सक्राइब करने के लिए धन्यवाद!', 'gu' => 'સબ્સ્ક્રાઇબ કરવા બદલ આભાર!'],
    'premium_access_desc' => ['en' => 'You now have unlimited premium access to detailed Panchang data, PDF downloads, the full Muhurat calendar, and exclusive spiritual content.', 'hi' => 'अब आपके पास विस्तृत पंचांग डेटा, PDF डाउनलोड, पूर्ण मुहूर्त कैलेंडर और विशेष आध्यात्मिक सामग्री तक असीमित प्रीमियम पहुंच है।', 'gu' => 'હવે તમારી પાસે વિસ્તૃત પંચાંગ ડેટા, PDF ડાઉનલોડ, સંપૂર્ણ મુહૂર્ત કેલેન્ડર અને વિશેષ આધ્યાત્મિક સામગ્રી સુધી અમર્યાદિત પ્રીમિયમ ઍક્સેસ છે.'],
    'subscribe_premium_desc' => ['en' => 'Get unlimited access to detailed Panchang data, PDF downloads, Muhurat calendar, and exclusive spiritual content with our premium subscription.', 'hi' => 'हमारी प्रीमियम सदस्यता के साथ विस्तृत पंचांग डेटा, PDF डाउनलोड, मुहूर्त कैलेंडर और विशेष आध्यात्मिक सामग्री तक असीमित पहुंच प्राप्त करें।', 'gu' => 'અમારી પ્રીમિયમ સબ્સ્ક્રિપ્શન સાથે વિસ્તૃત પંચાંગ ડેટા, PDF ડાઉનલોડ, મુહૂર્ત કેલેન્ડર અને વિશેષ આધ્યાત્મિક સામગ્રી સુધી અમર્યાદિત ઍક્સેસ મેળવો.'],
    'view_full_panchang' => ['en' => 'View Full Panchang', 'hi' => 'पूर्ण पंचांग देखें', 'gu' => 'સંપૂર્ણ પંચાંગ જુઓ'],
    'subscribe_now' => ['en' => 'Subscribe Now', 'hi' => 'अभी सब्सक्राइब करें', 'gu' => 'હમણાં સબ્સ્ક્રાઇબ કરો'],
    'pdf_downloads' => ['en' => 'PDF Downloads', 'hi' => 'PDF डाउनलोड', 'gu' => 'PDF ડાઉનલોડ'],
    'full_panchang' => ['en' => 'Full Panchang', 'hi' => 'पूर्ण पंचांग', 'gu' => 'સંપૂર્ણ પંચાંગ'],
    'muhurat_access' => ['en' => 'Muhurat Access', 'hi' => 'मुहूर्त एक्सेस', 'gu' => 'મુહૂર્ત ઍક્સેસ'],
    'login_view_full' => ['en' => 'Login to View Full Panchang', 'hi' => 'पूर्ण पंचांग देखने के लिए लॉगिन करें', 'gu' => 'સંપૂર્ણ પંચાંગ જોવા લોગિન કરો'],
    'subscribe_view_full' => ['en' => 'Subscribe to View Full Panchang', 'hi' => 'पूर्ण पंचांग देखने के लिए सब्सक्राइब करें', 'gu' => 'સંપૂર્ણ પંચાંગ જોવા સબ્સ્ક્રાઇબ કરો'],
    'unlock_full_details' => ['en' => 'Unlock Full Panchang Details', 'hi' => 'पूर्ण पंचांग विवरण अनलॉक करें', 'gu' => 'સંપૂર્ણ પંચાંગ વિગતો અનલૉક કરો'],
    'login_to_view' => ['en' => 'Login to View', 'hi' => 'देखने के लिए लॉगिन करें', 'gu' => 'જોવા માટે લોગિન કરો'],
    'subscribe_to_view' => ['en' => 'Subscribe to View', 'hi' => 'देखने के लिए सब्सक्राइब करें', 'gu' => 'જોવા માટે સબ્સ્ક્રાઇબ કરો'],
    'premium_content' => ['en' => 'Premium Content Locked', 'hi' => 'प्रीमियम सामग्री लॉक', 'gu' => 'પ્રીમિયમ સામગ્રી લૉક'],
    'premium_content_desc' => ['en' => 'Full metadata including PDF downloads and additional astrological calculations is only visible after subscribing.', 'hi' => 'PDF डाउनलोड और अतिरिक्त ज्योतिषीय गणनाओं सहित पूर्ण मेटाडेटा केवल सब्सक्राइब करने के बाद दिखाई देता है।', 'gu' => 'PDF ડાઉનલોડ અને વધારાની જ્યોતિષીય ગણતરીઓ સહિત સંપૂર્ણ મેટાડેટા ફક્ત સબ્સ્ક્રાઇબ કર્યા પછી જ દેખાય છે.'],

    // ---- Panchang Detail Fields ----
    'ayan' => ['en' => 'Ayan', 'hi' => 'अयन', 'gu' => 'અયન'],
    'gujarati_month' => ['en' => 'Gujarati Month', 'hi' => 'गुजराती महीना', 'gu' => 'ગુજરાતી મહિનો'],
    'sun_longitude' => ['en' => 'Sun Longitude', 'hi' => 'सूर्य रेखांश', 'gu' => 'સૂર્ય રેખાંશ'],
    'moon_longitude' => ['en' => 'Moon Longitude', 'hi' => 'चंद्र रेखांश', 'gu' => 'ચંદ્ર રેખાંશ'],
    'graha_position' => ['en' => 'Graha Position', 'hi' => 'ग्रह स्थिति', 'gu' => 'ગ્રહ સ્થિતિ'],
    'vichudo_panchak' => ['en' => 'Vichudo / Panchak', 'hi' => 'विछुडो / पंचक', 'gu' => 'વિછુડો / પંચક'],
    'location' => ['en' => 'Location', 'hi' => 'स्थान', 'gu' => 'સ્થાન'],
    'start' => ['en' => 'Start', 'hi' => 'शुरू', 'gu' => 'શરૂ'],
    'end' => ['en' => 'End', 'hi' => 'समाप्त', 'gu' => 'સમાપ્ત'],
    'pdf_not_available' => ['en' => 'PDF not available for this date.', 'hi' => 'इस तारीख के लिए PDF उपलब्ध नहीं है।', 'gu' => 'આ તારીખ માટે PDF ઉપલબ્ધ નથી.'],

    // ---- Monthly Calendar ----
    'monthly_calendar' => ['en' => 'Monthly Panchang Calendar', 'hi' => 'मासिक पंचांग कैलेंडर', 'gu' => 'માસિક પંચાંગ કેલેન્ડર'],
    'select_dates_desc' => ['en' => 'Click on dates to view Panchang details — select multiple dates to compare', 'hi' => 'पंचांग विवरण देखने के लिए तारीखों पर क्लिक करें — तुलना करने के लिए कई तारीखें चुनें', 'gu' => 'પંચાંગ વિગતો જોવા તારીખો પર ક્લિક કરો — સરખામણી માટે ઘણી તારીખો પસંદ કરો'],
    'select_date_prompt' => ['en' => 'Select one or more dates above to view and compare detailed Panchang.', 'hi' => 'विस्तृत पंचांग देखने और तुलना करने के लिए ऊपर एक या अधिक तारीखें चुनें।', 'gu' => 'વિસ્તૃત પંચાંગ જોવા અને સરખામણી કરવા ઉપર એક અથવા વધુ તારીખો પસંદ કરો.'],
    'dates_selected' => ['en' => 'Date(s) Selected', 'hi' => 'तारीख(ें) चयनित', 'gu' => 'તારીખ(ો) પસંદ'],

    // ---- Misc ----
    'no_upcoming_muhurat' => ['en' => 'No upcoming muhurat data available.', 'hi' => 'कोई आगामी मुहूर्त डेटा उपलब्ध नहीं।', 'gu' => 'કોઈ આગામી મુહૂર્ત ડેટા ઉપલબ્ધ નથી.'],
    'festival_coming_soon' => ['en' => 'Festival data coming soon.', 'hi' => 'त्योहार डेटा जल्द आ रहा है।', 'gu' => 'તહેવાર ડેટા ટૂંક સમયમાં આવશે.'],
    'divine_moments_desc' => ['en' => 'Moments of divine beauty and spiritual grace', 'hi' => 'दिव्य सौंदर्य और आध्यात्मिक अनुग्रह के क्षण', 'gu' => 'દિવ્ય સૌંદર્ય અને આધ્યાત્મિક કૃપાના ક્ષણો'],
    'view_full_gallery' => ['en' => 'View Full Gallery', 'hi' => 'पूरी गैलरी देखें', 'gu' => 'સંપૂર્ણ ગેલેરી જુઓ'],
    'hello' => ['en' => 'Hello,', 'hi' => 'नमस्ते,', 'gu' => 'નમસ્તે,'],

    // ---- Vastu Extended ----
    'vastu_ancient_science' => ['en' => 'The Ancient Science of Architecture', 'hi' => 'वास्तुकला का प्राचीन विज्ञान', 'gu' => 'વાસ્તુશિલ્પનું પ્રાચીન વિજ્ઞાન'],
    'vastu_intro_text' => [
        'en' => 'Vastu Shastra is an ancient Indian science of architecture and buildings, which helps in creating a harmonious environment by applying certain geometric patterns and directional alignments.',
        'hi' => 'वास्तु शास्त्र वास्तुकला और इमारतों का एक प्राचीन भारतीय विज्ञान है, जो कुछ ज्यामितीय पैटर्न और दिशात्मक संरेखण लागू करके एक सामंजस्यपूर्ण वातावरण बनाने में मदद करता है।',
        'gu' => 'વાસ્તુશાસ્ત્ર એ આર્કિટેક્ચર અને ઇમારતોનું એક પ્રાચીન ભારતીય વિજ્ઞાન છે, જે ચોક્કસ ભૌમિતિક પેટર્ન અને દિશાસૂચક ગોઠવણીઓ લાગુ કરીને સુમેળભર્યું વાતાવરણ બનાવવામાં મદદ કરે છે.'
    ],
    'vastu_directions_title' => ['en' => 'Vastu Directions & Guidelines', 'hi' => 'वास्तु दिशाएं और दिशानिर्देश', 'gu' => 'વાસ્તુ દિશાઓ અને માર્ગદર્શિકા'],
    'north' => ['en' => 'North (उत्तर)', 'hi' => 'उत्तर', 'gu' => 'ઉત્તર'],
    'east' => ['en' => 'East (पूर्व)', 'hi' => 'पूर्व', 'gu' => 'પૂર્વ'],
    'south' => ['en' => 'South (दक्षिण)', 'hi' => 'दक्षिण', 'gu' => 'દક્ષિણ'],
    'west' => ['en' => 'West (पश्चिम)', 'hi' => 'पश्चिम', 'gu' => 'પશ્ચિમ'],
    'north_desc' => ['en' => 'Governs wealth and career. Keep this area open and clutter-free.', 'hi' => 'धन और करियर को नियंत्रित करता है। इस क्षेत्र को खुला और अव्यवस्था मुक्त रखें।', 'gu' => 'ધન અને કારકિર્દીનું સંચાલન કરે છે. આ વિસ્તારને ખુલ્લો અને અવ્યવસ્થિત રાખો.'],
    'east_desc' => ['en' => 'Direction of the Sun. Ideal for main entrance or prayer room.', 'hi' => 'सूर्य की दिशा। मुख्य प्रवेश द्वार या प्रार्थना कक्ष के लिए आदर्श।', 'gu' => 'સૂર્યની દિશા. મુખ્ય પ્રવેશદ્વાર અથવા પ્રાર્થના ખંડ માટે આદર્શ.'],
    'south_desc' => ['en' => 'Associated with fame and recognition. Master bedroom is ideal here.', 'hi' => 'प्रसिद्धि और पहचान से जुड़ा है। मास्टर बेडरूम यहाँ आदर्श है।', 'gu' => 'પ્રસિદ્ધિ અને માન્યતા સાથે સંકળાયેલ. માસ્ટર બેડરૂમ અહીં આદર્શ છે.'],
    'west_desc' => ['en' => 'Governs gains and social connections. Suitable for dining room.', 'hi' => 'लाभ और सामाजिक संबंधों को नियंत्रित करता है। भोजन कक्ष के लिए उपयुक्त।', 'gu' => 'લાભ અને સામાજિક કનેક્શનનું સંચાલન કરે છે. ડાઇનિંગ રૂમ માટે યોગ્ય.'],
    'vastu_tips' => ['en' => 'Essential Vastu Tips', 'hi' => 'आवश्यक वास्तु टिप्स', 'gu' => 'મહત્વપૂર્ણ વાસ્તુ ટિપ્સ'],
    'main_entrance' => ['en' => 'Main Entrance', 'hi' => 'मुख्य प्रवेश द्वार', 'gu' => 'મુખ્ય પ્રવેશદ્વાર'],
    'bedroom' => ['en' => 'Bedroom', 'hi' => 'बेडરૂમ', 'gu' => 'બેડરૂમ'],
    'kitchen' => ['en' => 'Kitchen', 'hi' => 'रसोई', 'gu' => 'રસોડું'],
    'pooja_room' => ['en' => 'Pooja Room', 'hi' => 'पूजा कक्ष', 'gu' => 'પૂજા ખંડ'],

    // ---- Gating Labels ----
    'unlock_more' => ['en' => 'Unlock More', 'hi' => 'अधिक अनलॉक करें', 'gu' => 'વધુ અનલૉક કરો'],
    'unlock_all_muhurats' => ['en' => 'Unlock All Upcoming Muhurats', 'hi' => 'सभी आगामी मुहूर्त अनलॉक करें', 'gu' => 'બધા આગામી મુહૂર્ત અનલૉક કરો'],
    'unlock_all_festivals' => ['en' => 'Unlock All Festivals', 'hi' => 'सभी त्योहार अनलॉक करें', 'gu' => 'બધા તહેવારો અનલૉક કરો'],
    'login_to_unlock' => ['en' => 'Login to Unlock', 'hi' => 'अनलॉक करने के लिए लॉगिन करें', 'gu' => 'અનલૉક કરવા માટે લોગિન કરો'],
    'subscribe_to_unlock' => ['en' => 'Subscribe to Unlock', 'hi' => 'अनलॉक करने के लिए सब्सक्राइब करें', 'gu' => 'અનલૉક કરવા માટે સબ્સ્ક્રાઇબ કરો'],

    // ---- Time & Dates ----
    'monday' => ['en' => 'Monday', 'hi' => 'सोमवार', 'gu' => 'સોમવાર'],
    'tuesday' => ['en' => 'Tuesday', 'hi' => 'मंगलवार', 'gu' => 'મંગળવાર'],
    'wednesday' => ['en' => 'Wednesday', 'hi' => 'बुधवार', 'gu' => 'બુધવાર'],
    'thursday' => ['en' => 'Thursday', 'hi' => 'गुरुवार', 'gu' => 'ગુરુવાર'],
    'friday' => ['en' => 'Friday', 'hi' => 'शुक्रवार', 'gu' => 'શુક્રવાર'],
    'saturday' => ['en' => 'Saturday', 'hi' => 'शनिवार', 'gu' => 'શનિવાર'],
    'sunday' => ['en' => 'Sunday', 'hi' => 'रविवार', 'gu' => 'રવિવાર'],

    'january' => ['en' => 'January', 'hi' => 'जनवरी', 'gu' => 'જાન્યુઆરી'],
    'february' => ['en' => 'February', 'hi' => 'फ़रवरी', 'gu' => 'ફેબ્રુઆરી'],
    'march' => ['en' => 'March', 'hi' => 'मार्च', 'gu' => 'માર્ચ'],
    'april' => ['en' => 'April', 'hi' => 'अप्रैल', 'gu' => 'એપ્રિલ'],
    'may' => ['en' => 'May', 'hi' => 'मई', 'gu' => 'મે'],
    'june' => ['en' => 'June', 'hi' => 'जून', 'gu' => 'જૂન'],
    'july' => ['en' => 'July', 'hi' => 'जुलाई', 'gu' => 'જુલાઈ'],
    'august' => ['en' => 'August', 'hi' => 'अगस्त', 'gu' => 'ઓગસ્ટ'],
    'september' => ['en' => 'September', 'hi' => 'सितंबर', 'gu' => 'સપ્ટેમ્બર'],
    'october' => ['en' => 'October', 'hi' => 'अक्टूबर', 'gu' => 'ઓક્ટોબર'],
    'november' => ['en' => 'November', 'hi' => 'नवंबर', 'gu' => 'નવેમ્બર'],
    'december' => ['en' => 'December', 'hi' => 'दिसंबर', 'gu' => 'ડિસેમ્બર'],

    // ---- Common Tithis ----
    'pratipada' => ['en' => 'Pratipada', 'hi' => 'प्रतिपदा', 'gu' => 'પ્રતિપદા'],
    'dwitiya' => ['en' => 'Dwitiya', 'hi' => 'द्वितीया', 'gu' => 'દ્વિતીયા'],
    'tritiya' => ['en' => 'Tritiya', 'hi' => 'तृतीया', 'gu' => 'તૃતીયા'],
    'chaturthi' => ['en' => 'Chaturthi', 'hi' => 'ચતુર્થી', 'gu' => 'ચર્તુથી'],
    'panchami' => ['en' => 'Panchami', 'hi' => 'पंचमी', 'gu' => 'પંચમી'],
    'shasthi' => ['en' => 'Shasthi', 'hi' => 'षष्ठी', 'gu' => 'ષષ્ઠી'],
    'saptami' => ['en' => 'Saptami', 'hi' => 'सप्तमी', 'gu' => 'સપ્તમી'],
    'asthami' => ['en' => 'Asthami', 'hi' => 'अष्टमी', 'gu' => 'અષ્ટમી'],
    'navami' => ['en' => 'Navami', 'hi' => 'नवमी', 'gu' => 'નવમી'],
    'dashami' => ['en' => 'Dashami', 'hi' => 'દશમી', 'gu' => 'દશમી'],
    'ekadashi' => ['en' => 'Ekadashi', 'hi' => 'એકાદશી', 'gu' => 'એકાદશી'],
    'dwadashi' => ['en' => 'Dwadashi', 'hi' => 'દ્વાદશી', 'gu' => 'દ્વાદશી'],
    'trayodashi' => ['en' => 'Trayodashi', 'hi' => 'ત્રયોદશી', 'gu' => 'ત્રયોદશી'],
    'chaturdashi' => ['en' => 'Chaturdashi', 'hi' => 'ચતુર્દશી', 'gu' => 'ચતુર્દશી'],
    'purnima' => ['en' => 'Purnima', 'hi' => 'पूर्णिमा', 'gu' => 'પૂર્ણિમા'],
    'amavasya' => ['en' => 'Amavasya', 'hi' => 'અમાવસ્યા', 'gu' => 'અમાસ'],

    // ---- Common Nakshatras ----
    'ashwini' => ['en' => 'Ashwini', 'hi' => 'अश्विनी', 'gu' => 'અશ્વિની'],
    'bharani' => ['en' => 'Bharani', 'hi' => 'भरणी', 'gu' => 'ભરણી'],
    'kritika' => ['en' => 'Kritika', 'hi' => 'कृतिका', 'gu' => 'કૃતિકા'],
    'rohini' => ['en' => 'Rohini', 'hi' => 'रोहिणी', 'gu' => 'રોહિણી'],
    'mriga' => ['en' => 'Mriga', 'hi' => 'मृगा', 'gu' => 'મૃગસીર્ષ'],

    'no_festival_available' => ['en' => 'No festival data available for', 'hi' => 'के लिए कोई त्योहार डेटा उपलब्ध नहीं है', 'gu' => 'માટે કોઈ તહેવાર ડેટા ઉપલબ્ધ નથી'],
    'yes' => ['en' => 'YES', 'hi' => 'हा', 'gu' => 'હા'],
    'no' => ['en' => 'NO', 'hi' => 'नहीं', 'gu' => 'ના'],
    'vikram_samvat_full' => ['en' => 'Vikram Samvat', 'hi' => 'विक्रम संवत', 'gu' => 'વિક્રમ સંવત'],
    'shaka_samvat' => ['en' => 'Shaka Samvat', 'hi' => 'शक संवत', 'gu' => 'શક સંવત'],
    'end' => ['en' => 'Ends at', 'hi' => 'समाप्त', 'gu' => 'સમાપ્તિ'],
    'start' => ['en' => 'Starts at', 'hi' => 'प्रारंभ', 'gu' => 'પ્રારંભ'],
    'previous' => ['en' => 'Previous', 'hi' => 'पिछला', 'gu' => 'પાછલું'],
    'next' => ['en' => 'Next', 'hi' => 'अगला', 'gu' => 'આગલું'],
    'muhurat_calendar' => ['en' => 'Muhurat Calendar', 'hi' => 'मुहूर्त कैलेंडर', 'gu' => 'મુહૂર્ત કેલેન્ડર'],
    'muhurat_calendar_lock_desc' => ['en' => 'Subscribe to view detailed muhurat information when clicking on calendar dates.', 'hi' => 'कैलेंडर तिथियों पर क्लिक करने पर विस्तृत मुहूर्त जानकारी देखने के लिए सदस्यता लें।', 'gu' => 'કેલેન્ડર તારીખો પર ક્લિક કરવા પર વિગતવાર મુહૂર્ત માહિતી જોવા માટે સબ્સ્ક્રાઇબ કરો.'],
    'subscribe_now' => ['en' => 'Subscribe Now', 'hi' => 'अभी सदस्यता लें', 'gu' => 'હમણાં જ સબ્સ્ક્રાઇબ કરો'],
    'calendar' => ['en' => 'Calendar', 'hi' => 'कैलेंडर', 'gu' => 'કેલેન્ડર'],
    'time' => ['en' => 'Time', 'hi' => 'समय', 'gu' => 'સમય'],
    'details' => ['en' => 'Details', 'hi' => 'विवरण', 'gu' => 'વિગતો'],
    'type' => ['en' => 'Type', 'hi' => 'प्रकार', 'gu' => 'પ્રકાર'],

    // ---- Common Festivals ----
    'maha shivaratri' => ['en' => 'Maha Shivaratri', 'hi' => 'महा शिवरात्रि', 'gu' => 'મહા શિવરાત્રી'],
    'holi' => ['en' => 'Holi', 'hi' => 'होली', 'gu' => 'હોળી'],
    'makar sankranti' => ['en' => 'Makar Sankranti', 'hi' => 'मकर संक्रांति', 'gu' => 'મકર સંક્રાંતિ'],
    'ram navami' => ['en' => 'Ram Navami', 'hi' => 'राम नवमी', 'gu' => 'રામ નવમી'],
    'janmashtami' => ['en' => 'Janmashtami', 'hi' => 'जन्माष्टमी', 'gu' => 'જન્માષ્ટમી'],
    'ganesh chaturthi' => ['en' => 'Ganesh Chaturthi', 'hi' => 'गणेश चतुर्थी', 'gu' => 'ગણેશ ચતુર્થી'],
    'navratri' => ['en' => 'Navratri', 'hi' => 'नवरात्रि', 'gu' => 'નવરાત્રી'],
    'diwali' => ['en' => 'Diwali', 'hi' => 'दिवाली', 'gu' => 'દિવાળી'],
    'vagh baras' => ['en' => 'Vagh Baras', 'hi' => 'वाघ बारस', 'gu' => 'વાઘબારસ'],
    'dhanteras' => ['en' => 'Dhanteras', 'hi' => 'धनतेरस', 'gu' => 'ધનતેરસ'],
    'kali chaudas' => ['en' => 'Kali Chaudas', 'hi' => 'काली चौदस', 'gu' => 'કાળીચૌદશ'],
    'nutan varsh' => ['en' => 'Nutan Varsh', 'hi' => 'नूतन वर्ष', 'gu' => 'નૂતન વર્ષ'],
    'bhai dooj' => ['en' => 'Bhai Dooj', 'hi' => 'भाई दूज', 'gu' => 'ભાઇબીજ'],
    'labh panchami' => ['en' => 'Labh Panchami', 'hi' => 'लाभ पंचमी', 'gu' => 'લાભપંચમ'],

    // ---- Gallery Titles ----
    'vedic astrology chart' => ['en' => 'Vedic Astrology Chart', 'hi' => 'वैदिक ज्योतिष चार्ट', 'gu' => 'વૈદિક જ્યોતિષ ચાર્ટ'],
    'zodiac mandala' => ['en' => 'Zodiac Mandala', 'hi' => 'राशि चक्र मंडला', 'gu' => 'રાશિ ચક્ર મંડલા'],
    'ancient temple astronomy' => ['en' => 'Ancient Temple Astronomy', 'hi' => 'प्राचीन मंदिर खगोल विज्ञान', 'gu' => 'પ્રાચીન મંદિર ખગોળ વિજ્ઞાન'],
    'navagraha cosmic view' => ['en' => 'Navagraha Cosmic View', 'hi' => 'नवग्रह ब्रह्मांडीय दृश्य', 'gu' => 'નવગ્રહ કોસ્મિક વ્યુ'],

    // ---- Vastu Tips ----
    'vastu_tip_entrance_1' => ['en' => 'Main door should face North, East, or North-East', 'hi' => 'मुख्य द्वार उत्तर, पूर्व या उत्तर-पूर्व की ओर होना चाहिए', 'gu' => 'મુખ્ય દરવાજો ઉત્તર, પૂર્વ અથવા ઉત્તર-વૈદિક દિશામાં હોવો જોઈએ'],
    'vastu_tip_entrance_2' => ['en' => 'Avoid placing dustbin or shoe rack near the entrance', 'hi' => 'प्रवेश द्वार के पास कूड़ेदान या जूतों का रैक रखने से बचें', 'gu' => 'પ્રવેશદ્વાર પાસે કચરાપેટી અથવા જૂતાનું રેક રાખવાનું ટાળો'],
    'vastu_tip_entrance_3' => ['en' => 'Door should open inwards in a clockwise direction', 'hi' => 'दरवाजा घड़ी की दिशा में अंदर की ओर खुलना चाहिए', 'gu' => 'દરવાજો અંદરની તરફ જમણી (ક્લોકવાઈઝ) દિશામાં ખુલવો જોઈએ'],
    'vastu_tip_entrance_4' => ['en' => 'Keep the entrance well-lit and clean', 'hi' => 'प्रवेश द्वार को अच्छी तरह से रोशन और साफ रखें', 'gu' => 'પ્રવેશદ્વારને સારી રીતે પ્રકાશિત અને સ્વચ્છ રાખો'],
    'vastu_tip_bedroom_1' => ['en' => 'Master bedroom should be in the South-West', 'hi' => 'मुख्य बेडरूम दक्षिण-पश्चिम में होना चाहिए', 'gu' => 'મુખ્ય બેડરૂમ નૈઋત્ય (દક્ષિણ-પશ્ચિમ) ખૂણામાં હોવો જોઈએ'],
    'vastu_tip_bedroom_2' => ['en' => 'Head should face South or East while sleeping', 'hi' => 'सोते समय सिर दक्षिण या पूर्व की ओर होना चाहिए', 'gu' => 'સૂતી વખતે માથું દક્ષિણ અથવા પૂર્વ દિશા તરફ હોવું જોઈએ'],
    'vastu_tip_bedroom_3' => ['en' => 'Avoid mirrors directly facing the bed', 'hi' => 'बिस्तर के ठीक सामने दर्पण लगाने से बचें', 'gu' => 'બેડની બરાબર સામે અરીસો રાખવાનું ટાળો'],
    'vastu_tip_bedroom_4' => ['en' => 'Use light, soothing colors for walls', 'hi' => 'दीवारों के लिए हल्के, सुखदायक रंगों का प्रयोग करें', 'gu' => 'દીવાલો માટે હળવા અને શાંતિદાયક રંગોનો ઉપયોગ કરો'],
    'vastu_tip_kitchen_1' => ['en' => 'Kitchen should be in the South-East (Agni corner)', 'hi' => 'रसोई दक्षिण-पूर्व (अग्नि कोण) में होनी चाहिए', 'gu' => 'રસોડું આગ્નેય (દક્ષિણ-પૂર્વ) ખૂણામાં હોવું જોઈએ'],
    'vastu_tip_kitchen_2' => ['en' => 'Cook facing East for positive energy', 'hi' => 'सकारात्मक ऊर्जा के लिए पूर्व की ओर मुख करके खाना बनाएं', 'gu' => 'સકારાત્મક ઊર્જા માટે પૂર્વ દિશા તરફ મુખ રાખીને રસોઈ બનાવો'],
    'vastu_tip_kitchen_3' => ['en' => 'Keep kitchen clean and well-ventilated', 'hi' => 'रसोई को साफ और हवादार रखें', 'gu' => 'રસોડાને સ્વચ્છ અને હવા ઉજાસવાળું રાખો'],
    'vastu_tip_kitchen_4' => ['en' => 'Avoid placing kitchen directly opposite bathroom', 'hi' => 'बाथरूम के ठीक सामने रसोई बनाने से बचें', 'gu' => 'બાથરૂમની બરાબર સામે રસોડું રાખવાનું ટાળો'],
    'vastu_tip_pooja_1' => ['en' => 'Ideal location is North-East (Ishan corner)', 'hi' => 'आदर्श स्थान उत्तर-पूर्व (ईशान कोण) है', 'gu' => 'પૂજા રૂમ માટે ઇશાન (ઉત્તર-પૂર્વ) ખૂણો શ્રેષ્ઠ છે'],
    'vastu_tip_pooja_2' => ['en' => 'Face East or North while praying', 'hi' => 'पूजा करते समय पूर्व या उत्तर की ओर मुख करें', 'gu' => 'પૂજા કરતી વખતે પૂર્વ અથવા ઉત્તર દિશા તરફ મુખ રાખો'],
    'vastu_tip_pooja_3' => ['en' => 'Keep idols/photos at least a few inches from the wall', 'hi' => 'मूर्तियों/फोटो को दीवार से कम से कम कुछ इंच दूर रखें', 'gu' => 'મૂર્તિઓ અથવા ફોટાને દીવાલથી થોડા ઇંચ દૂર રાખો'],
    'vastu_tip_pooja_4' => ['en' => 'Avoid placing pooja room under staircase', 'hi' => 'सीढ़ियों के नीचे पूजा घर बनाने से बचें', 'gu' => 'પગથિયાંની નીચે પૂજા રૂમ બનાવવાનું ટાળો'],

    // ---- PDF & Navigation Labels ----
    'divine_guide' => ['en' => 'Your Divine Guide to Auspicious Timings', 'hi' => 'शुभ समय के लिए आपका दिव्य मार्गदर्शक', 'gu' => 'શુભ સમય માટે તમારા દિવ્ય માર્ગદર્શિકા'],
    'daily_overview' => ['en' => 'Daily Overview', 'hi' => 'दैनिक अवलोकन', 'gu' => 'દૈનિક વિહંગાવલોકન'],
    'panchang_elements' => ['en' => 'Panchang Elements', 'hi' => 'पंचांग तत्व', 'gu' => 'પંચાંગ તત્વો'],
    'inauspicious_timings' => ['en' => 'Inauspicious Timings', 'hi' => 'अशुभ समय', 'gu' => 'અશુભ સમય'],
    'graha_position' => ['en' => 'Graha Position (Planets)', 'hi' => 'ग्रह स्थिति', 'gu' => 'ગ્રહોની સ્થિતિ'],
    'sun_longitude' => ['en' => 'Sun Longitude', 'hi' => 'सूर्य देशांतर', 'gu' => 'સૂર્ય રેખાંશ'],
    'moon_longitude' => ['en' => 'Moon Longitude', 'hi' => 'चंद्र देशांतर', 'gu' => 'ચંદ્ર રેખાંશ'],
    'generated_by' => ['en' => 'Generated by Astro Panchang ©', 'hi' => 'एस्ट्रो पंचांग द्वारा जनरेट किया गया ©', 'gu' => 'એસ્ટ્રો પંચાંગ દ્વારા નિર્મિત ©'],
    'pdf_footer_text' => ['en' => 'This document is mathematically calculated based on Vedic planetary positions.', 'hi' => 'यह दस्तावेज़ वैदिक ग्रहों की स्थिति के आधार पर गणितीय रूप से गणना किया गया है।', 'gu' => 'આ દસ્તાવેજ વૈદિક ગ્રહોની સ્થિતિના આધારે ગાણિતિક રીતે ગણતરી કરવામાં આવ્યો છે.'],
    'unlock_full_muhurats' => ['en' => 'Unlock all Muhurats', 'hi' => 'सभी मुहूर्त अनलॉक करें', 'gu' => 'બધા મુહૂર્તો અનલોક કરો'],
    'unlock_all_festivals' => ['en' => 'Unlock all Festivals', 'hi' => 'सभी त्योहार अनलॉक करें', 'gu' => 'બધા તહેવારો અનલોક કરો'],
    'login_to_unlock' => ['en' => 'Login to Unlock', 'hi' => 'अनलॉक करने के लिए लॉगिन करें', 'gu' => 'અનલોક કરવા માટે લોગિન કરો'],
];

// Helper function to get translation
function t($key) {
    global $translations, $lang;
    $key = strtolower(trim((string)$key));
    if (isset($translations[$key][$lang])) {
        return $translations[$key][$lang];
    }
    // Fallback to English, then key itself
    return $translations[$key]['en'] ?? $key;
}

// Helper to format date in selected language
function t_date($date_str) {
    $time = strtotime($date_str);
    $day = strtolower(date('l', $time));
    $d = date('d', $time);
    $month = strtolower(date('F', $time));
    $year = date('Y', $time);
    
    return t($day) . ', ' . $d . ' ' . t($month) . ' ' . $year;
}
?>
