/**
 * JavaScript pour les pages d'authentification
 * (Login et Register)
 */

class AuthManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupFormValidation();
        this.setupPasswordToggle();
        this.setupAutoFocus();
        this.setupAnimations();
    }

    /**
     * Configuration de la validation des formulaires
     */
    setupFormValidation() {
        const forms = document.querySelectorAll('.auth-form');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            });

            // Validation en temps réel
            const inputs = form.querySelectorAll('input[required]');
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });
            });
        });
    }

    /**
     * Validation d'un formulaire complet
     */
    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required]');
        
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        // Validation spéciale pour la confirmation de mot de passe
        const password = form.querySelector('input[name="password"]');
        const confirmPassword = form.querySelector('input[name="confirmPassword"]');
        
        if (password && confirmPassword) {
            if (password.value !== confirmPassword.value) {
                this.showFieldError(confirmPassword, 'Les mots de passe ne correspondent pas');
                isValid = false;
            }
        }

        return isValid;
    }

    /**
     * Validation d'un champ individuel
     */
    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        // Supprimer les erreurs précédentes
        this.clearFieldError(field);

        // Validation selon le type de champ
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
                } else if (!this.isValidPassword(value)) {
                    isValid = false;
                    errorMessage = 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial';
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
     * Validation de mot de passe
     */
    isValidPassword(password) {
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
        return passwordRegex.test(password);
    }

    /**
     * Configuration du toggle de visibilité du mot de passe
     */
    setupPasswordToggle() {
        const passwordFields = document.querySelectorAll('input[type="password"]');
        
        passwordFields.forEach(field => {
            const toggleButton = document.createElement('button');
            toggleButton.type = 'button';
            toggleButton.className = 'password-toggle';
            toggleButton.innerHTML = '<i class="bi bi-eye"></i>';
            toggleButton.style.cssText = `
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                color: #666;
                cursor: pointer;
                padding: 5px;
            `;

            const fieldContainer = field.parentNode;
            fieldContainer.style.position = 'relative';
            fieldContainer.appendChild(toggleButton);

            toggleButton.addEventListener('click', () => {
                if (field.type === 'password') {
                    field.type = 'text';
                    toggleButton.innerHTML = '<i class="bi bi-eye-slash"></i>';
                } else {
                    field.type = 'password';
                    toggleButton.innerHTML = '<i class="bi bi-eye"></i>';
                }
            });
        });
    }

    /**
     * Configuration de l'auto-focus
     */
    setupAutoFocus() {
        const firstInput = document.querySelector('.auth-form input[autofocus]');
        if (firstInput) {
            setTimeout(() => {
                firstInput.focus();
            }, 100);
        }
    }

    /**
     * Configuration des animations
     */
    setupAnimations() {
        const authCard = document.querySelector('.auth-card');
        if (authCard) {
            authCard.classList.add('fade-in');
        }
    }

    /**
     * Afficher un message de succès
     */
    showSuccessMessage(message) {
        this.showMessage(message, 'success');
    }

    /**
     * Afficher un message d'erreur
     */
    showErrorMessage(message) {
        this.showMessage(message, 'error');
    }

    /**
     * Afficher un message
     */
    showMessage(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `auth-alert auth-alert-${type}`;
        alertDiv.textContent = message;

        const authBody = document.querySelector('.auth-body');
        if (authBody) {
            authBody.insertBefore(alertDiv, authBody.firstChild);
            
            // Auto-suppression après 5 secondes
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    }
}

// Initialisation quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    new AuthManager();
});

// Export pour utilisation dans d'autres modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AuthManager;
} 