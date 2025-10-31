# APP musikeo

## Initialisation du projet en local (Docker)

### 1️⃣ Cloner le projet

```bash
git clone git@github.com:bonnetarnaud/app-musikeo.git
cd app-musikeo
```

### 2️⃣ Préparer Docker

Lancer les services Docker pour Symfony, MySQL, Nginx et PhpMyAdmin :

```bash
docker compose up -d
```

- Symfony sera accessible sur **http://localhost:8080**
- PhpMyAdmin sera accessible sur **http://localhost:8081**

### 3️⃣ Installer les dépendances PHP (Composer)

```bash
docker compose exec app composer install
```

⚠️ **Toutes les commandes Symfony suivantes se lancent depuis le container app.**  
Exemple : `docker compose exec app php bin/console ...`

### 4️⃣ Configurer la base de données

Créer un fichier `.env.local` :

```dotenv
DATABASE_URL="mysql://user:password@database:3306/crm_moto?serverVersion=8.0&charset=utf8mb4"
```

- `database` correspond au nom du service MySQL dans Docker Compose
- Vérifie que `user` / `password` correspondent à ceux de ton `docker-compose.yml`

### 5️⃣ Vérifier et exécuter les migrations

Vérifie l'état des migrations :

```bash
docker compose exec app php bin/console doctrine:migrations:status
```

Applique les migrations si nécessaire :

```bash
docker compose exec app php bin/console doctrine:migrations:migrate
```

### 6️⃣ Compiler Tailwind CSS

Si tu veux rebuild le CSS Tailwind :

```bash
docker compose exec app php bin/console tailwind:build
```

Tu peux aussi utiliser le mode watch pour dev :

```bash
docker compose exec app php bin/console tailwind:watch
```

### 7️⃣ Charger les fixtures (jeu de données)

⚠️ **Attention :** cette commande vide la base avant de recréer les données.

```bash
docker compose exec app php bin/console doctrine:fixtures:load
```

Crée au minimum 2 utilisateurs : un admin et un concessionnaire.

## Liens utiles

[DoctrineMigrationsBundle](https://symfony.com/bundles/DoctrineMigrationsBundle/current/index.html)  
[AssetMapper](https://symfony.com/doc/current/frontend/asset_mapper.html)  
[TailwindCSS avec AssetMapper](https://symfony.com/bundles/TailwindBundle/current/index.html)  
[DoctrineFixturesBundle](https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html)  
[ZenstruckFoundryBundle](https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html)
[Formulaires Twig](https://symfony.com/doc/6.4/form/form_customization.html#reference-forms-twig-form)
