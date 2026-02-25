import { Play } from 'lucide-react';
import { Link } from 'react-router-dom';

interface VideoCardProps {
  thumbnail: string;
  title: string;
  duration: string;
  views?: string;
  category?: string;
  href?: string;
}

export function VideoCard({ thumbnail, title, duration, views, category, href = '/videos' }: VideoCardProps) {
  return (
    <Link to={href}>
      <article className="group cursor-pointer">
        <div className="relative aspect-video overflow-hidden rounded-xl">
          <img
            src={thumbnail}
            alt={title}
            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
          />
          <div className="video-overlay opacity-60" />
          
          {/* Play Button */}
          <div className="absolute inset-0 flex items-center justify-center">
            <div className="w-14 h-14 flex items-center justify-center bg-accent rounded-full group-hover:scale-110 transition-transform shadow-lg">
              <Play className="w-6 h-6 text-accent-foreground fill-current ml-1" />
            </div>
          </div>
          
          {/* Duration Badge */}
          <div className="absolute bottom-3 right-3 px-2 py-1 bg-black/80 text-white text-xs font-medium rounded">
            {duration}
          </div>
          
          {/* Category Badge */}
          {category && (
            <div className="absolute top-3 left-3 category-tag">
              {category}
            </div>
          )}
        </div>
        
        <div className="mt-3">
          <h3 className="font-semibold text-foreground group-hover:text-primary transition-colors line-clamp-2">
            {title}
          </h3>
          {views && (
            <span className="text-sm text-muted-foreground mt-1 block">{views} views</span>
          )}
        </div>
      </article>
    </Link>
  );
}
