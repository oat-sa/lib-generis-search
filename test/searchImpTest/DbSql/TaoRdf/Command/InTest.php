<?php
/**
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
 *  Copyright (c) 2016-2019 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\search\test\searchImpTest\DbSql\TaoRdf\Command;

use oat\search\test\UnitTestHelper;

/**
 * test for In
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class InTest extends UnitTestHelper
{
    public function testSetValuesList()
    {
        $fixtureValues = [0 , 5 , 10];
        
        $expected = '("0" , "5" , "10")';

        $this->instance = $this->getMockBuilder('\oat\search\DbSql\TaoRdf\Command\In')
            ->setMethods(['getDriverEscaper'])
            ->getMock();
        
        $DriverProphecy = $this->prophesize('oat\search\base\Query\EscaperInterface');
        $DriverProphecy->escape(0)->willReturn(0)->shouldBeCalledTimes(1);
        $DriverProphecy->escape(5)->willReturn(5)->shouldBeCalledTimes(1);
        $DriverProphecy->escape(10)->willReturn(10)->shouldBeCalledTimes(1);
        $DriverProphecy->quote(0)->willReturn('"0"')->shouldBeCalledTimes(1);
        $DriverProphecy->quote(5)->willReturn('"5"')->shouldBeCalledTimes(1);
        $DriverProphecy->quote(10)->willReturn('"10"')->shouldBeCalledTimes(1);
        $DriverProphecy->getFieldsSeparator()->willReturn(',')->shouldBeCalledTimes(1);
        
        $DriverMock     = $DriverProphecy->reveal();
        
        $this->instance->expects($this->any())->method('getDriverEscaper')->willReturn($DriverMock);
        
        $this->assertSame($expected, $this->invokeProtectedMethod($this->instance,'setValuesList' , [$fixtureValues]));
    }
    
    public function convertProvider()
    {
        return
            [
                [
                    [0 , 5 , 10],
                    [0 , 5 , 10],
                    '("0" , "5" , "10")',
                ],
                [
                    'test',
                    ['test'],
                    '("test")',
                ],
            ];
    }
    
    /**
     * @dataProvider convertProvider
     * @param mixed $value
     * @param array $listToEscape
     * @param string $valueList
     */
    public function testConvert($value, $listToEscape, $valueList)
    {
        $predicate = 'http://www.w3.org/2000/01/rdf-schema#label';
        $predicateQuery = '(`predicate` = "http://www.w3.org/2000/01/rdf-schema#label") AND';
        $fixtureOperator  = 'IN';
        $object = '`object`';

        $this->instance = $this->getMockBuilder('\oat\search\DbSql\TaoRdf\Command\In')
            ->setMethods(['getDriverEscaper', 'setPropertyName', 'getOperator', 'setValuesList'])
            ->getMock();
        
        $QueryCriterionProphecy = $this->prophesize('\oat\search\base\QueryCriterionInterface');
        $QueryCriterionProphecy->getValue()->willReturn($value);
        $QueryCriterionProphecy->getName()->willReturn($predicate);
        $QueryCriterionMock = $QueryCriterionProphecy->reveal();

        $DriverProphecy = $this->prophesize('oat\search\base\Query\EscaperInterface');
        $DriverProphecy->reserved('object')->willReturn('`object`')->shouldBeCalledTimes(1);
        $DriverMock = $DriverProphecy->reveal();

        $this->instance->method('getDriverEscaper')->willReturn($DriverMock);
        $this->instance->method('setPropertyName')->with($predicate)->willReturn($predicateQuery);
        $this->instance->method('getOperator')->willReturn($fixtureOperator);
        $this->instance->method('setValuesList')->with($listToEscape)->willReturn($valueList);

        $expected = $predicateQuery . ' ' . $object . ' ' . $fixtureOperator . ' ' . $valueList . ' ';

        $query = $this->instance->convert($QueryCriterionMock);
        $this->assertSame($expected, $query);
    }
}
