# Todolist #

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/f50b4977971f45cf98a445617f1a1300)](https://www.codacy.com/gh/boumebar/todolist/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=boumebar/todolist&amp;utm_campaign=Badge_Grade)

Formation ***Développeur d'application - PHP / Symfony***.  

## Informations du projet ##
Amélioration et documentation d'un projet existant Todolist

### Besoin client
Votre rôle ici est donc d’améliorer la qualité de l’application.
Vous êtes en charge des tâches suivantes :

l’implémentation de nouvelles fonctionnalités ;
la correction de quelques anomalies ;
et l’implémentation de tests automatisés.

Il vous est également demandé d’analyser le projet grâce à des outils vous permettant d’avoir une vision d’ensemble de la qualité du code et des différents axes de performance de l’application.

## Installation ##

1. Clonez ou téléchargez le repository GitHub :
```
    git clone https://github.com/boumebar/todolist.git
```
2. Configurer vos variables d'environnement dans le fichier .env .
3. Téléchargez et installez les dépendances du projet avec [Composer](https://getcomposer.org/download/) :
```
    composer install
```
4. Creer votre base de données
```
    $ php bin/console doctrine:database:create
    $ php bin/console doctrine:migrations:migrate
```
5. Installer vos fixtures
```
    $ php bin/console doctrine:fixtures:load
```


## Félicitation l'installation est terminée , vous pouvez tester votre API  
