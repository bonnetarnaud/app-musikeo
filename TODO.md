# 📋 TODO List - Musikeo

## 🔴 Priorité Haute

### Email & Communication
- [ ] Configurer MAILER_DSN pour l'envoi d'emails en production
  - Options : Gmail SMTP, SendGrid, Mailgun, Amazon SES
- [ ] Tester le workflow complet de réinitialisation de mot de passe
- [ ] Configurer le worker Messenger en production (`messenger:consume async`)

### Professeurs (Teacher Dashboard)
- [ ] Créer le contrôleur TeacherController avec les routes :
  - `app_teacher_course_index` - Liste des cours
  - `app_teacher_planning_index` - Planning/calendrier
  - `app_teacher_student_index` - Liste des élèves
  - `app_teacher_material_index` - Matériel pédagogique
  - `app_teacher_evaluation_index` - Évaluations
  - `app_teacher_room_booking_index` - Réservation de salles
- [ ] Implémenter les templates correspondants
- [ ] Mettre à jour la sidebar professeur avec les vraies routes

### Base de données
- [ ] Vérifier et synchroniser toutes les migrations en production
- [ ] Créer des fixtures complètes pour les tests

## 🟡 Priorité Moyenne

### Fonctionnalités Admin
- [ ] Dashboard admin avec statistiques
- [ ] Gestion des absences élèves
- [ ] Système de notation/évaluation
- [ ] Export des données (PDF, Excel)
- [ ] Gestion des paiements et factures

### Fonctionnalités Élèves
- [ ] Dashboard élève
- [ ] Consultation du planning personnel
- [ ] Accès aux ressources pédagogiques
- [ ] Suivi des progrès

### Interface Publique
- [ ] Page d'accueil améliorée
- [ ] Présentation des cours et instruments
- [ ] Formulaire de contact
- [ ] Page "À propos"

## 🟢 Priorité Basse

### Optimisations
- [ ] Cache Redis pour améliorer les performances
- [ ] Optimisation des requêtes Doctrine (lazy loading)
- [ ] Compression des assets
- [ ] CDN pour les images

### Documentation
- [ ] Documentation API
- [ ] Guide d'installation pour nouveaux développeurs
- [ ] Guide d'utilisation pour les administrateurs
- [ ] Documenter l'architecture du projet

### Tests
- [ ] Tests unitaires des services
- [ ] Tests fonctionnels des contrôleurs
- [ ] Tests d'intégration du workflow complet
- [ ] Tests E2E avec Panther/Cypress

## 🐛 Bugs Connus
- [ ] Tailwind CSS watch ne fonctionne pas (`tailwind:watch` exit code 1)
  - À investiguer : configuration Tailwind v4 ou problème Node.js

## 💡 Idées / Améliorations futures
- [ ] Application mobile (React Native / Flutter)
- [ ] Système de visioconférence pour cours en ligne
- [ ] Intégration calendrier Google/Outlook
- [ ] Notifications push
- [ ] Chat en temps réel entre profs et élèves
- [ ] Système de gamification (badges, points)
- [ ] Partage de partitions/fichiers audio

---

## 📝 Notes

### Configuration actuelle
- **Environnement** : Docker (app + database)
- **Framework** : Symfony 7.3
- **CSS** : Tailwind CSS v4
- **Base de données** : MySQL
- **Email** : Symfony Messenger (async) - MAILER_DSN à configurer
- **Authentification** : Symfony Security avec hiérarchie User (Admin/Teacher/Student)

### Services disponibles
- ✅ EmailService
- ✅ OrganizationService
- ✅ UserService
- ✅ PreRegistrationService

### Commandes utiles
```bash
# Lancer le worker pour les emails
docker compose exec app php bin/console messenger:consume async -vv

# Migrations
docker compose exec app php bin/console doctrine:migrations:migrate

# Clear cache
docker compose exec app php bin/console cache:clear

# Fixtures
docker compose exec app php bin/console doctrine:fixtures:load
```
