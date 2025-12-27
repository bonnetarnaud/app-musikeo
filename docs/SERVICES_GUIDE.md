# Guide d'utilisation des Services

## 📋 Services créés

### 1. **EmailService** - Gestion des emails asynchrones
Envoie des emails de manière asynchrone via Symfony Messenger.

**Méthodes disponibles :**
```php
sendWelcomeEmail(string $to, string $firstName, string $organizationName): void
sendPreRegistrationConfirmation(string $to, string $studentFirstName, string $organizationName): void
sendPreRegistrationContactedEmail(string $to, string $studentFirstName, string $organizationName): void
sendPreRegistrationEnrolledEmail(string $to, string $studentFirstName, string $organizationName): void
sendPasswordResetEmail(string $to, string $token): void
```

**Exemple d'utilisation :**
```php
public function __construct(
    private EmailService $emailService
) {}

public function someAction(): Response
{
    $this->emailService->sendWelcomeEmail(
        to: 'user@example.com',
        firstName: 'Jean',
        organizationName: 'École de Musique'
    );
}
```

### 2. **OrganizationService** - Gestion des organisations
Gère la création et les opérations sur les organisations.

**Méthodes disponibles :**
```php
createOrganization(string $name, string $email, string $type = 'school', string $subscriptionPlan = 'free'): Organization
canAddStudent(Organization $organization): bool
canAddTeacher(Organization $organization): bool
getUsageStats(Organization $organization): array
```

**Exemple d'utilisation :**
```php
public function __construct(
    private OrganizationService $organizationService
) {}

public function checkLimits(): Response
{
    $organization = $this->getUser()->getOrganization();
    
    if (!$this->organizationService->canAddStudent($organization)) {
        $this->addFlash('error', 'Limite d\'élèves atteinte');
    }
    
    $stats = $this->organizationService->getUsageStats($organization);
    // ['students' => ['current' => 25, 'max' => 30, 'percentage' => 83.33], ...]
}
```

### 3. **UserService** - Gestion des utilisateurs
Gère la création et les opérations sur les utilisateurs.

**Méthodes disponibles :**
```php
createAdmin(string $email, string $firstName, string $lastName, string $password, Organization $organization): Admin
updatePassword(Admin $admin, string $newPassword): void
getFullName(Admin $admin): string
```

**Exemple d'utilisation :**
```php
public function __construct(
    private UserService $userService
) {}

public function createNewAdmin(): Response
{
    $admin = $this->userService->createAdmin(
        email: 'admin@example.com',
        firstName: 'Marie',
        lastName: 'Dupont',
        password: 'securePassword123',
        organization: $organization
    );
}
```

### 4. **PreRegistrationService** - Gestion des pré-inscriptions
Gère les pré-inscriptions et leurs changements de statut.

**Méthodes disponibles :**
```php
updateStatus(PreRegistration $preRegistration, string $newStatus, ?string $notes = null): void
convertToStudent(PreRegistration $preRegistration): void
```

**Exemple d'utilisation :**
```php
public function __construct(
    private PreRegistrationService $preRegistrationService
) {}

public function processPreRegistration(PreRegistration $preReg): Response
{
    $this->preRegistrationService->updateStatus(
        preRegistration: $preReg,
        newStatus: PreRegistration::STATUS_CONTACTED,
        notes: 'Premier contact effectué par téléphone'
    );
    // Un email est automatiquement envoyé !
}
```

## 🚀 Lancer le worker Messenger

Pour que les emails s'envoient de manière asynchrone, lancez le worker :

```bash
# Mode développement (avec logs verbeux)
docker compose exec app php bin/console messenger:consume async -vv

# Mode production (avec limites)
docker compose exec app php bin/console messenger:consume async --time-limit=3600 --memory-limit=128M
```

## 📊 Commandes utiles

```bash
# Voir les messages en attente
docker compose exec app php bin/console messenger:stats

# Réessayer les messages échoués
docker compose exec app php bin/console messenger:failed:retry

# Voir les messages échoués
docker compose exec app php bin/console messenger:failed:show
```

## 💡 Avantages de cette architecture

1. **Code plus propre** : La logique métier est séparée des contrôleurs
2. **Réutilisable** : Les services peuvent être utilisés partout
3. **Testable** : Facile à tester unitairement
4. **Asynchrone** : Les emails ne bloquent pas les requêtes
5. **Maintenable** : Modifications centralisées dans les services

## 🎯 Prochaines étapes

Vous pourriez créer d'autres services pour :
- **PaymentService** : Gestion des paiements
- **LessonService** : Gestion des cours et planning
- **NotificationService** : Notifications en temps réel
- **ReportService** : Génération de rapports
- **ExportService** : Export de données (PDF, Excel)
