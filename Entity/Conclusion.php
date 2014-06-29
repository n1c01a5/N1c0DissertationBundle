<?php

namespace N1c0\DissertationBundle\Entity;

use N1c0\DissertationBundle\Model\Conclusion as AbstractConclusion;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *     name = "self",
 *     href = @Hateoas\Route(
 *         "api_1_get_dissertation_conclusion",
 *         parameters = { "id" = "expr(object.getDissertation().getId())", "conclusionId" = "expr(object.getId())" },
 *         absolute = true
 *     )
 * )
 */
abstract class Conclusion extends AbstractConclusion
{

}
