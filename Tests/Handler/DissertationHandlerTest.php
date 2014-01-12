<?php

namespace N1c0\DissertationBundle\Tests\Handler;

use N1c0\DissertationBundle\Handler\DissertationHandler;
use N1c0\DissertationBundle\Model\DissertationInterface;
use N1c0\DissertationBundle\Entity\Dissertation;

class DissertationHandlerTest extends \PHPUnit_Framework_TestCase
{
    const PAGE_CLASS = 'n1c0\DissertationBundle\Tests\Handler\DummyDissertation';

    /** @var DissertationHandler */
    protected $dissertationHandler;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $om;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $repository;

    public function setUp()
    {
        if (!interface_exists('Doctrine\Common\Persistence\ObjectManager')) {
            $this->markTestSkipped('Doctrine Common has to be installed for this test to run.');
        }
        
        $class = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $this->om = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');

        $this->om->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo(static::PAGE_CLASS))
            ->will($this->returnValue($this->repository));
        $this->om->expects($this->any())
            ->method('getClassMetadata')
            ->with($this->equalTo(static::PAGE_CLASS))
            ->will($this->returnValue($class));
        $class->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(static::PAGE_CLASS));
    }


    public function testGet()
    {
        $id = 1;
        $dissertation = $this->getDissertation();
        $this->repository->expects($this->once())->method('find')
            ->with($this->equalTo($id))
            ->will($this->returnValue($dissertation));

        $this->dissertationHandler = $this->createDissertationHandler($this->om, static::PAGE_CLASS,  $this->formFactory);

        $this->dissertationHandler->get($id);
    }

    public function testAll()
    {
        $offset = 1;
        $limit = 2;

        $dissertations = $this->getDissertations(2);
        $this->repository->expects($this->once())->method('findBy')
            ->with(array(), null, $limit, $offset)
            ->will($this->returnValue($dissertations));

        $this->dissertationHandler = $this->createDissertationHandler($this->om, static::PAGE_CLASS,  $this->formFactory);

        $all = $this->dissertationHandler->all($limit, $offset);

        $this->assertEquals($dissertations, $all);
    }

    public function testPost()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('title' => $title, 'body' => $body);

        $dissertation = $this->getDissertation();
        $dissertation->setTitle($title);
        $dissertation->setBody($body);

        $form = $this->getMock('n1c0\DissertationBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($dissertation));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->dissertationHandler = $this->createDissertationHandler($this->om, static::PAGE_CLASS,  $this->formFactory);
        $dissertationObject = $this->dissertationHandler->post($parameters);

        $this->assertEquals($dissertationObject, $dissertation);
    }

    /**
     * @expectedException n1c0\DissertationBundle\Exception\InvalidFormException
     */
    public function testPostShouldRaiseException()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('title' => $title, 'body' => $body);

        $dissertation = $this->getDissertation();
        $dissertation->setTitle($title);
        $dissertation->setBody($body);

        $form = $this->getMock('n1c0\DissertationBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->dissertationHandler = $this->createDissertationHandler($this->om, static::PAGE_CLASS,  $this->formFactory);
        $this->dissertationHandler->post($parameters);
    }

    public function testPut()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('title' => $title, 'body' => $body);

        $dissertation = $this->getDissertation();
        $dissertation->setTitle($title);
        $dissertation->setBody($body);

        $form = $this->getMock('n1c0\DissertationBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($dissertation));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->dissertationHandler = $this->createDissertationHandler($this->om, static::PAGE_CLASS,  $this->formFactory);
        $dissertationObject = $this->dissertationHandler->put($dissertation, $parameters);

        $this->assertEquals($dissertationObject, $dissertation);
    }

    public function testPatch()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('body' => $body);

        $dissertation = $this->getDissertation();
        $dissertation->setTitle($title);
        $dissertation->setBody($body);

        $form = $this->getMock('n1c0\DissertationBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($dissertation));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->dissertationHandler = $this->createDissertationHandler($this->om, static::PAGE_CLASS,  $this->formFactory);
        $dissertationObject = $this->dissertationHandler->patch($dissertation, $parameters);

        $this->assertEquals($dissertationObject, $dissertation);
    }


    protected function createDissertationHandler($objectManager, $dissertationClass, $formFactory)
    {
        return new DissertationHandler($objectManager, $dissertationClass, $formFactory);
    }

    protected function getDissertation()
    {
        $dissertationClass = static::PAGE_CLASS;

        return new $dissertationClass();
    }

    protected function getDissertations($maxDissertations = 5)
    {
        $dissertations = array();
        for($i = 0; $i < $maxDissertations; $i++) {
            $dissertations[] = $this->getDissertation();
        }

        return $dissertations;
    }
}

class DummyDissertation extends Dissertation
{
}
