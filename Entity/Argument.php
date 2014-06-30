<?php

namespace N1c0\DissertationBundle\Entity;

use N1c0\DissertationBundle\Model\Argument as AbstractArgument;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *     name = "self",
 *     href = @Hateoas\Route(
 *         "api_1_get_dissertation_part_argument",
 *         parameters = { "id" = "expr(object.getPart().getDissertation().getId())", "partId" = "expr(object.getPart().getId())" ,"argumentId" = "expr(object.getId())" },
 *         absolute = true
 *     )
 * )
 */
abstract class Argument extends AbstractArgument
{

}
