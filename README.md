# README - Back-end avec PHP Symfony

## Description du Projet

Ce projet consiste à gérer les stations de ski via une application web. Le back-end est développé en utilisant PHP Symfony, un framework PHP puissant pour construire des applications web. Il fournit une architecture robuste pour faciliter le développement, la maintenance et la scalabilité de l'application.
Installation

Pour mettre en place ce projet sur votre machine locale, suivez ces étapes :

### 1. Clonage du Répertoire

git clone https://github.com/Cours-Full-Stack-mastere1/back_ski
cd nom_du_dossier

### 2. Installation des Dépendances

symfony composer install

### 3.Configuration de la Base de Données

Configurez vos paramètres de base de données dans le fichier .env pour assurer la connexion à votre base de données MySQL ou tout autre SGBD de votre choix.

### 4.Création de la Base de Données

php bin/console doctrine:database:create
php bin/console doctrine:schema:update -f
php bin/console doctrine:fixtures:load

### 5. Lancement du Serveur Symfony

php bin/console server:run
