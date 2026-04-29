# Verify Actu - API REST Laravel

## 1. Présentation du projet

Verify Actu est une application web basée sur une API REST.  
Le projet a été réalisé dans le cadre de la matière **Principe de programmation**.

L'objectif de l'application est de proposer une plateforme de publication et de vérification d'articles.  
Des journalistes peuvent proposer des articles, des modérateurs peuvent les vérifier et leur attribuer un score de fiabilité, les valider les supprimer ou les restaurer, et des utilisateurs peuvent consulter les articles validés, les liker et recevoir des notifications.

Le projet utilise Laravel pour le backend, Eloquent comme ORM, MySQL pour la base de données, Docker pour la conteneurisation et Docker Compose pour lancer tous les services.

---

## 2. Ce que l'application permet de faire

L'application permet de gérer plusieurs actions principales :

- créer un compte avec un rôle ;
- se connecter avec un token API ;
- proposer un article en tant que journaliste ;
- consulter les articles validés ou restaurés ;
- permettre au modérateur de voir tous les articles ;
- attribuer un score de fiabilité à un article ;
- valider un article ;
- supprimer un article ;
- restaurer un article ;
- liker ou retirer son like sur un article ;
- recevoir des notifications ;
- supprimer une notification en la marquant comme lue ;
- tester rapidement les données avec des routes de démonstration.

L'application gère trois rôles :

- utilisateur ;
- journaliste ;
- modérateur.

---

## 3. Technologies utilisées

- PHP 8.2
- Laravel
- Laravel Sanctum
- Eloquent ORM
- MySQL 8
- Docker
- Docker Compose
- phpMyAdmin
- Git / GitHub

---

## 4. Architecture du projet

Le projet est organisé autour de plusieurs services lancés avec Docker Compose.

```txt
Navigateur / Postman
        |
        v
API Laravel REST
        |
        v
Base de données MySQL
```

Le conteneur `app` contient l'application Laravel.  
Il expose l'API REST sur le port `8000`.

Le conteneur `db` contient la base de données MySQL.  
Il stocke les utilisateurs, les profils, les articles, les likes et les notifications.

Le conteneur `phpmyadmin` permet de consulter la base de données plus facilement depuis le navigateur.

Les services communiquent entre eux grâce au réseau Docker créé automatiquement par Docker Compose.

```txt
app          → Laravel API
db           → MySQL
phpmyadmin   → Interface de gestion de la base
```

---

## 5. Rôles de l'application

### Utilisateur

Un utilisateur peut :

- consulter les articles validés ou restaurés ;
- liker un article ;
- retirer son like ;
- consulter ses notifications ;
- supprimer une notification ;
- supprimer toutes ses notifications.

### Journaliste

Un journaliste peut :

- se connecter ;
- proposer un article ;
- voir son article passer en attente de validation ;
- recevoir une notification quand son article est validé, supprimé ou restauré.

### Modérateur

Un modérateur peut :

- consulter tous les articles ;
- voir les articles en attente, validés, supprimés et restaurés ;
- attribuer un score de fiabilité ;
- valider un article ;
- supprimer un article ;
- restaurer un article ;
- recevoir des notifications.

---

## 6. Relations ORM utilisées

Le projet utilise Eloquent ORM.  
Les relations principales sont définies dans les modèles Laravel.

### One-to-One

Un utilisateur possède un seul profil.

```txt
User 1 --- 1 Profile
```

Dans le code :

```php
User::hasOne(Profile::class)
Profile::belongsTo(User::class)
```

Cette relation permet de séparer les informations de connexion de l'utilisateur et les informations complémentaires du profil.

---

### One-to-Many

Un journaliste peut proposer plusieurs articles.

```txt
User 1 --- n Article
```

Dans le code :

```php
User::hasMany(Article::class, 'journalist_id')
Article::belongsTo(User::class, 'journalist_id')
```

Cela signifie qu'un journaliste peut avoir plusieurs articles, mais qu'un article appartient à un seul journaliste.

---

### One-to-Many pour les notifications

Un utilisateur peut recevoir plusieurs notifications.

```txt
User 1 --- n Notification
```

Dans le code :

```php
User::hasMany(Notification::class)
Notification::belongsTo(User::class)
```

Cela permet d'afficher uniquement les notifications de l'utilisateur connecté.

---

### Many-to-One

Plusieurs articles peuvent appartenir au même journaliste.

```txt
Article n --- 1 User
```

