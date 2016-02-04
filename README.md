[![Build Status](https://travis-ci.org/MediaMonks/SymfonyRestApiBundle.svg?branch=master)](https://travis-ci.org/MediaMonks/SymfonyRestApiBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/MediaMonks/SymfonyRestApiBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/MediaMonks/SymfonyRestApiBundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/MediaMonks/SymfonyRestApiBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/MediaMonks/SymfonyRestApiBundle/?branch=master)
[![Total Downloads](https://poser.pugx.org/mediamonks/rest-api-bundle/downloads)](https://packagist.org/packages/mediamonks/rest-api-bundle)
[![Latest Stable Version](https://poser.pugx.org/mediamonks/rest-api-bundle/v/stable)](https://packagist.org/packages/mediamonks/rest-api-bundle)
[![Latest Unstable Version](https://poser.pugx.org/mediamonks/rest-api-bundle/v/unstable)](https://packagist.org/packages/mediamonks/rest-api-bundle)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/c42e43fd-9c7b-47e1-8264-3a98961e9236.svg)](https://insight.sensiolabs.com/projects/c42e43fd-9c7b-47e1-8264-3a98961e9236)
[![License](https://poser.pugx.org/mediamonks/rest-api-bundle/license)](https://packagist.org/packages/mediamonks/rest-api-bundle)

MediaMonksRestApiBundle
=======

This bundle provides tools to implement an API according to the MediaMonks REST API specifications:

- Converts all responses automatically to the specification
- Accepts application/json, application/x-www-form-urlencoded & multipart/form-data input
- Supports json and xml response
- Supports method overriding
- Supports forcing a "200 OK" status method
- Supports paginated responses

Requirements
============

- [Symfony Framework](https://github.com/symfony/symfony) version 2.3+
- [JMSSerializerBundle](https://github.com/schmittjoh/JMSSerializerBundle) version 1.0+

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require mediamonks/rest-api-bundle "~1.0@dev"
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new MediaMonks\RestApiBundle\MediaMonksRestApiBundle(),
        );

        // ...
    }

    // ...
}
```

Usage
=====

By default the event subscriber will be active on all calls that start with /api, /api/doc is ignored.

You can simply return a string, array, object, Symfony response or throw an exception from a controller and it will be
automatically converted into a proper output conforming to the MediaMonks Rest API Spec.
