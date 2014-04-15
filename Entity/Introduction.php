<?php

namespace N1c0\DissertationBundle\Entity;

use N1c0\DissertationBundle\Model\Introduction as AbstractIntroduction;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *     name = "self",
 *     href = @Hateoas\Route(
 *         "api_1_get_dissertation_introduction",
 *         parameters = { "id" = "expr(object.getDissertation().getId())", "introductionId" = "expr(object.getId())" },
 *         absolute = true
 *     )
 * )
 */
abstract class Introduction extends AbstractIntroduction
{

}
