# Diagramme de Classes - App Musikeo

## Architecture Multi-tenant avec Gestion d'Inventaire d'Instruments

```mermaid
classDiagram
    class Organization {
        -id: int
        -name: string
        -email: string
        -phone: string
        -address: string
        -subscriptionPlan: string
        -subscriptionStartDate: DateTime
        -subscriptionEndDate: DateTime
        -isActive: boolean
        -createdAt: DateTime
        +getName() string
        +getSubscriptionPlan() string
        +isSubscriptionActive() boolean
    }

    class User {
        <<abstract>>
        -id: int
        -email: string
        -password: string
        -firstname: string
        -lastname: string
        -phone: string
        -address: string
        -createdAt: DateTime
        +getFullName() string
        +getRoles() array
    }

    class Admin {
        +getRoles() array
    }

    class Teacher {
        -phone: string
        -biography: text
        -specialties: string
        +getRoles() array
        +getCourses() Collection
        +addCourse() static
        +removeCourse() static
    }

    class Student {
        -dateOfBirth: DateTime
        -address: string
        -phone: string
        +getRoles() array
        +getAge() int
        +getEnrollments() Collection
        +getPayments() Collection
        +getInstrumentRentals() Collection
        +addEnrollment() static
        +removeEnrollment() static
        +addPayment() static
        +removePayment() static
        +addInstrumentRental() static
        +removeInstrumentRental() static
    }

    class Instrument {
        -id: int
        -name: string
        -type: string
        -description: text
        -serialNumber: string
        -brand: string
        -model: string
        -isRentable: boolean
        -isCurrentlyRented: boolean
        -currentRenter: Student
        -rentalStartDate: DateTime
        -additionalInfo: text
        -condition: string
        +getConditionLabel() string
        +isAvailableForRent() boolean
        +getCurrentRental() InstrumentRental
        +rentTo() InstrumentRental
        +returnFromRent() void
        +getRentalHistory() Collection
        +addRentalHistory() static
        +removeRentalHistory() static
    }

    class InstrumentRental {
        -id: int
        -startDate: DateTime
        -endDate: DateTime
        -monthlyPrice: decimal
        -status: string
        -notes: text
        +isActive() boolean
        +isOverdue() boolean
        +getTotalDuration() int
        +getTotalCost() decimal
    }

    class Course {
        -id: int
        -name: string
        -description: text
        -dayOfWeek: string
        -startTime: time
        -endTime: time
        -maxStudents: int
        +getEnrollments() Collection
        +getLessons() Collection
        +getStudents() Collection
        +getEnrollmentCount() int
        +getLessonCount() int
        +addEnrollment() static
        +removeEnrollment() static
        +addLesson() static
        +removeLesson() static
    }

    class Room {
        -id: int
        -name: string
        -capacity: int
        -location: string
        +isAvailable() boolean
    }

    class Lesson {
        -id: int
        -startDatetime: DateTime
        -endDatetime: DateTime
        -notes: text
        +getDuration() int
        +isToday() boolean
    }

    class Enrollment {
        -id: int
        -enrollmentDate: DateTime
        -status: string
        +isActive() boolean
        +canAttendLesson() boolean
        +getStatusLabel() string
        +getStatusChoices() array
    }

    class Payment {
        -id: int
        -amount: decimal
        -paymentDate: DateTime
        -paymentMethod: string
        -status: string
        -description: string
        +getFormattedAmount() string
        +isRecent() boolean
        +getMethodLabel() string
        +getStatusLabel() string
    }

    %% Relations Multi-tenant
    Organization --> User : owns
    Organization --> Instrument : owns
    Organization --> InstrumentRental : manages
    Organization --> Course : offers
    Organization --> Room : has
    Organization --> Lesson : schedules
    Organization --> Enrollment : processes
    Organization --> Payment : receives

    %% Héritage utilisateurs
    User <|-- Admin
    User <|-- Teacher
    User <|-- Student

    %% Gestion inventaire
    Student --> InstrumentRental : rents
    Instrument --> InstrumentRental : rented_in
    Instrument --> Student : currently_rented_by

    %% Cours et planning
    Teacher --> Course : teaches
    Course --> Lesson : has_sessions
    Room --> Lesson : hosts

    %% Inscriptions et paiements
    Student --> Enrollment : enrolls_in
    Course --> Enrollment : accepts
    Student --> Payment : makes
```

