function showToast(message, type = 'success') {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const icons = {
    success: 'fa-check-circle',
    error: 'fa-exclamation-circle',
    info: 'fa-info-circle'
  };

  const toast = document.createElement('div');
  toast.className = 'toast-sacred toast-' + type;
  toast.innerHTML =
    '<i class="fas ' + (icons[type] || icons.info) + ' toast-icon"></i>' +
    '<span class="toast-msg">' + message + '</span>' +
    '<button class="toast-close" onclick="this.parentElement.style.animation=\'toastSlideOut 0.3s forwards\';setTimeout(()=>this.parentElement.remove(),300)"><i class="fas fa-times"></i></button>' +
    '<div class="toast-progress"></div>';

  container.appendChild(toast);

  // Auto dismiss after 4 seconds
  setTimeout(() => {
    if (toast.parentElement) {
      toast.style.animation = 'toastSlideOut 0.3s forwards';
      setTimeout(() => toast.remove(), 300);
    }
  }, 4000);
}

// Auto-show toasts from PHP
document.addEventListener('DOMContentLoaded', function () {
  const toastData = document.getElementById('toast-data');
  if (toastData) {
    const msg = toastData.getAttribute('data-message');
    const type = toastData.getAttribute('data-type');
    if (msg) showToast(msg, type);
    toastData.remove();
  }
});

document.addEventListener('DOMContentLoaded', function () {

  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        entry.target.classList.add('fade-in');
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  document.querySelectorAll('.animate-on-scroll').forEach(el => {
    observer.observe(el);
  });

  const lightbox = document.getElementById('lightbox');
  if (lightbox) {
    const lightboxImg = lightbox.querySelector('img');

    document.querySelectorAll('.gallery-item img').forEach(img => {
      img.addEventListener('click', function () {
        lightboxImg.src = this.src;
        lightboxImg.alt = this.alt;
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
      });
    });

    lightbox.addEventListener('click', function () {
      lightbox.classList.remove('active');
      document.body.style.overflow = '';
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && lightbox.classList.contains('active')) {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  }

  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  const navbar = document.querySelector('.navbar-sacred');
  if (navbar) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 50) {
        navbar.style.padding = '0.3rem 0';
        navbar.style.boxShadow = '0 4px 20px rgba(44,24,16,0.2)';
      } else {
        navbar.style.padding = '0.6rem 0';
        navbar.style.boxShadow = '';
      }
    });
  }

  // =============================================
  // Form validation feedback
  // =============================================
  document.querySelectorAll('form.form-sacred').forEach(form => {
    form.addEventListener('submit', function (e) {
      const requiredFields = form.querySelectorAll('[required]');
      let valid = true;
      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          field.classList.add('is-invalid');
          valid = false;
        } else {
          field.classList.remove('is-invalid');
        }
      });
      if (!valid) {
        e.preventDefault();
        if (typeof showToast === 'function') {
          showToast('Please fill in all required fields.', 'error');
        }
      }
    });
  });

  // =============================================
  // Auto-dismiss alerts after 5 seconds
  // =============================================
  document.querySelectorAll('.alert-sacred').forEach(alert => {
    setTimeout(() => {
      alert.style.transition = 'opacity 0.5s, transform 0.5s';
      alert.style.opacity = '0';
      alert.style.transform = 'translateY(-10px)';
      setTimeout(() => alert.remove(), 500);
    }, 5000);
  });

});

// =============================================
// =============================================
function initMuhuratCalendar(events, isSubscribed, subscribeUrl) {
  const calendarEl = document.getElementById('muhurat-calendar');
  if (!calendarEl) return;

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,listMonth'
    },
    events: events,
    eventColor: '#C5973B',
    eventTextColor: '#5B1A18',
    dayMaxEvents: 3,
    eventClick: function (info) {
      info.jsEvent.preventDefault();
      if (isSubscribed) {
        // Show details in modal
        const modal = document.getElementById('muhuratModal');
        if (modal) {
          document.getElementById('modalTitle').textContent = info.event.title;
          document.getElementById('modalDate').textContent = info.event.start.toLocaleDateString('en-IN', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
          });
          document.getElementById('modalType').textContent = info.event.extendedProps.type || '';
          document.getElementById('modalTime').textContent =
            (info.event.extendedProps.start_time || '') + ' - ' + (info.event.extendedProps.end_time || '');
          document.getElementById('modalDesc').textContent = info.event.extendedProps.description || '';
          new bootstrap.Modal(modal).show();
        }
      } else {
        window.location.href = subscribeUrl;
      }
    },
    eventDidMount: function (info) {
      info.el.style.cursor = 'pointer';
    }
  });

  calendar.render();
}
