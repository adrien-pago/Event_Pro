#  Event Pro - Gestionnaire d'Événements

**Event Pro** est une application web complète et moderne, développée avec **Symfony**, conçue pour simplifier la gestion d'événements professionnels. De la planification initiale à la génération de devis, en passant par la synchronisation avec des services externes comme Google Agenda, Event Pro centralise toutes les opérations pour une productivité maximale.

---

## ✨ Fonctionnalités

-   **Gestion de Compte Utilisateur Complète**
    -   Inscription et connexion sécurisées.
    -   **Connexion sociale avec Google (OAuth 2.0)** pour une authentification simplifiée.
    -   Tableau de bord personnel pour visualiser et gérer les informations du compte.
    -   Possibilité de **supprimer son compte** et toutes les données associées de manière sécurisée.

-   **Gestion d'Événements (CRUD)**
    -   Créez, visualisez, modifiez et supprimez des événements.
    -   Interface de liste avec **pagination** pour une navigation aisée.
    -   Sécurité : chaque utilisateur ne peut accéder qu'à ses propres événements.

-   **Gestion des Prestations par Événement**
    -   Pour chaque événement, ajoutez et gérez des prestations détaillées (ex: traiteur, photographe, DJ).
    -   **Interface 100% AJAX** via des fenêtres modales pour ajouter, modifier et supprimer des prestations sans rechargement de page.
    -   Calcul dynamique des **totaux de prix et de marge** en temps réel.

-   **Intégrations et Services**
    -   **Synchronisation avec Google Agenda** : Les événements créés dans l'application peuvent être automatiquement ajoutés au Google Agenda de l'utilisateur.
    -   **Génération de Devis PDF** : Créez en un clic un devis professionnel et élégant au format PDF pour n'importe quel événement.

-   **Technologie et Design**
    -   Interface utilisateur **moderne et entièrement responsive** (mobile, tablette, ordinateur).
    -   Construit avec les meilleures pratiques de Symfony et Doctrine.

---

## 🛠️ Stack Technique

-   **Backend** : Symfony 7 / PHP 8.2
-   **Frontend** : Twig, Bootstrap 5, Stimulus.js, SASS, Webpack Encore
-   **Base de Données** : Doctrine ORM (compatible MySQL, PostgreSQL, SQLite)
-   **Intégrations** : KnpUOAuth2ClientBundle (Google), Dompdf (PDF)

---

## 🚀 Guide d'Installation

### Prérequis
-   PHP 8.2 ou supérieur
-   Composer 2
-   Symfony CLI
-   Node.js et npm

### Étapes

1.  **Cloner le Repository**
    ```bash
    git clone https://github.com/votre-nom/Wendding_Management.git
    cd Wendding_Management
    ```

2.  **Installer les Dépendances**
    ```bash
    composer install
    npm install
    ```

3.  **Configuration de l'Environnement**
    -   **Base de données** : Configurez la variable `DATABASE_URL` pour pointer vers votre base de données.
    -   **Google OAuth** :
        -   Créez un projet sur la [Google Cloud Platform](https://console.cloud.google.com/).
        -   Activez l'API **Google Calendar API**.
        -   Créez des identifiants pour une "Application web" OAuth 2.0.
        -   Ajoutez `https://127.0.0.1:8000/connect/google/check` comme URI de redirection autorisé.
        -   Renseignez les variables `GOOGLE_CLIENT_ID` et `GOOGLE_CLIENT_SECRET` dans votre fichier `.env.local`.

4.  **Base de Données**
    ```bash
    # Créer la base de données
    php bin/console doctrine:database:create

    # Exécuter les migrations pour créer le schéma
    php bin/console doctrine:migrations:migrate
    ```

5.  **Compiler les Assets Frontend**
    ```bash
    # Pour le développement (recompilation auto)
    npm run watch

    # Pour la production
    npm run build
    ```

6.  **Lancer le Serveur**
    ```bash
    symfony server:start -d
    ```

L'application est maintenant disponible à l'adresse `https://127.0.0.1:8000`.

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