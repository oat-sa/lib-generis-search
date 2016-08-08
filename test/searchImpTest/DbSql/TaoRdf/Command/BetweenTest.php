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
 * test for Between
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class BetweenTest extends UnitTestHelper {
    
    public function testSetValuesList() {
        
        $fixtureValues = [0 , 10];
        
        $expected = '"0" AND "10"';
        
        $this->instance = $this->getMock('\oat\search\DbSql\TaoRdf\Command\Between' , ['getDriverEscaper'] );
        
        $DriverProphecy = $this->prophesize('oat\search\base\Query\EscaperInterface');
        $DriverProphecy->escape(0)->willReturn(0)->shouldBeCalledTimes(1);
        $DriverProphecy->escape(10)->willReturn(10)->shouldBeCalledTimes(1);
        $DriverProphecy->quote(0)->willReturn('"0"')->shouldBeCalledTimes(1);
        $DriverProphecy->quote(10)->willReturn('"10"')->shouldBeCalledTimes(1);
        $DriverProphecy->dbCommand('AND')->willReturn('AND')->shouldBeCalledTimes(1);
        
        $DriverMock     = $DriverProphecy->reveal();
        
        $this->instance->expects($this->any())->method('getDriverEscaper')->willReturn($DriverMock);
        
        $this->assertSame($expected, $this->invokeProtectedMethod($this->instance,'setValuesList' , [$fixtureValues]));
    }
    
    public function convertProvider() {
        return 
        [
            [
                'http://www.w3.org/2000/01/rdf-schema#label' , 
                '(`predicate` = "http://www.w3.org/2000/01/rdf-schema#label") AND',
                [0 , 5] ,
                '"0" AND "5"',
                '(`predicate` = "http://www.w3.org/2000/01/rdf-schema#label") AND `object` BETWEEN "0" AND "5" ', 
                false,
            ],
            [
               'http://www.w3.org/2000/01/rdf-schema#label' , 
                '(`predicate` = "http://www.w3.org/2000/01/rdf-schema#label") AND',
                'test',
                '',
                null, 
                true, 
            ],
        ];
    }
    
    /**
     * @dataProvider convertProvider
     * @param string $predicate
     * @param string $predicateQuery
     * @param mixed $value
     * @param string $valueList
     * @param string $expected
     * @param boolean $exception
     */
    public function testConvert($predicate , $predicateQuery , $value , $valueList , $expected , $exception) {
        
        $fixtureOperator  = 'BETWEEN';
        
        $this->instance = $this->getMock('\oat\search\DbSql\TaoRdf\Command\Between' , 
                ['getDriverEscaper' , 'setPropertyName' , 'getOperator' , 'setValuesList']);
        
        $QueryCriterionProphecy = $this->prophesize('\oat\search\base\QueryCriterionInterface');
        $DriverProphecy = $this->prophesize('oat\search\base\Query\EscaperInterface');
        
        $QueryCriterionProphecy->getValue()->willReturn($value);
        
        if($exception) {
          $this->setExpectedException('\oat\search\base\exception\QueryParsingException');  
        } else {
            
            $QueryCriterionProphecy->getName()->willReturn($predicate);

            $DriverProphecy->reserved('object')->willReturn('`object`')->shouldBeCalledTimes(1);

            $DriverMock     = $DriverProphecy->reveal();
            
            $this->instance->expects($this->any())->method('getDriverEscaper')->willReturn($DriverMock);
            $this->instance->expects($this->once())->method('setPropertyName')->with($predicate)->willReturn($predicateQuery);
            $this->instance->expects($this->any())->method('getOperator')->willReturn($fixtureOperator);
            $this->instance->expects($this->once())->method('setValuesList')->with($value)->willReturn($valueList);
            
        }
        
        $QueryCriterionMock = $QueryCriterionProphecy->reveal();

        $this->assertSame($expected, $this->instance->convert($QueryCriterionMock));
    }
}
