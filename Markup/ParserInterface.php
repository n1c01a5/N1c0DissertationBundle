<?php

/**
 * This file is part of the N1c0DissertationBundle package.
 *
 * (c) 
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace N1c0\DissertationBundle\Markup;

/**
 * Interface to implement to bridge a Markup parser to
 * N1c0DissertationBundle.
 *
 * @author Wagner Nicolas <contact@wagner-nicolas.com>
 */
interface ParserInterface
{
    /**
     * Takes a markup string and returns raw html.
     *
     * @param  string $raw
     * @return string
     */
    public function parse($raw);
}