Dans le code :

```php
Article::belongsTo(User::class, 'journalist_id')
```

Elle permet, à partir d'un article, de retrouver le journaliste qui l'a proposé.

---

### Many-to-Many

Plusieurs utilisateurs peuvent liker plusieurs articles.

```txt
User n --- n Article
```

Cette relation passe par la table intermédiaire `likes`.

Dans le code :

```php
User::belongsToMany(Article::class, 'likes')
Article::belongsToMany(User::class, 'likes')
```

La table `likes` contient :

```txt
user_id
article_id
```

Elle empêche aussi un utilisateur de liker plusieurs fois le même article grâce à une contrainte unique sur `user_id` et `article_id`.

---

### Autres relations utilisées

Un article peut aussi être lié à plusieurs actions du modérateur :

```txt
validated_by
deleted_by
restored_by
```

Ces champs permettent de savoir quel modérateur a validé, supprimé ou restauré un article.
Notre Projet contient encore d'autres relation mais on les as mentionné partout dans le code en tant que commentaire pour facilité la compréhension mais on peut pas tous les mettres ici sinon le readMe va etre beaucoup trop chargé.

---

## 7. Statuts des articles

Un article peut avoir plusieurs statuts :

```txt
pending    : article proposé mais pas encore validé
validated  : article validé par le modérateur
deleted    : article supprimé
restored   : article restauré
```

La route publique `/api/articles` affiche seulement les articles avec le statut :

```txt
validated
restored
```

Les articles `pending` et `deleted` ne sont pas affichés publiquement.  
Ils sont visibles côté modérateur ou dans la route de démonstration.

---

## 8. Lancement du projet avec Docker

