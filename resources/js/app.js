import './bootstrap';
import '../css/app.css';

// Simple JavaScript for Blade views
console.log('EAV System loaded successfully');

// Global functions for modals and interactions
window.toggleSidebar = function() {
    const sidebar = document.getElementById('mobile-sidebar');
    if (sidebar) {
        sidebar.classList.toggle('hidden');
    }
};

// Close mobile sidebar when clicking on links
document.addEventListener('DOMContentLoaded', function() {
    const mobileLinks = document.querySelectorAll('#mobile-sidebar a');
    mobileLinks.forEach(link => {
        link.addEventListener('click', function() {
            const sidebar = document.getElementById('mobile-sidebar');
            if (sidebar) {
                sidebar.classList.add('hidden');
            }
        });
    });
});