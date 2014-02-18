<?php

namespace N1c0\DissertationBundle\Entity;

use N1c0\DissertationBundle\Model\Dissertation as AbstractDissertation;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Serializer\XmlRoot("dissertation")
 *
 * @Hateoas\Relation(
 *     name = "self",
 *     href = @Hateoas\Route(
 *         "api_1_get_dissertation",
 *         parameters = { "id" = "expr(object.getId())" },
 *         absolute = true
 *     )
 * )
 */
abstract class Dissertation extends AbstractDissertation
{

}
