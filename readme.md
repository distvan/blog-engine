## Blog Engine

File based blog engine.

Goals:
- Creating a simple and minimal style blog engine with SEO support
- Getting 100% Google Page Insight in desktop and mobile mode
- Applying pure OOP paradigms including SOLID and DRY principles with MVC architecture
- Be as much secure as can
- Minimum installation and configuration

#### How to use:

Requirements:
- PHP 7.1 or greater
    - php-xml
- Composer package manager
- Slim 3.x microframework
- Codeception framework if you want to run tests

Firstly clone and cd into project directory and install dependencies

run composer:

```
composer install
```

run all tests:

```
php vendor/bin/codecept run unit
```

or a specific one:

```
php vendor/bin/codecept run unit ValidatorTest
```


If you want to modify the frontend code run:

```
gulp
```

If you want to run it a testing virtual environment run:

```
docker-compose up -d
```

or without virtual environment, cd to public dir and run

```
php -d variables_order=EGPCS -S localhost:8000
```

If everything is fine you are ready to use and just copy the whole directory (cache, lib, log, public, storage, vendor) to your server.
The default language is english but you can change it. See some config options in the lib/Config.php file.

### Load testing (Todo)

The engine is based on xml. It stores data in a descriptor files and directories in a local filesystem.
When the crawler or visitor get one page it loads from the filesystem.

Testing environment:

1. Ubuntu 16.14.5 x64, 1 GB RAM/3 CPU Frankfurt
2. Ubuntu 16.14.5 x64, 4 GB RAM/2 CPU Frankfurt

Autogenerated category number: 100

Autogenerated article number:

1. 100
2. 300
3. 600
4. 1000
5. 2000

Testing tools:

- Locust.io

### Google Page Insight Testing (Todo)



### More about goals

#### Motivation

I have some preconceptions about Wordpress. I dont like its whole structure and I hate install new plugins,
update and configure third party programs and setup. It is dangerous in a live environment because mostly the result are unknown.
It is easier for me to write a complete blog engine and it is a passion too. That was the reason to create this one.

#### Security

- The engine uses the Zend Escaper feature to prevent xss attacks.
- It uses Guard component to protect from cross site request forgery.
- The admin site access is always unique, generated during setup.
- XML DTD has readonly permission to prevent schema poisoning.
- The setup process force to set register globals off.
- Admin login restrictions after 3 failed login attempt (todo).
- It doesn't contain any vulnerable third party, unknown or untested code.
- Autobackup feature (todo).

### Simplicity

- just copy the files to the server and fill input fields during the install
- login to the admin and use