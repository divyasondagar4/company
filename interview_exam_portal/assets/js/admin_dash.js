const menuToggle = document.getElementById('menuToggle');
        
if (menuToggle) {
    menuToggle.addEventListener('click', function() {
        const sidebar = document.querySelector('.admin-sidebar');
        if (sidebar) {
            sidebar.classList.toggle('active');
        }
    });
}
