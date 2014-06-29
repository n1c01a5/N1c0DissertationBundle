<?php

namespace N1c0\DissertationBundle\Entity;

use N1c0\DissertationBundle\Model\Transition as AbstractTransition;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *     name = "self",
 *     href = @Hateoas\Route(
 *         "api_1_get_dissertation_transition",
 *         parameters = { "id" = "expr(object.getDissertation().getId())", "transitionId" = "expr(object.getId())" },
 *         absolute = true
 *     )
 * )
 */
abstract class Transition extends AbstractTransition
{

}
