# Gestion d'Événements - Symfony

Application web de gestion d'événements développée avec Symfony 6.4.

## 🚀 Fonctionnalités Actuelles

### ✅ Authentification
- **Flux simplifié** : Redirection automatique vers `/login` si non connecté
- **Inscription** : Création de compte avec validation complète
  - Validation email (unicité)
  - Validation mot de passe (complexité + confirmation)
  - Acceptation des conditions RGPD
  - Messages d'erreur/succès en français
  - Redirection automatique vers `/home` après inscription
- **Connexion** : Authentification sécurisée
  - Formulaire HTML classique compatible Symfony Security
  - Messages d'erreur personnalisés
  - Redirection automatique vers `/home` après connexion
- **Déconnexion** : Sécurisée via le firewall Symfony

### ✅ Interface Utilisateur
- **Design moderne** : Interface responsive avec gradient et animations
- **Navigation adaptative** : Header avec menu utilisateur
- **Structure modulaire** : CSS et JavaScript organisés par page
- **Pages principales** :
  - Connexion (`/login`) : Point d'entrée principal pour les utilisateurs non connectés
  - Inscription (`/register`) : Formulaire de création de compte
  - Accueil (`/home`) : Page d'accueil pour utilisateurs connectés
  - Compte (`/account`) : Page de gestion du profil (en développement)

### ✅ Base de Données
- **SQLite** : Base de données locale pour le développement
- **Entité User** : Gestion des utilisateurs avec Doctrine ORM
- **Migrations** : Système de versioning de la base de données

### ✅ Architecture Modulaire
- **Styles CSS** : Un fichier par page (auth.css, home.css, account.css)
- **JavaScript** : Modules ES6 organisés par fonctionnalité
- **Validation** : Validation côté client et serveur
- **Animations** : Système d'animations fluides et performantes

## 📁 Structure du Projet

```
Wendding_Management/
├── assets/                          # Assets frontend
│   ├── styles/                      # Styles CSS modulaires
│   │   ├── app.css                  # Styles globaux + imports
│   │   ├── auth.css                 # Styles authentification (login/register)
│   │   ├── home.css                 # Styles page d'accueil
│   │   └── account.css              # Styles page compte
│   ├── js/                          # JavaScript modulaire
│   │   ├── app.js                   # JavaScript global + gestionnaire principal
│   │   ├── auth.js                  # JavaScript authentification
│   │   ├── home.js                  # JavaScript page d'accueil
│   │   └── account.js               # JavaScript page compte
│   ├── app.js                       # Point d'entrée principal
│   └── bootstrap.js                 # Configuration initiale
├── src/
│   ├── Controller/                  # Contrôleurs Symfony
│   │   ├── SecurityController.php   # Gestion connexion/déconnexion
│   │   ├── RegistrationController.php # Gestion inscription
│   │   ├── HomeController.php       # Page d'accueil (redirection si non connecté)
│   │   └── AccountController.php    # Gestion du compte
│   ├── Entity/                      # Entités Doctrine
│   │   └── User.php                 # Entité utilisateur
│   ├── Form/                        # Formulaires Symfony
│   │   └── RegistrationFormType.php # Formulaire d'inscription
│   └── Repository/                  # Repositories Doctrine
│       └── UserRepository.php       # Repository utilisateur
├── templates/                       # Templates Twig
│   ├── base.html.twig              # Template de base
│   ├── partials/                   # Partiels réutilisables
│   │   └── _header.html.twig       # Header de navigation
│   ├── security/                   # Pages d'authentification
│   │   └── login.html.twig         # Page de connexion
│   ├── registration/               # Pages d'inscription
│   │   └── register.html.twig      # Page d'inscription
│   ├── home/                       # Pages d'accueil
│   │   └── index.html.twig         # Page d'accueil (utilisateurs connectés)
│   └── account/                    # Pages de compte
│       └── index.html.twig         # Page de gestion du compte
├── config/                         # Configuration Symfony
│   └── packages/
│       ├── security.yaml           # Configuration sécurité
│       └── doctrine.yaml           # Configuration base de données
├── migrations/                     # Migrations Doctrine
│   └── Version20250619135045.php   # Migration table user
└── README.md                       # Documentation du projet
```

## 🎯 Checkpoints

### ✅ Checkpoint 1 : Authentification de Base (TERMINÉ)
- [x] Configuration Symfony 6.4
- [x] Entité User avec Doctrine
- [x] Système d'inscription fonctionnel
- [x] Système de connexion fonctionnel
- [x] Interface utilisateur moderne
- [x] Base de données SQLite opérationnelle

### ✅ Checkpoint 2 : Structure Modulaire (TERMINÉ)
- [x] Organisation des styles CSS par page
- [x] Organisation du JavaScript par page
- [x] Documentation README complète
- [x] Optimisation de la structure des assets
- [x] Système de validation côté client
- [x] Animations et interactions utilisateur
- [x] Architecture modulaire ES6

