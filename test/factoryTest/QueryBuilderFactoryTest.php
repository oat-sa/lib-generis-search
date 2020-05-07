<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace oat\taoSearch\factoryTest;

use oat\search\factory\QueryBuilderFactory;
use oat\search\QueryBuilder;
use oat\search\test\UnitTestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use Zend\ServiceManager\ServiceManager;

/**
 * Description of QueryBuilderFactoryTest
 *
 * @author christophe
 */
class QueryBuilderFactoryTest extends UnitTestHelper
{
    /** @var MockObject|QueryBuilderFactory */
    protected $instance;
    
    public function setup(): void
    {
        $this->instance = $this->getMockBuilder(QueryBuilderFactory::class)
            ->setMethods(['isValidClass', 'getServiceLocator'])
            ->getMock();
    }
    
    public function testInvokeFactory() {
        
        $fixtureOptions = [
            'test',
            'equal',
            'toto', 
            false
        ];
        
        $serviceManager =  $this->getMockBuilder(ServiceManager::class)->getMock();
        
        $testClassName  = '\\oat\\search\\QueryBuilderInterface';
        $mockTest       = $this->getMockBuilder(QueryBuilder::class)->getMock();
        
        $mockTest->expects($this->once())
                ->method('setOptions')
                ->with($fixtureOptions)
                ->willReturn($mockTest);
        $mockTest->expects($this->once())
                ->method('setServiceLocator')
                ->with($serviceManager)
                ->willReturn($mockTest);
        
        
        $serviceManager->expects($this->once())
                ->method('get')
                ->with($testClassName)
                ->willReturn($mockTest);
        
        $this->instance->expects($this->once())->method('isValidClass')->with($mockTest)->willReturn(true);
        $this->instance->expects($this->exactly(2))->method('getServiceLocator')->willReturn($serviceManager);
        $this->assertEquals($mockTest , $this->instance->get($testClassName , $fixtureOptions));
    }
    
}
