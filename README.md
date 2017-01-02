Cron Bundle
===========

 [![Packagist](https://img.shields.io/packagist/v/cron/cron-bundle.svg?style=flat-square)](https://packagist.org/packages/cron/cron-bundle)
 [![Build Status](https://img.shields.io/travis/Cron/Symfony-Bundle.svg?style=flat-square)](https://travis-ci.org/Cron/Symfony-Bundle)
 [![Packagist](https://img.shields.io/packagist/dt/Cron/Cron-Bundle.svg?style=flat-square)](https://packagist.org/packages/cron/cron-bundle)
 [![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)
 
[Cron](https://github.com/Cron/Cron) integration for symfony.

Installation
------------

Installing this bundle can be done through these simple steps:

1. Add the bundle to your project as a composer dependency:
  ```javascript
  // composer.json
  {
      // ...
      require: {
          // ...
          "cron/cron-bundle": "1.0.*"
      }
  }
  ```

2. Update your composer installation:
  ```shell
  composer update
  ````

3. Add the bundle to your application kernel:
  ```php
  // app/AppKernel.php
  public function registerBundles()
  {
  	// ...
  	$bundle = array(
  		// ...
          new Cron\CronBundle\CronCronBundle(),
	  );
      // ...
  
      return $bundles;
  }
  ```

4. Update your DB schema
  ```shell
  app/console doctrine:schema:update
  ```

5. Start using the bundle:
  ```shell
  app/console cron:list
  app/console cron:run
  ```

6. To run your cron jobs automatically, add the following line to your (or whomever's) crontab:
  ```shell
  * * * * * /path/to/symfony/install/app/console cron:run 1>> /dev/null 2>&1
  ```

Available commands
------------------

### list
```shell
app/console cron:list
```
Show a list of all jobs. Job names are show with ```[x]``` if they are enabled and ```[ ]``` otherwise.

### create
```shell
app/console cron:create
```
Create a new job.

### delete
```shell
app/console cron:delete _jobName_
```
Delete a job. For your own protection, the job must be disabled first.

### enable
```shell
app/console cron:enable _jobName_
```
Enable a job.

### disable
```shell
app/console cron:disable _jobName_
```
Disable a job.

### run
```shell
app/console cron:run [--force] [job]
```
Run the cron.
If a job is given only this will be triggered.
You can trigger a specific job that is disabled by using _--force_.

Contributing
------------

> All code contributions - including those of people having commit access - must
> go through a pull request and approved by a core developer before being
> merged. This is to ensure proper review of all the code.
>
> Fork the project, create a feature branch, and send us a pull request.
>
> To ensure a consistent code base, you should make sure the code follows
> the [Coding Standards](http://symfony.com/doc/2.0/contributing/code/standards.html)
> which we borrowed from Symfony.
> Make sure to check out [php-cs-fixer](https://github.com/fabpot/PHP-CS-Fixer) as this will help you a lot.

If you would like to help, take a look at the [list of issues](http://github.com/Cron/CronBundle/issues).

Requirements
------------

PHP 5.3.2 or above

Author and contributors
-----------------------

Dries De Peuter - <dries@nousefreak.be> - <http://nousefreak.be>

See also the list of [contributors](https://github.com/Cron/CronBundle/contributors) who participated in this project.

License
-------

CronBundle is licensed under the MIT license.
