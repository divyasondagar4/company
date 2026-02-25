import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import { LanguageProvider } from "@/contexts/LanguageContext";
import { ScrollToTop } from "@/components/ScrollToTop";
import Index from "./pages/Index";
import Gujarat from "./pages/Gujarat";
import National from "./pages/National";
import International from "./pages/International";
import Business from "./pages/Business";
import Sports from "./pages/Sports";
import Entertainment from "./pages/Entertainment";
import Technology from "./pages/Technology";
import Videos from "./pages/Videos";
import Reels from "./pages/Reels";
import LatestNews from "./pages/LatestNews";
import Trending from "./pages/Trending";
import CityNews from "./pages/CityNews";
import About from "./pages/About";
import Contact from "./pages/Contact";
import Privacy from "./pages/Privacy";
import Terms from "./pages/Terms";
import Careers from "./pages/Careers";
import Advertise from "./pages/Advertise";
import Subscribe from "./pages/subscribe";


import NotFound from "./pages/NotFound";

const queryClient = new QueryClient();

const App = () => (
  <QueryClientProvider client={queryClient}>
    <LanguageProvider>
      <TooltipProvider>
        <Toaster />
        <Sonner />
        <BrowserRouter>
          <ScrollToTop />
          <Routes>
            <Route path="/" element={<Index />} />
            <Route path="/gujarat" element={<Gujarat />} />
            <Route path="/gujarat/:city" element={<CityNews />} />
            <Route path="/national" element={<National />} />
            <Route path="/national/:city" element={<CityNews />} />
            <Route path="/international" element={<International />} />
            <Route path="/international/:city" element={<CityNews />} />
            <Route path="/business" element={<Business />} />
            <Route path="/sports" element={<Sports />} />
            <Route path="/entertainment" element={<Entertainment />} />
            <Route path="/technology" element={<Technology />} />
            <Route path="/videos" element={<Videos />} />
            <Route path="/reels" element={<Reels />} />
            <Route path="/latest" element={<LatestNews />} />
            <Route path="/trending" element={<Trending />} />
            <Route path="/city/:city" element={<CityNews />} />
            <Route path="/about" element={<About />} />
            <Route path="/contact" element={<Contact />} />
            <Route path="/privacy" element={<Privacy />} />
            <Route path="/terms" element={<Terms />} />
            <Route path="/careers" element={<Careers />} />
            <Route path="/advertise" element={<Advertise />} />
      
            <Route path="/subscribe" element={<Subscribe />} />
            {/* ADD ALL CUSTOM ROUTES ABOVE THE CATCH-ALL "*" ROUTE */}
            <Route path="*" element={<NotFound />} />
          </Routes>
        </BrowserRouter>
      </TooltipProvider>
    </LanguageProvider>
  </QueryClientProvider>
);

export default App;
