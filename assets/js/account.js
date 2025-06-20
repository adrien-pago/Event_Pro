/**
 * JavaScript pour les pages de gestion du compte
 */

class AccountManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupFormHandlers();
        this.setupActionButtons();
        this.setupAnimations();
        this.setupConfirmations();
    }

    /**
     * Configuration des gestionnaires de formulaires
     */
    setupFormHandlers() {
        const forms = document.querySelectorAll('.account-form');
        
        forms.forEach(form => {
            // Validation en temps réel
            const inputs = form.querySelectorAll('input[required]');
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });
                
                input.addEventListener('input', () => {
                    this.clearFieldError(input);
                });
            });

            // Gestion de la soumission
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            });
        });
    }

    /**
     * Validation d'un formulaire
     */
    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required]');
        
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    /**
     * Validation d'un champ
     */
    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        this.clearFieldError(field);

        switch (field.type) {
            case 'email':
                if (!this.isValidEmail(value)) {
                    isValid = false;
                    errorMessage = 'Veuillez entrer un email valide';
                }
                break;
            
            case 'password':
                if (value.length < 8) {
                    isValid = false;
                    errorMessage = 'Le mot de passe doit contenir au moins 8 caractères';
                }
                break;
            
            default:
                if (!value) {
                    isValid = false;
                    errorMessage = 'Ce champ est obligatoire';
                }
        }

        if (!isValid) {
            this.showFieldError(field, errorMessage);
        }

        return isValid;
    }

    /**
     * Afficher une erreur de champ
     */
    showFieldError(field, message) {
        field.classList.add('is-invalid');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'form-error';
        errorDiv.textContent = message;
        
        field.parentNode.appendChild(errorDiv);
    }

    /**
     * Supprimer les erreurs d'un champ
     */
    clearFieldError(field) {
        field.classList.remove('is-invalid');
        const errorDiv = field.parentNode.querySelector('.form-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    /**
     * Validation d'email
     */
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Configuration des boutons d'action
     */
    setupActionButtons() {
        const actionButtons = document.querySelectorAll('.account-action-button');
        
        actionButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const action = button.dataset.action;
                
                if (action) {
                    e.preventDefault();
                    this.handleAction(action, button);
                }
            });
        });
    }

    /**
     * Gestion des actions
     */
    handleAction(action, button) {
        switch (action) {
            case 'edit-profile':
                this.showEditForm('profile');
                break;
            
            case 'change-password':
                this.showEditForm('password');
                break;
            
            case 'delete-account':
                this.confirmDeleteAccount();
                break;
            
            default:
                console.log('Action non reconnue:', action);
        }
    }

    /**
     * Afficher un formulaire d'édition
     */
    showEditForm(type) {
        const formContainer = document.querySelector(`.account-form[data-type="${type}"]`);
        
        if (formContainer) {
            // Masquer tous les formulaires
            document.querySelectorAll('.account-form').forEach(form => {
                form.style.display = 'none';
            });
            
            // Afficher le formulaire demandé
            formContainer.style.display = 'block';
            formContainer.scrollIntoView({ behavior: 'smooth' });
            
            // Focus sur le premier champ
            const firstInput = formContainer.querySelector('input');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 300);
            }
        }
    }

    /**
     * Confirmation de suppression de compte
     */
    confirmDeleteAccount() {
        const confirmed = confirm(
            'Êtes-vous sûr de vouloir supprimer votre compte ?\n\n' +
            'Cette action est irréversible et supprimera définitivement toutes vos données.'
        );
        
        if (confirmed) {
            // Ici on pourrait ajouter une confirmation supplémentaire
            const finalConfirm = confirm(
                'Dernière confirmation :\n\n' +
                'Votre compte et toutes vos données seront définitivement supprimés.\n' +
                'Cette action ne peut pas être annulée.\n\n' +
                'Êtes-vous absolument sûr ?'
            );
            
            if (finalConfirm) {
                this.deleteAccount();
            }
        }
    }

    /**
     * Suppression du compte
     */
    deleteAccount() {
        // Ici on enverrait une requête AJAX pour supprimer le compte
        this.showNotification('Suppression du compte en cours...', 'warning');
        
        // Simulation d'une requête
        setTimeout(() => {
            this.showNotification('Compte supprimé avec succès. Redirection...', 'success');
            setTimeout(() => {
                window.location.href = '/logout';
            }, 2000);
        }, 1500);
    }

    /**
     * Configuration des animations
     */
    setupAnimations() {
        const elements = document.querySelectorAll('.account-header, .account-info-card, .account-form');
        
        elements.forEach((element, index) => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                element.style.transition = 'all 0.6s ease';
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }, index * 150);
        });
    }

    /**
     * Configuration des confirmations
     */
    setupConfirmations() {
        // Confirmation pour les actions importantes
        const importantButtons = document.querySelectorAll('.account-form-button.btn-danger');
        
        importantButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const action = button.textContent.trim();
                
                if (action.includes('Supprimer') || action.includes('Delete')) {
                    const confirmed = confirm(`Êtes-vous sûr de vouloir ${action.toLowerCase()} ?`);
                    if (!confirmed) {
                        e.preventDefault();
                    }
                }
            });
        });
    }

    /**
     * Afficher une notification
     */
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `account-alert account-alert-${type}`;
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
     * Sauvegarder automatiquement les modifications
     */
    setupAutoSave() {
        const inputs = document.querySelectorAll('.account-form input');
        let saveTimeout;
        
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => {
                    this.autoSave();
                }, 2000); // Sauvegarde après 2 secondes d'inactivité
            });
        });
    }

    /**
     * Sauvegarde automatique
     */
    autoSave() {
        // Ici on enverrait les données via AJAX
        this.showNotification('Sauvegarde automatique...', 'info');
    }
}

// Initialisation quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    new AccountManager();
});

// Export pour utilisation dans d'autres modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AccountManager;
} 