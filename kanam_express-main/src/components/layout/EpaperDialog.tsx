import { X, ChevronLeft, ChevronRight, Newspaper } from "lucide-react";
import { Dialog, DialogContent, DialogTrigger } from "@/components/ui/dialog";
import { useLanguage } from "@/contexts/LanguageContext";
import { useState } from "react";
import { useNavigate } from "react-router-dom";

const epaperPages = [
  "https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800",
  "https://images.unsplash.com/photo-1495020689067-958852a7765e?w=800",
  "https://images.unsplash.com/photo-1586339949916-3e9457bef6d3?w=800",
];

export function EpaperDialog() {
  const { language } = useLanguage();
  const navigate = useNavigate();

  const [currentPage, setCurrentPage] = useState(0);
  const [open, setOpen] = useState(false);
  const [loading, setLoading] = useState(false);

  const handleDownload = async () => {
    if (loading) return;

    setLoading(true);

    try {
      const response = await fetch(
        "http://localhost/vibe_tech_labs/php-backend/check-subscription.php?user_id=1",
        { method: "GET" }
      );

      if (!response.ok) {
        throw new Error("Server not responding");
      }

      const data = await response.json();

      if (data.status === "active" && data.download_url) {
        setOpen(false); // close dialog
        window.location.href = data.download_url;
      } else {
        setOpen(false); // close dialog
        navigate("/subscribe");
      }

    } catch (error) {
      alert("Server connection failed. Check backend.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <button className="flex items-center gap-2 px-4 py-2 border rounded-full hover:bg-gray-100">
          <Newspaper size={16} />
          {language === "en" ? "ePaper" : "ઈ-પેપર"}
        </button>
      </DialogTrigger>

      <DialogContent className="sm:max-w-4xl p-0 [&>button]:hidden">
        {/* Header */}
        <div className="flex justify-between items-center p-4 border-b bg-gray-50">
          <h2 className="font-bold text-lg">Kanam Express ePaper</h2>

          <div className="flex gap-3 items-center">
            <button
              onClick={handleDownload}
              disabled={loading}
              className="px-4 py-2 bg-black text-white rounded disabled:opacity-60"
            >
              {loading ? "Checking..." : "Download PDF"}
            </button>

            <button onClick={() => setOpen(false)}>
              <X />
            </button>
          </div>
        </div>

        {/* Preview Section */}
        <div className="p-6 text-center">
          <img
            src={epaperPages[currentPage]}
            alt="ePaper"
            className="mx-auto rounded shadow max-h-[70vh]"
          />

          <div className="flex justify-center items-center gap-6 mt-6">
            <button
              disabled={currentPage === 0}
              onClick={() => setCurrentPage((p) => p - 1)}
            >
              <ChevronLeft size={28} />
            </button>

            <span>
              Page {currentPage + 1} / {epaperPages.length}
            </span>

            <button
              disabled={currentPage === epaperPages.length - 1}
              onClick={() => setCurrentPage((p) => p + 1)}
            >
              <ChevronRight size={28} />
            </button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
}