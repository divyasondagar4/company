// Dashboard JavaScript

// Smooth scrolling for internal links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Add animation classes when elements come into view
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe all cards and sections
document.addEventListener('DOMContentLoaded', () => {
    const elements = document.querySelectorAll('.stat-card, .category-card, .quiz-category-card, .summary-card');
    
    elements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(el);
    });
});

// Mobile menu toggle (if needed)
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('mobile-active');
    }
}

// Add active state to current page in sidebar
document.addEventListener('DOMContentLoaded', () => {
    const currentPage = new URLSearchParams(window.location.search).get('page') || 'home';
    const menuItems = document.querySelectorAll('.sidebar-menu li');
    
    menuItems.forEach(item => {
        const link = item.querySelector('a');
        if (link && link.href.includes(`page=${currentPage}`)) {
            item.classList.add('active');
        }
    });
});

// Confirmation before logout
document.addEventListener('DOMContentLoaded', () => {
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            if (!confirm('Are you sure you want to logout?')) {
                e.preventDefault();
            }
        });
    }
});

// Add tooltips for score badges
document.addEventListener('DOMContentLoaded', () => {
    const scoreBadges = document.querySelectorAll('.score-badge');
    
    scoreBadges.forEach(badge => {
        const score = parseFloat(badge.textContent);
        let title = '';
        
        if (score >= 80) {
            title = 'Excellent Performance!';
        } else if (score >= 60) {
            title = 'Good Job!';
        } else if (score >= 50) {
            title = 'Passed';
        } else {
            title = 'Needs Improvement';
        }
        
        badge.setAttribute('title', title);
    });
});

// Auto-hide alerts or notifications (if added in future)
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 90px;
        right: 30px;
        padding: 15px 25px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        z-index: 9999;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add CSS for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(100px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideOut {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100px);
        }
    }
    
    .notification-success {
        border-left: 4px solid #43e97b;
    }
    
    .notification-error {
        border-left: 4px solid #f5576c;
    }
    
    .notification-info {
        border-left: 4px solid #667eea;
    }
`;
document.head.appendChild(style);

// Progress bar animations
document.addEventListener('DOMContentLoaded', () => {
    const progressBars = document.querySelectorAll('.progress-fill');
    
    const progressObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const bar = entry.target;
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
            }
        });
    });
    
    progressBars.forEach(bar => progressObserver.observe(bar));
});

// Table row click to expand (for future enhancement)
document.addEventListener('DOMContentLoaded', () => {
    const tableRows = document.querySelectorAll('.results-table tbody tr');
    
    tableRows.forEach(row => {
        row.addEventListener('click', function() {
            this.style.background = 'rgba(102, 126, 234, 0.08)';
            setTimeout(() => {
                this.style.background = '';
            }, 300);
        });
    });
});

console.log('Dashboard loaded successfully!');
