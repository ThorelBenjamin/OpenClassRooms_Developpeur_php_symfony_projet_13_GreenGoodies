
# Projet Symfony

## 📌 Description

Ce projet est une application Symfony permettant de gérer des produits et un panier utilisateur.
Il inclut :

* Changer la base de données.
* API REST pour consulter les produits.
* Fixtures pour peupler la base de données avec des données de test.

---

## 🔧 Installation et configuration

### 1️⃣ Cloner le projet

```bash
  git clone <URL_DU_PROJET>
  cd <NOM_DU_PROJET>
```

### 2️⃣ Installer les dépendances

```bash
  composer install
```

### 3️⃣ Configurer la base de données

* Ouvrir le fichier `.env` et définir :

```dotenv
  DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=8.0"
```

* Remplacer `db_user`, `db_password`, `db_name` par vos informations.

---

### 4️⃣ Créer la base de données et les tables

```bash
  php bin/console doctrine:database:create
  php bin/console doctrine:migrations:migrate
```


---

### 5️⃣ Charger les fixtures

```bash
  php bin/console doctrine:fixtures:load
```

* Confirmez pour vider la base si demandé.
* Pour ne pas écraser les données existantes, utilisez :

```bash
  php bin/console doctrine:fixtures:load --append
```

---

## 🌐 Utilisation de l’API

### Lancer le serveur

```bash
  symfony serve
```

API accessible : `http://127.0.0.1:8000`

---

### Endpoints principaux

| Méthode | URL              | Description                                      |
|---------|------------------| ------------------------------------------------ |
| POST    | /api/login_check | Liste tous les produits                          |
| GET     | /api/products/   | Détails d’un produit                             |

---

### Test avec Postman

1. Crée une requête avec l’URL de ton API.
2. Choisis la méthode HTTP (GET, POST, PUT, DELETE).
3. Ajoute le header `Content-Type: application/json`.
4. Si authentification requise, ajoute ton token ou identifiants. tu l'obtiens en ajoutant au body de la requête username et password. Exemple : {"username" : "user0@example.com", "password": "123456"}
5. Envoie la requête et visualise la réponse JSON.


## ⚡ Conseils

* Toujours vérifier que les fixtures sont chargées avant d’utiliser l’API.
* Utiliser migrations plutôt que `schema:update --force` en production.
* Pour réinitialiser la base et les fixtures rapidement :

```bash
  bin/console doctrine:database:drop --force
  php bin/console doctrine:database:create
  php bin/console doctrine:migrations:migrate
  php bin/console doctrine:fixtures:load
```
