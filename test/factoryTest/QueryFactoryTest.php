<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace oat\taoSearch\factoryTest;

/**
 * Description of QueryFactory
 *
 * @author christophe
 */
class QueryFactoryTest extends \oat\search\test\UnitTestHelper 
{
    
    protected $instance;
    
    public function setup() {
        
    $this->instance = $this->getMock('\\oat\\search\\factory\\QueryFactory', ['isValidClass' , 'getServiceLocator']);
        
    }
    
    public function testInvokeFactory() {
        
         $fixtureOptions = [
            'test',
            'equal',
            'toto', 
            false
        ];
         
        $serviceManager =  $this->getMock('\\Zend\\ServiceManager\\ServiceManager');
        
        $testClassName  = '\\oat\\search\\base\\QueryInterface';
        $mockTest       = $this->getMock('\\stdClass' , ['setOptions' , 'setServiceLocator']);
        
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
        $this->assertEquals($mockTest , $this->instance->get($testClassName , $fixtureOptions));
    }
    
}