## Architecture Multi-tenant

L'application est conçue comme un **SaaS multi-tenant** où chaque **école de musique** (Organization) a sa propre isolation de données.

### Entités principales :

- **Organization** : École de musique avec abonnement
- **User** (abstract) : Utilisateurs avec héritage
  - **Admin** : Gestionnaire de l'école
  - **Teacher** : Professeurs de musique
  - **Student** : Élèves inscrits
- **Instrument** : Inventaire physique d'instruments
- **InstrumentRental** : Système de location/prêt
- **Course** : Cours proposés par l'école
- **Lesson** : Sessions de cours planifiées
- **Enrollment** : Inscriptions des étudiants
- **Payment** : Gestion des paiements

## Évolutions Récentes

### ✅ v2.2 - Système de Gestion des Élèves (Nov 2025)
- **Interface complète de gestion des élèves** avec CRUD complet
- **StudentController** : Sécurité admin et isolation par organisation
- **StudentType** : Formulaire avec validation complète (email, nom, prénom, date de naissance, téléphone, adresse)
- **Templates responsives** : Index avec grille, détail complet, formulaires de création/édition
- **Fonctionnalités avancées** :
  - Statistiques en temps réel (élèves, inscriptions actives, locations, paiements)
  - Système de recherche multi-champs (nom, prénom, email, téléphone)
  - Filtres par statut (inscriptions actives, locations actives, paiements récents)
  - Validation des contraintes avant suppression
- **Navigation hiérarchique** : Menu "Élèves" avec sous-menu "Préinscriptions"
- **Intégration complète** avec les cours, instruments et paiements

### ✅ v2.1 - Gestion Complète des Cours (Nov 2025)
- **Interface d'administration complète** pour la gestion des cours
- **CRUD complet** : Création, consultation, modification, suppression des cours
- **Système de recherche et filtres** par nom, professeur, description
- **Statistiques avancées** : nombre d'élèves, leçons programmées par cours
- **Validation des contraintes** : impossible de supprimer un cours avec inscriptions/leçons
- **Templates responsives** avec interface moderne TailwindCSS
- **Attribution flexible des professeurs** avec gestion des changements
- **Navigation intégrée** dans le menu administrateur

### ✅ v2.0 - Transformation Inventaire (Nov 2025)
- **Restructuration complète** de l'entité `Instrument`
- Passage d'un **catalogue académique** à un **inventaire physique**
- Ajout du système de **location/prêt** avec `InstrumentRental`
- Support des **numéros de série**, **conditions**, **marques/modèles**

### ✅ v1.5 - Multi-tenant Architecture (Nov 2025)
- Ajout de l'entité `Organization` pour l'isolation des données
- **Architecture SaaS** complète par école de musique
- Plans d'abonnement (free, standard, premium, custom)
- Toutes les entités liées à une organisation

### ✅ v1.0 - Base Symfony (Oct 2025)
- Architecture utilisateur avec héritage (`Admin`, `Teacher`, `Student`)
- Système de cours, salles et planning
- Gestion des inscriptions et paiements
- Interface moderne avec Tailwind CSS

## Prochaines Évolutions Prévues

### 🔄 Dashboard Student
- Interface étudiante pour consulter cours et locations
- Historique des paiements et planning personnel
- Gestion du profil étudiant

### 🔄 Gestion Avancée du Planning
- Interface de planification des leçons
- Calendrier intégré pour visualiser les cours
- Gestion des conflits d'horaires et salles

### 🔄 Interface de Gestion d'Inventaire Avancée
- Dashboard d'inventaire avec statistiques d'utilisation
- Gestion des retours d'instruments en retard
- Maintenance et réparations

### 🔄 Système de Notifications
- Alertes pour retours d'instruments en retard
- Notifications de paiements
- Rappels de cours
- Notifications par email/SMS

### 🔄 Gestion des Préinscriptions
- Système de préinscriptions en ligne
- Validation des demandes d'inscription
- Workflow d'admission des élèves