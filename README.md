FunnyPet
=========

Gallery for users pet photos

Startup
=======

Initial setup (dependencies and database structure)
```
$ composer install
$ app/console doctrine:schema:create
$ app/console assets:install

```

Add correct facebook application information at app/config/config.yml
```
facebook:
    type:                facebook
    client_id:           **Your facebook application id**
    client_secret:       **Your facebook application secret code**
    scope:               "email"

```