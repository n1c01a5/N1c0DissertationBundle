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
        new FOS\RestBundle\FOSRestBundle(),
        new JMS\SerializerBundle\JMSSerializerBundle(),
        new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
        new N1c0\DissertationBundle\N1c0DissertationBundle(),
    );
}
```
FOSRestBundle and NelmioApiDocBundle must be configured.

### C) Enable Http Method Override

[Enable HTTP Method override as described here](http://symfony.com/doc/master/cookbook/routing/method_parameters.html#faking-the-method-with-method)

As of symfony 2.3, you just have to modify your config.yml :

``` yaml
# app/config/config.yml

framework:
    http_method_override: true
```
    

Step 2: Setup Doctrine ORM mapping
----------------------------------

The ORM implementation does not provide a concrete Dissertation class for your use, you must create one. This can be done by extending the abstract entities provided by the bundle and creating the appropriate mappings.

For example, the dissertation entity:

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
For example, the argument entity:

``` php
<?php
// src/MyProject/MyBundle/Entity/Argument.php

namespace MyProject\MyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use N1c0\DissertationBundle\Entity\Argument as BaseArgument;

/**
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Argument extends BaseArgument
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Dissertation of this argument
     *
     * @var Thread
     * @ORM\ManyToOne(targetEntity="MyProject\MyBundle\Entity\Argument")
     */
    protected $dissertation;
}
```

Add in app/config/config.yml:
``` yaml
# N1c0DissertationBundle
n1c0_dissertation:
    db_driver: orm
    class:
        model:
            dissertation: MyProject\MyBundle\Entity\Dissertation
            argument: MyProject\MyBundle\Entity\Argument

entity_managers:
            default:
                mappings:
                    N1c0DissertationBundle: ~
                    MyBundleMyProjectBundle: ~
```

Step 3: Import N1c0DissertationBundle routing files
---------------------------------------------------

```
# /app/config/routing.yml
n1c0_dissertation:
    type: rest
    prefix: /api
    resource: "@N1c0Dissertation/Resources/config/routing.yml"
```

Content negociation
-------------------

Each ressource is accessible into different formats.

HTTP verbs:

For the dissertations:

GET:

In html format:
```
curl -i localhost:8000/api/v1/dissertations/10
```

In json format:
```
curl -i -H "Accept: application/json" localhost:8000/api/v1/dissertations/10
```

POST:

In html format:
```
curl -X POST -d "n1c0_dissertation_dissertation%5Btitle%5D=myTitle&n1c0_dissertation_dissertation%5Bbody%5D=myBody" http://localhost:8000/api/v1/dissertations
```

In json format:
```
curl -X POST -d '{"n1c0_dissertation_dissertation":{"title":"myTitle","body":"myBody"}}' http://localhost:8000/api/v1/dissertations.json --header "Content-Type:application/json" -v
```
PUT:

In json format:
```
curl -X PUT -d '{"n1c0_dissertation_dissertation":{"title":"myNewTitle","body":"myNewBody http://localhost:8000/api/v1/dissertations/10 --header "Content-Type:application/json" -v
```
For the arguments:

GET:

In json format:
```
curl -i -H "Accept: application/json" localhost:8000/api/v1/dissertations/10/arguments
```
POST:

In json format:
```
curl -X POST -d '{"n1c0_dissertation_argument":{"title":"myTitleArgument","body":"myBodyArgument"}}' http://localhost:8000/api/v1/dissertations/10/arguments.json --header "Content-Type:application/json" -v
```
PUT:

In json format:
```
curl -X PUT -d '{"n1c0_dissertation_argument":{"title":"myNewTitleArgument","body":"myNewBodyArgument"}}' http://localhost:8000/api/v1/dissertations/10/arguments/11 --header "Content-Type:application/json" -v 

Documentation as bonus (NelmioApiDocBundle)
-------------------------------------------

Go to http://localhost:8000/api/doc.
