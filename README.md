Cron Bundle
===========

Cron integration for symfony.

## Installation

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
  // application/ApplicationKernel.php
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
