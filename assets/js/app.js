/**
 * JavaScript global de l'application
 * Point d'entrée principal pour tous les scripts
 */

// Import des modules spécifiques
import './auth.js';
import './home.js';
import './account.js';
import './event.js';

class AppManager {
    constructor() {
        this.currentPage = this.detectCurrentPage();
        this.init();
    }

    /**
     * Détection de la page courante
     */
    detectCurrentPage() {
        const path = window.location.pathname;
        
        if (path.includes('/login') || path.includes('/register')) {
            return 'auth';
        } else if (path.includes('/account')) {
            return 'account';
        } else if (path === '/' || path === '/home') {
            return 'home';
        }
        
        return 'default';
    }

    /**
     * Initialisation de l'application
     */
    init() {
        this.setupGlobalHandlers();
        this.setupNavigation();
        this.setupResponsive();
        this.setupAnimations();
        this.setupNotifications();
        
        console.log(`Application initialisée sur la page: ${this.currentPage}`);
    }

    /**
     * Configuration des gestionnaires globaux
     */
    setupGlobalHandlers() {
        // Gestion des erreurs globales
        window.addEventListener('error', (e) => {
            console.error('Erreur JavaScript:', e.error);
            this.showNotification('Une erreur est survenue', 'error');
        });

        // Gestion des clics sur les liens externes
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href^="http"]');
            if (link) {
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
            }
        });

        // Gestion des formulaires avec confirmation
        document.addEventListener('submit', (e) => {
            const form = e.target;
            const confirmMessage = form.dataset.confirm;
            
            if (confirmMessage && !confirm(confirmMessage)) {
                e.preventDefault();
            }
        });
    }

    /**
     * Configuration de la navigation
     */
    setupNavigation() {
        // Gestion du menu mobile
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarCollapse = document.querySelector('.navbar-collapse');
        
        if (navbarToggler && navbarCollapse) {
            navbarToggler.addEventListener('click', () => {
                navbarCollapse.classList.toggle('show');
            });

            // Fermer le menu quand on clique sur un lien
            const navLinks = navbarCollapse.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    navbarCollapse.classList.remove('show');
                });
            });
        }

        // Gestion du dropdown utilisateur
        const userDropdown = document.querySelector('.dropdown-toggle');
        if (userDropdown) {
            userDropdown.addEventListener('click', (e) => {
                e.preventDefault();
                const dropdownMenu = userDropdown.nextElementSibling;
                dropdownMenu.classList.toggle('show');
            });

            // Fermer le dropdown en cliquant ailleurs
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        menu.classList.remove('show');
                    });
                }
            });
        }
    }

    /**
     * Configuration du responsive
     */
    setupResponsive() {
        const handleResize = () => {
            const isMobile = window.innerWidth <= 768;
            document.body.classList.toggle('mobile', isMobile);
            
            // Fermer le menu mobile sur desktop
            if (!isMobile) {
                const navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse) {
                    navbarCollapse.classList.remove('show');
                }
            }
        };
        
        window.addEventListener('resize', handleResize);
        handleResize(); // Appel initial
    }

    /**
     * Configuration des animations globales
     */
    setupAnimations() {
        // Animation d'entrée pour les éléments avec la classe .fade-in
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });
    }

    /**
     * Configuration des notifications globales
     */
    setupNotifications() {
        // Système de notifications global
        window.showNotification = (message, type = 'info', duration = 5000) => {
            const notification = document.createElement('div');
            notification.className = `global-notification global-notification-${type}`;
            notification.textContent = message;
            
            // Styles pour la notification
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                padding: 15px 20px;
                border-radius: 8px;
                color: white;
                font-weight: 500;
                min-width: 300px;
                max-width: 400px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                transform: translateX(100%);
                transition: transform 0.3s ease;
            `;

            // Couleurs selon le type
            const colors = {
                success: '#28a745',
                error: '#dc3545',
                warning: '#ffc107',
                info: '#17a2b8'
            };
            
            notification.style.background = colors[type] || colors.info;
            
            document.body.appendChild(notification);
            
            // Animation d'entrée
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Auto-suppression
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, duration);
        };
    }

    /**
     * Utilitaires globaux
     */
    static utils = {
        // Formatage de date
        formatDate(date) {
            return new Intl.DateTimeFormat('fr-FR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }).format(new Date(date));
        },

        // Validation d'email
        isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },

        // Debounce function
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        // Throttle function
        throttle(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        }
    };
}

// Initialisation quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    window.app = new AppManager();
});

// Export pour utilisation dans d'autres modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AppManager;
}
