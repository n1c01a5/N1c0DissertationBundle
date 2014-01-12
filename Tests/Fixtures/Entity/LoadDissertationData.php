<?php

namespace N1c0\DissertationBundle\Tests\Fixtures\Entity;

use N1c0\DissertationBundle\Entity\Dissertation;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadDissertationData implements FixtureInterface
{
    static public $dissertations = array();

    public function load(ObjectManager $manager)
    {
        $dissertation = new Dissertation();
        $dissertation->setTitle('title');
        $dissertation->setBody('body');

        $manager->persist($dissertation);
        $manager->flush();

        self::$dissertations[] = $dissertation;
    }
}
