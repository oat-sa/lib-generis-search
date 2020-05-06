<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace oat\taoSearch\factoryTest;

use oat\search\factory\QueryFactory;
use oat\search\test\UnitTestHelper;

/**
 * Description of QueryFactory
 *
 * @author christophe
 */
class QueryFactoryTest extends UnitTestHelper
{

    protected $instance;

    public function setup(): void
    {

        $this->instance = $this->getMockBuilder(QueryFactory::class)
            ->getMock(['isValidClass', 'getServiceLocator']);

    }

    public function testInvokeFactory()
    {

        $fixtureOptions = [
            'test',
            'equal',
            'toto',
            false
        ];

        $serviceManager = $this->getMockBuilder('\\Zend\\ServiceManager\\ServiceManager');

        $testClassName = '\\oat\\search\\base\\QueryInterface';
        $mockTest = $this->getMockBuilder('\\stdClass')->getMock(['setOptions', 'setServiceLocator']);

        $mockTest->expects($this->once())
            ->method('setServiceLocator')
            ->with($serviceManager)
            ->willReturn($mockTest);

        $mockTest->expects($this->once())
            ->method('setOptions')
            ->with($fixtureOptions)
            ->willReturn($mockTest);

        $serviceManager->expects($this->once())
            ->method('get')
            ->with($testClassName)
            ->willReturn($mockTest);

        $this->setInaccessibleProperty($this->instance, 'serviceLocator', $serviceManager);
        $this->instance->expects($this->exactly(2))->method('getServiceLocator')->willReturn($serviceManager);
        $this->instance->expects($this->once())->method('isValidClass')->with($mockTest)->willReturn(true);
        $this->assertEquals($mockTest, $this->instance->get($testClassName, $fixtureOptions));
    }

}
