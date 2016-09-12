<?php

/*
 * This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; under version 2
 *  of the License (non-upgradable).
 *  
 * This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 * 
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 *  Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\taoSearch\factoryTest;

/**
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class QueryCriterionfactoryTest extends \oat\search\test\UnitTestHelper {
    
    public function setup() {
        $this->instance = $this->getMock('\\oat\\search\\factory\\QueryCriterionFactory', ['isValidClass' , 'getServiceLocator']);
    }
    
    public function testInvokeFactory() {
        
        $fixtureOptions = [
            'test',
            'equal',
            'toto', 
            false
        ];
        
        $testClassName  = '\\oat\\search\\QueryCriterion';
        
        $serviceManager =  $this->getMock('\\Zend\\ServiceManager\\ServiceManager');
        
        $mockTest       = $this->getMock('\\stdClass' , ['setName' , 'setOperator' , 'setValue' , 'setAndSeparator' , 'setServiceLocator']);
        
        $mockTest->expects($this->once())
                ->method('setName')
                ->with($fixtureOptions[0])
                ->willReturn($mockTest);
        $mockTest->expects($this->once())
                ->method('setOperator')
                ->with($fixtureOptions[1])
                ->willReturn($mockTest);
        $mockTest->expects($this->once())
                ->method('setValue')
                ->with($fixtureOptions[2])
                ->willReturn($mockTest);
        $mockTest->expects($this->once())
                ->method('setServiceLocator')
                ->with($serviceManager)
                ->willReturn($mockTest);

        $serviceManager->expects($this->once())
                ->method('get')
                ->with($testClassName)
                ->willReturn($mockTest);
        
        $this->instance->expects($this->exactly(2))->method('getServiceLocator')->willReturn($serviceManager);
        
        $this->instance->expects($this->once())->method('isValidClass')->with($mockTest)->willReturn(true);
        $this->assertEquals($mockTest , $this->instance->get($testClassName , $fixtureOptions));
    }
    
}
