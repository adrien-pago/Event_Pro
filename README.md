#  Event Pro - Gestionnaire d'Événements

**Event Pro** est une application web moderne et complète, développée avec le framework **Symfony**, conçue pour simplifier la gestion d'événements professionnels. De la planification à la synchronisation avec des services externes comme Google Agenda, Event Pro centralise toutes les opérations pour une productivité maximale.

---

## ✨ Fonctionnalités Clés

-   **Gestion de Compte Utilisateur :**
    -   Inscription et connexion sécurisées avec validation des formulaires.
    -   **Connexion sociale via Google (OAuth 2.0)** pour une authentification rapide.
    -   Tableau de bord personnel pour la gestion des informations du compte.
    -   Déconnexion et possibilité de supprimer son compte et ses données.

-   **Gestion d'Événements (CRUD) :**
    -   Création, affichage, modification et suppression d'événements.
    -   Interface de liste avec **pagination** pour une navigation fluide.
    -   **Gestion fine de la durée :**
        -   Support des événements sur une **journée complète**.
        -   Définition d'**heures de début et de fin** précises.
    -   Sécurité : chaque utilisateur ne peut accéder et gérer que ses propres événements.

-   **Intégrations et Services :**
    -   **Synchronisation avec Google Agenda :**
        -   Ajout, mise à jour et suppression des événements dans le calendrier Google de l'utilisateur.
        -   Gestion correcte de la durée (journée complète ou heures spécifiques).
        -   Système robuste de **renouvellement de token** pour une connexion persistante.
    -   **Gestion des Prestations (à venir) :**
        -   L'interface est prête pour ajouter des prestations (traiteur, DJ, etc.) à chaque événement.
    -   **Génération de Devis PDF (à venir) :**
        -   La structure est en place pour générer des devis PDF en un clic.

-   **Interface et Expérience Utilisateur :**
    -   Design **moderne et entièrement responsive** (mobile, tablette, ordinateur) basé sur Bootstrap 5.
    -   Architecture JavaScript modulaire avec des fichiers dédiés par fonctionnalité (`event.js`, `prestation.js`).
    -   Interactions dynamiques (masquage/affichage de champs, indicateurs de chargement) pour une meilleure UX.

---

## 🛠️ Stack Technique

-   **Backend :** Symfony 7 / PHP 8.2
-   **Frontend :** Twig, JavaScript (ES6+), Bootstrap 5, Webpack Encore
-   **Base de Données :** Doctrine ORM (utilisant SQLite en développement, compatible MySQL/PostgreSQL)
-   **Intégrations :** KnpUOAuth2ClientBundle (Google)

---

## 🚀 Guide de Déploiement

Ce guide vous aidera à déployer l'application sur un serveur de production (type VPS).

### Prérequis
-   Serveur avec accès SSH (ex: Ubuntu 22.04)
-   PHP 8.2 ou supérieur, avec les extensions `intl`, `pdo_sqlite` (ou `pdo_mysql`), `gd`, etc.
-   Composer 2
-   Node.js et npm
-   Un serveur web (Nginx ou Apache)
-   Un gestionnaire de base de données (MySQL/MariaDB ou PostgreSQL)

### Étapes du Déploiement

1.  **Cloner le Repository sur le Serveur**
    ```bash
    git clone https://github.com/votre-nom/Wendding_Management.git /var/www/event-pro
    cd /var/www/event-pro
    ```

2.  **Configuration de l'Environnement de Production**
    -   Créez un fichier `.env.local` à partir du fichier `env.txt` :
        ```bash
        cp env.txt .env.local
        ```
    -   Éditez le fichier `.env.local` et configurez les variables :
        -   `APP_ENV=prod`
        -   `APP_SECRET=...` (générez une clé forte, par exemple avec `openssl rand -hex 32`)
        -   `DATABASE_URL="mysql://user:password@127.0.0.1:3306/event_pro"` (adaptez à votre configuration)
        -   Renseignez `GOOGLE_CLIENT_ID` et `GOOGLE_CLIENT_SECRET`. **Important :** N'oubliez pas d'ajouter l'URL de votre domaine (`https://votre-domaine.com/connect/google/check`) dans les URI de redirection autorisés sur votre console Google Cloud.

3.  **Installer les Dépendances**
    ```bash
    # Installez les dépendances PHP sans les packages de développement
    composer install --no-dev --optimize-autoloader

    # Installez les dépendances frontend
    npm install
    ```

4.  **Compiler les Assets pour la Production**
    ```bash
    npm run build
    ```
    Cette commande va minifier et versionner les fichiers CSS et JS dans le dossier `public/build`.

5.  **Base de Données**
    ```bash
    # (Si nécessaire) Créez la base de données sur votre serveur MySQL/PostgreSQL
    # Exécutez les migrations pour créer le schéma
    php bin/console doctrine:migrations:migrate --no-interaction
    ```

6.  **Configuration du Serveur Web (Exemple Nginx)**
    -   Créez un fichier de configuration pour votre site (ex: `/etc/nginx/sites-available/event-pro.conf`).
    -   Voici un exemple de configuration de base :
        ```nginx
        server {
            listen 80;
            server_name votre-domaine.com;

            root /var/www/event-pro/public;
            index index.php;

            location / {
                try_files $uri /index.php$is_args$args;
            }

            location ~ \.php$ {
                fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
                fastcgi_split_path_info ^(.+\.php)(/.*)$;
                include fastcgi_params;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            }

            location ~* \.(js|css|png|jpg|jpeg|gif|ico)$ {
                expires 1y;
                log_not_found off;
            }
        }
        ```
    -   Activez le site et redémarrez Nginx. Pensez à configurer un certificat SSL (Let's Encrypt) pour le HTTPS.

7.  **Optimisation du Cache**
    Pour des performances optimales en production, vous pouvez vider et préchauffer le cache :
    ```bash
    php bin/console cache:clear --env=prod
    php bin/console cache:warmup --env=prod
    ```

Votre application est maintenant déployée et prête à être utilisée !

---

## 📁 Structure des Fichiers Importants

```
.
├── assets/         # Fichiers CSS et JavaScript
├── config/         # Fichiers de configuration de Symfony
├── migrations/     # Migrations de la base de données
├── public/         # Point d'entrée de l'application (index.php)
├── src/
│   ├── Controller/ # Contrôleurs (logique des pages)
│   ├── Entity/     # Entités Doctrine (modèles de données)
│   ├── Form/       # Classes de formulaires Symfony
│   ├── Repository/ # Logique de requêtes de base de données
│   └── Service/    # Services applicatifs (ex: PdfService)
├── templates/      # Templates Twig (vues HTML)
│   ├── event/      # Templates pour les événements
│   ├── prestation/ # Templates pour les prestations
│   └── pdf/        # Templates pour la génération de PDF
└── composer.json   # Dépendances PHP
```