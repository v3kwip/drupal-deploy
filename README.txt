Drupal deploy
====

### Install

(1) Build the code base

        git clone https://github.com/v3kwip/drupal-deploy.git
        cd drupal-deploy
        composer update --no-dev

(2) Make sure www-data user is owner of ./files/*

(3) Configure Drupal instances

        cp config.same.php config.php
        vim config.php # Config per your needs

(4) Run web server:

    php localhost:8989 -t ./public ./public/index.php

### Web-hooks:

(1) http://www.example.com/sync-sql/PROJECT_NAME
     OR /sync-sql/PROJECT_NAME/production:staging

(2) http://www.example.com/git-pull/PROJECT_NAME
     OR /git-pull/PROJECT_NAME/staging:master

(3) http://www.example.com/feature-revert/PROJECT_NAME
     OR /feature-revert/PROJECT_NAME/staging

(4) Combination:

    http://www.example.com/
      ?cmd[]=sync-sql/PROJECT_NAME/production:staging
      &cmd[]=git-pull/PROJECT_NAME/staging:master
      &cmd[]=feature-revert/PROJECT_NAME/staging
