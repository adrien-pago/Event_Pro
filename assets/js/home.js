/**
 * JavaScript pour la page d'accueil
 */

class HomeManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupAnimations();
        this.setupInteractiveElements();
        this.setupWelcomeMessage();
        this.setupActionCards();
    }

    /**
     * Configuration des animations de la page d'accueil
     */
    setupAnimations() {
        // Animation d'entrée pour les éléments
        const elements = document.querySelectorAll('.home-welcome-card, .home-action-card, .home-auth-button');
        
        elements.forEach((element, index) => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                element.style.transition = 'all 0.6s ease';
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }

    /**
     * Configuration des éléments interactifs
     */
    setupInteractiveElements() {
        // Effet hover sur les cartes d'action
        const actionCards = document.querySelectorAll('.home-action-card');
        
        actionCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-5px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Effet hover sur les boutons d'authentification
        const authButtons = document.querySelectorAll('.home-auth-button');
        
        authButtons.forEach(button => {
            button.addEventListener('mouseenter', () => {
                button.style.transform = 'translateY(-3px) scale(1.05)';
            });
            
            button.addEventListener('mouseleave', () => {
                button.style.transform = 'translateY(0) scale(1)';
            });
        });
    }

    /**
     * Configuration du message de bienvenue personnalisé
     */
    setupWelcomeMessage() {
        const userInfo = document.querySelector('.home-user-info');
        const userEmail = document.querySelector('.home-user-email');
        
        if (userInfo && userEmail) {
            // Animation du texte de bienvenue
            const text = userInfo.textContent;
            userInfo.textContent = '';
            
            let index = 0;
            const typeWriter = () => {
                if (index < text.length) {
                    userInfo.textContent += text.charAt(index);
                    index++;
                    setTimeout(typeWriter, 50);
                }
            };
            
            setTimeout(typeWriter, 500);
        }
    }

    /**
     * Configuration des cartes d'action
     */
    setupActionCards() {
        const actionCards = document.querySelectorAll('.home-action-card');
        
        actionCards.forEach(card => {
            const button = card.querySelector('.home-action-button');
            
            if (button) {
                button.addEventListener('click', (e) => {
                    // Animation de clic
                    button.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        button.style.transform = 'scale(1)';
                    }, 150);
                });
            }
        });
    }

    /**
     * Afficher une notification
     */
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `home-alert home-alert-${type}`;
        notification.textContent = message;
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '1000';
        notification.style.minWidth = '300px';
        notification.style.animation = 'slideInRight 0.3s ease';
        
        document.body.appendChild(notification);
        
        // Auto-suppression après 5 secondes
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }

    /**
     * Gestion des actions rapides
     */
    setupQuickActions() {
        // Action pour créer un événement (à implémenter plus tard)
        const createEventButton = document.querySelector('.home-action-button[href="#"]');
        if (createEventButton) {
            createEventButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.showNotification('Fonctionnalité en cours de développement !', 'info');
            });
        }
    }

    /**
     * Configuration du responsive
     */
    setupResponsive() {
        const handleResize = () => {
            const isMobile = window.innerWidth <= 768;
            const actionGrid = document.querySelector('.home-actions-grid');
            
            if (actionGrid) {
                if (isMobile) {
                    actionGrid.style.gridTemplateColumns = '1fr';
                } else {
                    actionGrid.style.gridTemplateColumns = 'repeat(auto-fit, minmax(300px, 1fr))';
                }
            }
        };
        
        window.addEventListener('resize', handleResize);
        handleResize(); // Appel initial
    }

    /**
     * Configuration des raccourcis clavier
     */
    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + N pour nouveau compte (si non connecté)
            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                const registerButton = document.querySelector('.home-auth-button[href*="register"]');
                if (registerButton) {
                    e.preventDefault();
                    registerButton.click();
                }
            }
            
            // Ctrl/Cmd + L pour connexion (si non connecté)
            if ((e.ctrlKey || e.metaKey) && e.key === 'l') {
                const loginButton = document.querySelector('.home-auth-button[href*="login"]');
                if (loginButton) {
                    e.preventDefault();
                    loginButton.click();
                }
            }
        });
    }
}

// Initialisation quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    new HomeManager();
});

// Export pour utilisation dans d'autres modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = HomeManager;
} 