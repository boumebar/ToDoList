# ToDoList - Comment contribuer à l'evolution de l'application <br/>

Bienvenue dans ce guide du développeur qui vous permettra de contribuer aux évolutions de l'application avec les bonnes pratiques à suivre.

### 1. Installer le projet

- Installez le projet en local pour cela veuillez suivre les instructions dans le fichier [README.md](README.md).

### 2. Créer une branche

- Créez une nouvelle branche sous Gitavec la commande :

```
git checkout -b new-branch
```

### 3. Coder

Coder vos nouvelles fonvctionnalités ou vos ameliorations aux projet.
N'oubliez pas de respecter les standard de Php et de symfony.
Quelques liens :

- <a href="https://www.php-fig.org/psr/" target="_blank">PSR standard</a>
- <a href="https://symfony.com/doc/5.4/contributing/code/standards.html" target="_blank">Symfony standard</a>

### 4. Tester votre code

- Tester votre code a l'aide de PHPUnit.

```
$ ./vendor/bin/phpunit
```

- Mettez a jour votre taux de coverage a l'aide de cette commande :

```
$ ./vendor/bin/phpunit --coverage-html web/code-coverage
```

### 5. Soumetter votre code

- Créer une Pull Request en consultant la documentation officielle => [la documentation GitHub](https://docs.github.com/en/github/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/about-pull-requests).

### 5. Rappels sur les bonnes pratiques Symfony

- <a href="https://symfony.com/doc/4.4/best_practices.html" target="_blank">Symfony Best Practices</a>
