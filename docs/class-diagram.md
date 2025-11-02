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
        -speciality: string
        -biography: text
        -hourlyRate: decimal
        -isActive: boolean
        +getRoles() array
        +getActiveCourses() Collection
    }

    class Student {
        -dateOfBirth: DateTime
        -parentName: string
        -parentEmail: string
        -parentPhone: string
        -level: string
        -notes: text
        +getRoles() array
        +getAge() int
        +getActiveRentals() Collection
    }

    class Instrument {
        -id: int
        -type: string
        -description: text
        -brand: string
        -model: string
        -serialNumber: string
        -condition: string
        -isRentable: boolean
        -isCurrentlyRented: boolean
        -additionalInfo: text
        +getConditionLabel() string
        +isAvailableForRent() boolean
        +getCurrentRental() InstrumentRental
        +rentTo() InstrumentRental
        +returnFromRent() void
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
        +getEnrollments() Collection
        +getLessons() Collection
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
        -dateEnrolled: DateTime
        -status: string
        +isActive() boolean
        +canAttendLesson() boolean
    }

    class Payment {
        -id: int
        -amount: decimal
        -date: DateTime
        -method: string
        -description: string
        +getFormattedAmount() string
        +isRecent() boolean
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

### 🔄 Interface de Gestion d'Inventaire
- CRUD complet pour les instruments
- Gestion des locations/retours
- Statistiques d'utilisation du matériel

### 🔄 Système de Notifications
- Alertes pour retours d'instruments en retard
- Notifications de paiements
- Rappels de cours