<?php

namespace N1c0\DissertationBundle\Entity;

use N1c0\DissertationBundle\Model\Part as AbstractPart;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *     name = "self",
 *     href = @Hateoas\Route(
 *         "api_1_get_dissertation_part",
 *         parameters = { "id" = "expr(object.getDissertation().getId())", "partId" = "expr(object.getId())" },
 *         absolute = true
 *     )
 * )
 */
abstract class Part extends AbstractPart
{

}
