<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace oat\taoSearch\factoryTest;

/**
 * Description of QueryBuilderFactoryTest
 *
 * @author christophe
 */
class QueryBuilderFactoryTest extends \oat\search\test\UnitTestHelper 
{
    
    protected $instance;
    
    public function setup() {
        $this->instance = $this->getMock('\\oat\\search\\factory\\QueryBuilderFactory', ['isValidClass' , 'getServiceLocator']);
    }
    
    public function testInvokeFactory() {
        
        $fixtureOptions = [
            'test',
            'equal',
            'toto', 
            false
        ];
        
        $serviceManager =  $this->getMock('\\Zend\\ServiceManager\\ServiceManager');
        
        $testClassName  = '\\oat\\search\\QueryBuilderInterface';
        $mockTest       = $this->getMock('\\oat\\search\\QueryBuilder' , ['setOptions' , 'setServiceLocator']);
        
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