Avant de lancer le projet, Docker Desktop doit être ouvert (C TRES IMPORTANT SINON ON AURA UN MESSAGE D'ERREUR).

À la racine du projet, il faut taper :

```bash
docker compose up -d --build
```

Ensuite, il faut lancer les migrations et les données de test :

```bash
docker compose exec app php artisan migrate:fresh --seed
```

L'API est disponible ici :

```txt
http://localhost:8000
```

phpMyAdmin est disponible ici :

```txt
http://localhost:8080
```

les identifiants phpMyAdmin :

```txt
Serveur : db
Utilisateur : Waris_Abir
Mot de passe : Waris_Abir123
Base : verify_actu
```

---

## 9. Volume Docker utilisé

Le projet utilise un volume Docker pour garder les données MySQL même si les conteneurs sont arrêtés.

Dans le fichier `docker-compose.yml`, le volume utilisé est :

```txt
verify_actu_mysql_data
```

Il est relié au conteneur MySQL avec :

```txt
/var/lib/mysql
```

Cela permet de conserver les données de la base entre deux lancements de Docker Compose.

Si on veut supprimer complètement la base Docker et repartir de zéro, on peut utiliser :

```bash
docker compose down -v
```

Puis relancer :

```bash
docker compose up -d --build
docker compose exec app php artisan migrate:fresh --seed
```

---

## 10. Comptes de test

Le seeder(DatabaseSeeder.php) crée plusieurs comptes pour tester rapidement l'application.

### Utilisateurs

```txt
Nom : Waris
Email : waris@test.com
Mot de passe : Waris123
Rôle : utilisateur
```

```txt
Nom : Abir
Email : abir@test.com
Mot de passe : Abir123
Rôle : utilisateur
```

### Journalistes

```txt
Nom : Journaliste 1
Email : journaliste@test.com
Mot de passe : password123
Rôle : journaliste
```

```txt
Nom : Journaliste 2
Email : journaliste2@test.com
Mot de passe : password123
Rôle : journaliste
```

### Modérateur

```txt
Nom : Moderateur Test
Email : modo@test.com
Mot de passe : password123
Rôle : moderateur
```

---

## 11. URL accessibles directement dans le navigateur

Certaines routes peuvent être ouvertes directement dans la barre d'URL du navigateur.

### Voir les articles publics

```txt
http://localhost:8000/api/articles
```

Cette route affiche seulement les articles validés ou restaurés.

---

### Voir un article précis

```txt
http://localhost:8000/api/articles/1
```

Cette route affiche un article précis via son ID, seulement s'il est public.

---

### Voir tous les articles pour la démonstration

```txt
http://localhost:8000/api/demo/articles-all
```

Cette route affiche tous les articles, y compris :

```txt
pending
validated
deleted
restored
```

Elle sert surtout à montrer les données de test rapidement.

---

### Voir tous les likes de démonstration

```txt
http://localhost:8000/api/demo/likes
```

Cette route permet de voir tous les likes.

---

### Voir toutes les notifications de démonstration

```txt
http://localhost:8000/api/demo/notifications
```

Cette route affiche toutes les notifications, même celles déjà lues.

Elle permet de montrer la différence entre :

```txt
is_read = false
is_read = true
```
Bien entendu dans les profils des utilisateurs deja connectés, on ne voit pas les notifications avec is_read= true parce qu'elles sont supprimés(caché).

---

## 12. Routes principales de l'API

### Authentification

| Méthode | Route | Description |
|---|---|---|
| POST | `/api/register` | Créer un compte |
| POST | `/api/login` | Se connecter |
| POST | `/api/logout` | Se déconnecter |
| GET | `/api/user` | Voir l'utilisateur connecté |

---

### Articles

| Méthode | Route | Description |
|---|---|---|
| GET | `/api/articles` | Voir les articles publics |
| GET | `/api/articles/{id}` | Voir un article précis |
| POST | `/api/articles` | Proposer un article en tant que journaliste |

---

### Modération

| Méthode | Route | Description |
|---|---|---|
| GET | `/api/moderator/articles` | Voir tous les articles |
| PATCH | `/api/moderator/articles/{id}/score` | Ajouter ou modifier un score |
| PATCH | `/api/moderator/articles/{id}/validate` | Valider un article |
| DELETE | `/api/moderator/articles/{id}` | Supprimer un article |
| PATCH | `/api/moderator/articles/{id}/restore` | Restaurer un article |

---

### Likes

| Méthode | Route | Description |
|---|---|---|
| POST | `/api/articles/{id}/like` | Liker un article |
| DELETE | `/api/articles/{id}/like` | Retirer son like |

---

### Notifications

| Méthode | Route | Description |
|---|---|---|
| GET | `/api/notifications` | Voir ses notifications |
| DELETE | `/api/notifications/{id}` | Supprimer une notification |
| DELETE | `/api/notifications` | Supprimer toutes ses notifications |

---

### Routes de démonstration (creer uniquement pour mieux expliquer les fonctionnalités pour mr Youcef Samir et les personnes qui vont examiner notre projet)

| Méthode | Route | Description |
|---|---|---|
| GET | `/api/demo/articles-all` | Voir tous les articles |
| GET | `/api/demo/likes` | Voir tous les likes |
| GET | `/api/demo/notifications` | Voir toutes les notifications |

---

## 13. Exemples de requêtes JSON avec Postman

Pour tester les differentes routes, on a choisi d'utiliser **Postman**.
On a essayé de vous clarifier au maximum comment utiliser Postman surtout pour les etudiants qui vont noter le projet
Dans Postman, il faut choisir :

```txt
Méthode : POST, PATCH, DELETE ou GET selon la route
URL : http://localhost:8000/api/...
Body : raw → JSON quand la route demande des données
Authorization : Bearer Token quand la route est protégée
```

Les routes protégées nécessitent d'abord une connexion.  
Après la connexion, l'API renvoie un `token`. Ce token doit ensuite être copié dans Postman dans :

```txt
Authorization
Type : Bearer Token
Token : coller le token reçu
```
---

### Connexion avec un utilisateur

Cette requête permet de connecter l'utilisateur Waris ou Abir.

```txt
Méthode : POST
URL : http://localhost:8000/api/login
```

Dans Postman, aller dans :

```txt
Body → raw → JSON
```

Puis mettre :

```json
{
  "email": "waris@test.com",
  "password": "Waris123"
}
```
OU 
```json
{
  "email": "abir@test.com",
  "password": "Abir123"
}
```

Après avoir cliqué sur **Send**, l'API renvoie une réponse JSON avec les informations de l'utilisateur connecté et un token :

```json
{
  "message": "Connexion réussie.",
  "user": {
    "id": 1,
    "name": "Waris OU Abir",
    "email": "waris@test.com OU abir@test.com",
    "role": "utilisateur"
  },
  "token": "exemple_de_token"
}
```

Le champ `token` sert à accéder aux routes protégées de Waris OU Abir, par exemple ses notifications ou ses likes.

---

### Connexion avec les journalistes

Le seeder crée deux comptes journalistes.

#### Journaliste 1

```txt
Méthode : POST
URL : http://localhost:8000/api/login
```

Dans **Body → raw → JSON** :

```json
{
  "email": "journaliste@test.com",
  "password": "password123"
}
```

#### Journaliste 2

```txt
Méthode : POST
URL : http://localhost:8000/api/login
```

Dans **Body → raw → JSON** :

```json
{
  "email": "journaliste2@test.com",
  "password": "password123"
}
```

Le token reçu permet ensuite au journaliste connecté de proposer un article avec la route :

```txt
POST http://localhost:8000/api/articles
```
---

### Connexion avec le modérateur

Cette requête permet de connecter le modérateur.

```txt
Méthode : POST
URL : http://localhost:8000/api/login
```

Dans **Body → raw → JSON** :

```json
{
  "email": "modo@test.com",
  "password": "password123"
}
```

Le token reçu permet ensuite au modérateur de gérer les articles : score, validation, suppression et restauration.

---

### Voir l'utilisateur connecté

Cette route permet de vérifier à quel compte correspond le token utilisé.

```txt
Méthode : GET
URL : http://localhost:8000/api/user
Authorization : Bearer Token
```

Exemple : si on met le token de Waris, l'API renvoie les informations de Waris.

---

### Création d'un article par un journaliste

Cette route permet à un journaliste de proposer un article.  
Elle nécessite un token de journaliste.

```txt
Méthode : POST
URL : http://localhost:8000/api/articles
Authorization : Bearer Token du journaliste
```

Dans **Body → raw → JSON** :

```json
{
  "title": "Nouvel article proposé",
  "content": "Contenu de l'article proposé par un journaliste."
}
```

L'article est créé avec le statut :

```txt
pending
```

Il devra ensuite être vérifié par un modérateur.

---

### Voir tous les articles côté modérateur

Cette route permet au modérateur de voir tous les articles, y compris les articles en attente ou supprimés.  
Elle nécessite un token de modérateur.

```txt
Méthode : GET
URL : http://localhost:8000/api/moderator/articles
Authorization : Bearer Token du modérateur
```

Cette route est utile pour montrer que le modérateur voit :

```txt
pending
validated
deleted
restored
```

---

### Attribution d'un score par le modérateur

Cette route permet au modérateur d'attribuer un score de fiabilité à un article.  
Elle nécessite un token de modérateur.

```txt
Méthode : PATCH
URL : http://localhost:8000/api/moderator/articles/1/score
Authorization : Bearer Token du modérateur
```
Ici "1" reprensente l'ID de l'article
Dans **Body → raw → JSON** :

```json
{
  "reliability_score": 85
}
```

Le score doit être compris entre `0` et `100`.

---

### Validation d'un article par le modérateur

Cette route permet au modérateur de valider un article.  
Elle nécessite un token de modérateur.

```txt
Méthode : PATCH
URL : http://localhost:8000/api/moderator/articles/1/validate
Authorization : Bearer Token du modérateur
```

Après validation, l'article devient visible dans la route publique :

```txt
http://localhost:8000/api/articles
```

---

### Suppression d'un article par le modérateur

Cette route permet au modérateur de supprimer un article.  
Elle nécessite un token de modérateur.

```txt
Méthode : DELETE
URL : http://localhost:8000/api/moderator/articles/1
Authorization : Bearer Token du modérateur
```

L'article n'est pas supprimé physiquement de la base.  
Son statut passe à :

```txt
deleted
```

Il ne s'affiche donc plus dans la route publique `/api/articles`.

---

### Restauration d'un article par le modérateur

Cette route permet au modérateur de restaurer un article supprimé.  
Elle nécessite un token de modérateur.

```txt
Méthode : PATCH
URL : http://localhost:8000/api/moderator/articles/1/restore
Authorization : Bearer Token du modérateur
```

Après restauration, l'article prend le statut :

```txt
restored
```

Il redevient visible dans la route publique `/api/articles`.

---

### Liker un article avec un utilisateur

Cette route permet à un utilisateur de liker un article.  
Elle nécessite un token utilisateur.

```txt
Méthode : POST
URL : http://localhost:8000/api/articles/1/like
Authorization : Bearer Token de l'utilisateur
```

Si le like fonctionne, le compteur `likes_count` augmente dans la route :

```txt
http://localhost:8000/api/articles
```

---

### Retirer son like

Cette route permet à un utilisateur de retirer son like.  
Elle nécessite un token utilisateur.

```txt
Méthode : DELETE
URL : http://localhost:8000/api/articles/1/like
Authorization : Bearer Token de l'utilisateur
```

Si le unlike fonctionne, le compteur `likes_count` diminue.

---

### Voir ses notifications

Cette route permet de voir uniquement les notifications de l'utilisateur connecté.  
Elle nécessite un token.

```txt
Méthode : GET
URL : http://localhost:8000/api/notifications
Authorization : Bearer Token
```

Par exemple, si on utilise le token de Waris, l'API renvoie seulement les notifications de Waris avec :

```txt
is_read = false
```

Les notifications déjà lues ou supprimées ne sont pas affichées dans cette route.

---

### Supprimer une notification

Cette route permet de supprimer une seule notification.  
Elle nécessite un token.

```txt
Méthode : DELETE
URL : http://localhost:8000/api/notifications/1
Authorization : Bearer Token
```

En réalité, la notification n'est pas supprimée physiquement de la base.  
Son champ `is_read` passe à :

```txt
true
```

Elle disparaît donc de la liste normale des notifications.

---

### Supprimer toutes ses notifications

Cette route permet de supprimer toutes les notifications visibles de l'utilisateur connecté.  
Elle nécessite un token.

```txt
Méthode : DELETE
URL : http://localhost:8000/api/notifications
Authorization : Bearer Token
```

Toutes les notifications non lues de l'utilisateur passent à :

```txt
is_read = true
```

---

### Résumé pour tester avec Postman

Pour tester correctement les routes protégées :

```txt
1. Envoyer POST /api/login avec un compte de test
2. Copier le token reçu dans la réponse
3. Aller dans Authorization dans Postman
4. Choisir Bearer Token
5. Coller le token
6. Envoyer la requête protégée
```

Exemple :

```txt
Token de Waris OU Abir      → tester les likes et les notifications utilisateur
Token du journaliste → tester la création d'article
Token du modérateur  → tester le score, la validation, la suppression et la restauration
```

---

## 14. Gestion des notifications

Les notifications possèdent un champ `is_read`.

```txt
is_read = false : notification encore visible
is_read = true  : notification considérée comme lue ou supprimée
```

Quand un utilisateur supprime une notification, elle n'est pas supprimée physiquement de la base.  
Elle est simplement marquée comme lue avec `is_read = true`.

Cela permet de garder une trace en base tout en cachant la notification de l'affichage normal.

---

## 15. Données de test

Le projet contient un seeder(DatabaseSeeder.php) Laravel qui crée automatiquement :

- 2 utilisateurs ;
- 2 journalistes ;
- 1 modérateur ;
- des profils ;
- des articles validés ;
- un article en attente ;
- un article supprimé ;
- un article restauré ;
- plusieurs likes ;
- plusieurs notifications.

Ces données permettent de tester rapidement les principales fonctionnalités de l'application.

Pour recréer les données de test :

```bash
docker compose exec app php artisan migrate:fresh --seed
```

---

## 16. Commandes utiles

Voir les routes Laravel :

```bash
docker compose exec app php artisan route:list
```

Relancer les migrations avec les données de test :

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Arrêter les conteneurs :

```bash
docker compose down
```

Relancer les conteneurs :

```bash
docker compose up -d --build
```

Voir les conteneurs lancés :

```bash
docker compose ps
```

---
## 17. Publication de l'image Docker Hub

Cette partie sera complétée après la publication de l'image Docker de l'API sur Docker Hub.

Lien de l'image Docker Hub :

```txt
À compléter
```

Commande prévue pour récupérer l'image :

```bash
docker pull NOM_UTILISATEUR_DOCKER/verify-actu-api:latest
```

Commande prévue pour lancer le projet :

```bash
docker compose up -d
```
---

## 18. Objectif technique du projet

Ce projet montre la création d'une application en tant que API REST structurée avec Laravel.

Il respecte plusieurs objectifs techniques :

- backend exposant une API REST ;
- utilisation des méthodes HTTP GET, POST, PATCH et DELETE ;
- authentification par token ;
- base de données relationnelle ;
- utilisation d'Eloquent ORM ;
- relations One-to-One, One-to-Many, Many-to-One et Many-to-Many ;
- gestion des rôles ;
- gestion des erreurs avec des réponses JSON ;
- données de test avec un seeder ;
- conteneurisation avec Docker ;
- orchestration avec Docker Compose.