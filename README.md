# Billeterie inter-evenement

### Déploiement
Merci de créer une branche par événement au format "deploy/votreprojet.utt.fr" puis d'ajouter le `.gitlab-ci.yml` suivant sur cette branche.

```
###############################################################################
#                               Variables                                     #
###############################################################################
variables:
    DOKKU_HOST: 'node1.dokku.uttnetgroup.net'
    PROJECT_NAME: 'votreprojet.assos.utt.fr'

###############################################################################
#                                 Cache                                       #
###############################################################################
cache:
  untracked: false
  paths:
    - vendor/
  key: 'web_dependencies'

###############################################################################
#                                Templates                                    #
###############################################################################
.deploy_template: &deploy_definition
  image: ubuntu
  stage: deploy
  before_script:
    # Install
    - apt-get update -y
    - which ssh-keyscan || (apt-get install -y ssh &>/dev/null)
    - which git || (apt-get install -y git &>/dev/null)
    - which gzip || (apt-get install -y gzip &>/dev/null)
    - which ssh-agent || (apt-get install openssh-client -y)
    # Add ssh private key $SSH_DEPLOY_KEY
    - mkdir -p ~/.ssh
    - eval $(ssh-agent -s)
    - ssh-add <(echo "$SSH_DEPLOY_KEY")
    # SSH config
    - echo -e "Host *\n\tStrictHostKeyChecking no\n\n" > ~/.ssh/config
    # Add dokku to known hosts
    - ssh-keyscan -H $DOKKU_HOST >> ~/.ssh/known_hosts
  script:
    - git push dokku@$DOKKU_HOST:$PROJECT_NAME HEAD:refs/heads/master -f

###############################################################################
#                                  Stages                                     #
###############################################################################
stages:
  - deploy

deploy_to_dokku:
  <<: *deploy_definition
  only:
    - deploy/$PROJECT_NAME
  environment: production
```
Pensez à modifier
