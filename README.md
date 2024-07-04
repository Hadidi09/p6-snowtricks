# P6 SNOWTRICKS

Développez de A à Z le site communautaire SnowTricks

1. Clonez le projet

   `git clone https://github.com/Hadidi09/p6-snowtricks.git`

2. Téléchargez et installez les dépendances du projet avec composer

   `composer install`

> Renseignez les identifiants de votre base de données MYSQL dans le fichier .env.local comme ceci : 3. Créez un fichier **.env.local** à la racine de votre projet. 4. Dans ce fichier .env.local, ajoutez la ligne suivante pour configurer la connexion à votre base de données MySQL :

`DATABASE_URL="mysql://USER:PASSWORD@HOST:PORT/DB_NAME?serverVersion=5.1.36&charset=utf8mb4"`

3.  Remplacez les éléments suivants par vos informations :

> USER : Nom d'utilisateur de votre base de données

> PASSWORD : Mot de passe de votre base de données

> HOST : Adresse de votre serveur MySQL (127.0.0.1 pour localhost)

> PORT : Port de votre serveur MySQL (3306)

> DB_NAME : Nom de votre base de données 6.

Dans le fichier **.env** existant à la racine du projet, assurez-vous que la variable d'environnement suivante est présente :

    `` DATABASE_URL=${DATABASE_URL} ``

4.  Exécuter la commande :

`php bin/console doctrine:database:create`

5.  Pour créez les tables
    Exécuter une la commande :

`php bin/console make:migration`

puis la commande :

`php bin/console doctrine:migrations:migrate`

6. Pour créer des figures fictives rapidement, utilisez les fixtures :

   `php bin/console doctrine:fixtures:load`

7. Télécharger l'exécutable de mailHog , en vous rendant sur cette page : Mailhog tutoriel vous pouvez suivre cet article qui vous aide à l'installer et le lancer https://github.com/mailhog/MailHog/releases. En choississant la version correspondant à votre OS.

8. Lancez l'executable de mailHog et rendez vous ici : http://localhost:8025/ pour intercépter les mails.

9. Exécutez MailHog pour intercepter les mails envoyés depuis votre pc.
10. Démarrez le projet avec la commande

    `symfony serve`
