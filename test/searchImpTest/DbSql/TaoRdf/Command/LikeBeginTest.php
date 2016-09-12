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
 * test for LikeBegin
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class LikeBeginTest extends UnitTestHelper {
    
    public function testConvert() {
        
        $fixturePredicate = 'http://www.w3.org/2000/01/rdf-schema#label';
        $fixtureValue     = 'test';
        $fixtureOperator  = 'LIKE';
        
        $fixtureProperty = '(`predicate` = "' . $fixturePredicate . '") AND';
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\DbSql\TaoRdf\Command\LikeBegin',
                [], '',  true, true, true, 
                ['getDriverEscaper' , 'setPropertyName' , 'getOperator']
        );
        
        $expected = '' . $fixtureProperty . ' `object` LIKE "test%"';
        
        $QueryCriterionProphecy = $this->prophesize('\oat\search\base\QueryCriterionInterface');
        
        $QueryCriterionProphecy->getValue()->willReturn($fixtureValue);
        $QueryCriterionProphecy->getName()->willReturn($fixturePredicate);
        
        $QueryCriterionMock = $QueryCriterionProphecy->reveal();
        
        $DriverProphecy = $this->prophesize('oat\search\base\Query\EscaperInterface');
        
        $DriverProphecy->escape($fixtureValue . '%')->willReturn($fixtureValue . '%')->shouldBeCalledTimes(1);
        $DriverProphecy->quote($fixtureValue . '%')->willReturn('"' . $fixtureValue . '%"')->shouldBeCalledTimes(1);
        $DriverProphecy->reserved('object')->willReturn('`object`')->shouldBeCalledTimes(1);
        
        $DriverMock     = $DriverProphecy->reveal();
        
        $this->instance->expects($this->any())->method('getDriverEscaper')->willReturn($DriverMock);
        $this->instance->expects($this->once())->method('setPropertyName')->with($fixturePredicate)->willReturn($fixtureProperty);
        $this->instance->expects($this->any())->method('getOperator')->willReturn($fixtureOperator);
        
        $this->setInaccessibleProperty($this->instance, 'operator', $fixtureOperator);
        $this->assertSame($expected, $this->instance->convert($QueryCriterionMock));
    }
}
