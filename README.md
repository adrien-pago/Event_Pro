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