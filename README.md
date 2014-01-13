n1c0DissertationBundle
======================

Bundle to manage dissertations.

Step 1: Setting up the bundle
-----------------------------

### A) Download and install N1c0Dissertation

To install N1c0Dissertation run the following command

``` bash
$ php composer.phar require n1c01a5/n1c0dissertation-bundle
```

### B) Enable the bundle

Enable the required bundles in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new N1c0/DissertationBundle/N1c0DissertationBundle(),
    );
}
```

### C) Enable Http Method Override

[Enable HTTP Method override as described here](http://symfony.com/doc/master/cookbook/routing/method_parameters.html#faking-the-method-with-method)

As of symfony 2.3, you just have to modify your config.yml :

``` yaml
# app/config/config.yml

framework:
    http_method_override: true
```
    

Step 2: Create your Dissertation class
--------------------------------------


For example:

``` php
<?php
// src/MyProject/MyBundle/Entity/Dissertation.php

namespace MyProject\MyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use N1c0\DissertationBundle\Entity\Dissertation as BaseDissertation;

/**
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Dissertation extends BaseDissertation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
}
```
