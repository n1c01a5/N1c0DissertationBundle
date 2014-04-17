<?php

/**
 * This file is part of the N1c0DissertationBundle package.
 *
 * (c) 
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace N1c0\DissertationBundle\Model;

/**
 * A comment that holds a raw version of the comment allowing
 * for different markup languages to be used.
 *
 * @author Wagner Nicolas <contact@wagner-nicolas.com>
 */
interface RawDissertationInterface extends DissertationInterface
{
    /**
     * Gets the raw processed html.
     *
     * @return string
     */
    public function getRawBody();

    /**
     * Sets the processed body with raw html.
     *
     * @param string $rawBody
     */
    public function setRawBody($rawBody);
}