### ✅ Checkpoint 2.5 : Flux d'Authentification Simplifié (TERMINÉ)
- [x] Redirection automatique vers `/login` si non connecté
- [x] Redirection vers `/home` après connexion/inscription
- [x] Suppression de la page d'accueil publique
- [x] Simplification de la navigation
- [x] Flux utilisateur optimisé

### 📋 Checkpoint 3 : Gestion du Compte (À VENIR)
- [ ] Page de profil utilisateur complète
- [ ] Modification des informations personnelles
- [ ] Changement de mot de passe
- [ ] Suppression de compte
- [ ] Historique des actions

### 📋 Checkpoint 4 : Gestion d'Événements (À VENIR)
- [ ] Entité Event
- [ ] CRUD événements
- [ ] Interface de création/modification
- [ ] Liste des événements
- [ ] Système de catégories

### 📋 Checkpoint 5 : Fonctionnalités Avancées (À VENIR)
- [ ] Système de rôles et permissions
- [ ] Notifications en temps réel
- [ ] Export/Import de données
- [ ] API REST
- [ ] Système de recherche

## 🛠️ Installation et Configuration

### Prérequis
- PHP 8.2+
- Composer
- Node.js et npm

### Installation
```bash
# Cloner le projet
git clone [URL_DU_REPO]

# Installer les dépendances PHP
composer install

# Installer les dépendances Node.js
npm install

# Créer la base de données et appliquer les migrations
php bin/console doctrine:migrations:migrate

# Compiler les assets
npm run build

# Lancer le serveur de développement
php -S localhost:8000 -t public
```

### Configuration
- L'application utilise SQLite par défaut (fichier `var/data.db`)
- Les variables d'environnement sont dans le fichier `.env`
- Le secret de l'application est configuré dans `APP_SECRET`

## 🔄 Flux Utilisateur

### Utilisateur non connecté
1. Accès à l'application → Redirection automatique vers `/login`
2. Choix entre :
   - Se connecter avec un compte existant
   - Créer un nouveau compte via `/register`
3. Après authentification → Redirection vers `/home`

### Utilisateur connecté
1. Accès direct à `/home` (page d'accueil)
2. Navigation vers `/account` pour gérer le profil
3. Déconnexion via le menu utilisateur → Retour à `/login`

## 🎨 Styles et JavaScript

### Organisation Modulaire
Chaque page a ses propres fichiers CSS et JS pour faciliter la maintenance :

#### Styles CSS
- **app.css** : Styles globaux + imports des modules
- **auth.css** : Pages de connexion et inscription
- **home.css** : Page d'accueil
- **account.css** : Pages de gestion du compte

#### JavaScript
- **app.js** : Gestionnaire principal + utilitaires globaux
- **auth.js** : Validation et interactions des formulaires d'authentification
- **home.js** : Animations et interactions de la page d'accueil
- **account.js** : Gestion des formulaires et actions du compte

### Fonctionnalités JavaScript
- **Validation en temps réel** : Validation des formulaires côté client
- **Animations fluides** : Système d'animations CSS et JS
- **Responsive design** : Adaptation automatique aux différentes tailles d'écran
- **Notifications** : Système de notifications global
- **Gestion d'erreurs** : Capture et affichage des erreurs JavaScript

### Compilation
Les assets sont compilés avec Webpack Encore :
```bash
# Développement avec recompilation automatique
npm run watch

# Production
npm run build
```

## 🔒 Sécurité

- **CSRF Protection** : Activée sur tous les formulaires
- **Validation** : Contraintes de validation sur les entités
- **Hachage** : Mots de passe hashés avec l'algorithme recommandé
- **Authentification** : Système Symfony Security configuré
- **Validation côté client** : Validation JavaScript pour une meilleure UX
- **Redirection sécurisée** : Protection des routes sensibles

## 📝 Notes de Développement

- L'application est en mode développement (`APP_ENV=dev`)
- Les logs sont dans `var/log/`
- Le cache est dans `var/cache/`
- Les migrations sont dans `migrations/`
- Structure modulaire pour faciliter la maintenance
- Code commenté et documenté
- Flux d'authentification simplifié et optimisé

## 🚀 Prochaines Étapes

1. **Checkpoint 3** : Finaliser la gestion du compte utilisateur
2. **Checkpoint 4** : Implémenter la gestion d'événements
3. **Checkpoint 5** : Ajouter les fonctionnalités avancées
4. **Tests** : Ajouter des tests unitaires et d'intégration
5. **Performance** : Optimiser les performances et le SEO

---

**Dernière mise à jour** : 20/06/2025  
**Version** : 1.2.0  
**Statut** : En développement - Flux d'authentification simplifié 