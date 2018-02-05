# Billetterie inter-evenement

## Déploiement sur Dokku
Documentation permettant de déployer une nouvelle Billetterie sur un nouveau domaine. Si vous souhaitez modifier une Billetterie déjà déployé, jetez un coup d'oeils aux branches avec le prefix `deploy/`.


### Configuration de dokku
Commencez par créer l'application sur dokku
```
dokku apps:create billetterie.gala.utt.fr
```
Nous allons maintenant régler les variables d'environment (les variables qui se trouvent dans le fichier `.env` lorsqu'on est en mode dev).

```
dokku config:set billetterie.gala.utt.fr \
    APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxx \
    APP_URL=https://billetterie.gala.utt.fr \
    \
    LANDING_DATE=2017-06-01 18:00:00
    \
    APP_NAME="Gala UTT" \
    APP_SUBNAME="Billetterie" \
    APP_CONTACT="gala@utt.fr" \
    \
    DB_CONNECTION=mysql \
    DB_HOST=sql.uttnetgroup.net \
    DB_DATABASE="billetterie.gala.utt.fr" \
    DB_USERNAME="billetterie.gala.utt.fr" \
    DB_PASSWORD="mot de passe sql" \
    \
    MAIL_DRIVER=smtp
    MAIL_HOST=mail.utt.fr
    \
    ETUPAY_APIKEY= \
    ETUPAY_ENDPOINT=https://etupay.utt.fr/initiate
    ETUPAY_SERVICEID= \
    \
    ETUUTT_CLIENT_ID= \
    ETUUTT_CLIENT_SECRET= \
    \
    PIWIK_SITE_ID= \
    GOOGLE_ANALYTICS_ID=
```

Quelques explications :
* `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` : Identifiants de la base de donnée MariaDB que vous devez créer au préalable.
* `APP_KEY` : A générer en utilisant la commande suivante dans n'importe quel projet laravel :
```
php artisan key:generate --show
```
* `LANDING_DATE` : Date d'ouverture de la Billetterie. Avant cette date, une page de compte à rebours sera affichée.


### Configuration du repo
Clonez le repo, et créez une branche au format `deploy/votreprojet.utt.fr`:
```
git clone https://gitlab.uttnetgroup.fr/bde/billetterie
git checkout master
git branch deploy/votreprojet.utt.fr
git checkout deploy/votreprojet.utt.fr
```
Configurez ensuite la variable `PROJECT_NAME` dans le `.gitlab-ci.yml`. Et ensuite on deploy !
```
git add .gitlab-ci.yml
git commit -m "Update .gitlab-ci.yml for votreprojet.utt.fr"
git push --set-upstream origin deploy/votreprojet.utt.fr
```
Vous pouvez maintenant vous rendre sur Gitlab pour voir l'état du déploiement.
