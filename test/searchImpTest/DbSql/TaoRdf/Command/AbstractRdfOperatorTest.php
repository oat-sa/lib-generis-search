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

namespace oat\search\test\searchImpTest\DbSql\TaoRdf\Command;

use oat\search\test\UnitTestHelper;

/**
 * Description of AbstractRdfOperatorTest
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class AbstractRdfOperatorTest extends UnitTestHelper {
    
    public function testGetOperator() {
        
        $operator = '=';
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\DbSql\TaoRdf\Command\AbstractRdfOperator',
                [], '',  true, true, true, 
                ['getDriverEscaper']
                );
        
        $DriverProphecy = $this->prophesize('oat\search\base\Query\EscaperInterface');
        $DriverProphecy->dbCommand($operator)->willReturn($operator)->shouldBeCalledTimes(1);
        
        $DriverMock     = $DriverProphecy->reveal();
        
        $this->instance->expects($this->once())->method('getDriverEscaper')->willReturn($DriverMock);
        $this->setInaccessibleProperty($this->instance, 'operator', $operator);
        
        $this->assertSame($operator, $this->invokeProtectedMethod($this->instance,'getOperator'));
    }
    
    public function testSetPropertyName() {
        
        $fixtureName = 'http://www.w3.org/2000/01/rdf-schema#label';
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\DbSql\TaoRdf\Command\AbstractRdfOperator',
                [], '',  true, true, true, 
                ['getDriverEscaper']
                );
        
        $expected = '`predicate` = "' . $fixtureName . '" AND ( ';
        
        $DriverProphecy = $this->prophesize('oat\search\base\Query\EscaperInterface');
        $DriverProphecy->escape($fixtureName)->willReturn($fixtureName)->shouldBeCalledTimes(1);
        $DriverProphecy->quote($fixtureName)->willReturn('"' . $fixtureName . '"')->shouldBeCalledTimes(1);
        
        $DriverProphecy->reserved('predicate')->willReturn('`predicate`')->shouldBeCalledTimes(1);
        $DriverProphecy->dbCommand('AND')->willReturn('AND')->shouldBeCalledTimes(1);
        
        $DriverMock     = $DriverProphecy->reveal();
        
        $this->instance->expects($this->any())->method('getDriverEscaper')->willReturn($DriverMock);
        
        $this->assertSame($expected, $this->invokeProtectedMethod($this->instance,'setPropertyName' , [$fixtureName]));
    }
    
    public function testConvert() {
        
        $fixturePredicate = 'http://www.w3.org/2000/01/rdf-schema#label';
        $fixtureValue     = 'test';
        $fixtureOperator  = '=';
        
        $fixtureProperty = '(`predicate` = "' . $fixturePredicate . '") AND';
        
        $this->instance = $this->getMock(
                'oat\search\DbSql\TaoRdf\Command\AbstractRdfOperator',
                ['getDriverEscaper' , 'setPropertyName' , 'getOperator']
        );
        
        $expected = '' . $fixtureProperty . ' `object` = "test"';
        
        $QueryCriterionProphecy = $this->prophesize('\oat\search\base\QueryCriterionInterface');
        
        $QueryCriterionProphecy->getValue()->willReturn($fixtureValue);
        $QueryCriterionProphecy->getName()->willReturn($fixturePredicate);
        
        $QueryCriterionMock = $QueryCriterionProphecy->reveal();
        
        $DriverProphecy = $this->prophesize('oat\search\base\Query\EscaperInterface');
        
        $DriverProphecy->escape($fixtureValue)->willReturn($fixtureValue)->shouldBeCalledTimes(1);
        $DriverProphecy->quote($fixtureValue)->willReturn('"' . $fixtureValue . '"')->shouldBeCalledTimes(1);
        $DriverProphecy->reserved('object')->willReturn('`object`')->shouldBeCalledTimes(1);
        
        $DriverMock     = $DriverProphecy->reveal();
        
        $this->instance->expects($this->any())->method('getDriverEscaper')->willReturn($DriverMock);
        $this->instance->expects($this->once())->method('setPropertyName')->with($fixturePredicate)->willReturn($fixtureProperty);
        $this->instance->expects($this->any())->method('getOperator')->willReturn($fixtureOperator);
        
        $this->setInaccessibleProperty($this->instance, 'operator', $fixtureOperator);
        $this->assertSame($expected, $this->instance->convert($QueryCriterionMock));
    }
    
}
