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
use oat\search\test\UnitTestHelper;
/**
 * tests for AbstractSqlQuerySerialyser
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class AbstractSqlQuerySerialyserTest extends UnitTestHelper {
    
    public function testPrefixQuery() {
        
        $fixtureTable = 'test';
        $fixtureOptions = ['table' => $fixtureTable];
        
        $expectedQuery = 'SELECT * FROM `test` WHERE ';
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\DbSql\AbstractSqlQuerySerialyser',
                [], '',  true, true, true, 
                ['getOptions' , 'validateOptions' , 'setFieldList' , 'getDriverEscaper']
                );
        
        $DriverProphecy = $this->prophesize('oat\search\base\Query\EscaperInterface');

        $DriverProphecy->dbCommand('SELECT')->willReturn('SELECT')->shouldBeCalledTimes(1);
        $DriverProphecy->dbCommand('FROM')->willReturn('FROM')->shouldBeCalledTimes(1);
        $DriverProphecy->dbCommand('WHERE')->willReturn('WHERE')->shouldBeCalledTimes(1);
        
        $DriverProphecy->reserved($fixtureTable)->willReturn('`' .$fixtureTable. '`')->shouldBeCalledTimes(1);
        
        $DriverMock = $DriverProphecy->reveal();
        
        $this->instance->expects($this->once())->method('getOptions')->willReturn($fixtureOptions);
        $this->instance->expects($this->once())->method('validateOptions')->with($fixtureOptions)->willReturn(true);
        $this->instance->expects($this->any())->method('setFieldList')->with($fixtureOptions)->willReturn('*');
        $this->instance->expects($this->any())->method('getDriverEscaper')->willReturn($DriverMock);
        
        $this->assertSame($this->instance, $this->instance->prefixQuery());
        $this->assertSame($expectedQuery, $this->getInaccessibleProperty($this->instance, 'queryPrefix'));
    }
    
    public function testPrepareOperator() {
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\DbSql\AbstractSqlQuerySerialyser'
               );
        
        $fixtureQuery = 'SELECT * FROM `test` WHERE ';
        
        $this->setInaccessibleProperty($this->instance, 'query', $fixtureQuery);
        $this->setInaccessibleProperty($this->instance, 'operationSeparator', "\n");
        
        $this->assertSame($this->instance, $this->invokeProtectedMethod($this->instance,'prepareOperator'));
        $this->assertSame($fixtureQuery . "\n" , $this->getInaccessibleProperty($this->instance, 'query'));
    }
    
    public function testAddOperator() {
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\DbSql\AbstractSqlQuerySerialyser'
               );
        
        $fixtureQuery = 'SELECT * FROM `test` WHERE ';
        $expression   = '`text` like "test"';
        
        $this->setInaccessibleProperty($this->instance, 'query', $fixtureQuery);
        $this->assertSame($this->instance, $this->invokeProtectedMethod($this->instance,'addOperator' , [$expression]));
        $this->assertSame($fixtureQuery . $expression , $this->getInaccessibleProperty($this->instance, 'query'));
    }
    
    public function addSeparatorProvider() {
        return [
            [false , 'OR' , 1],
            [true , 'AND' , 2],
        ];
    }
    /**
     * @dataProvider addSeparatorProvider
     * @param boolean $operator
     * @param string $expectedString
     * @param integer $calls
     */
    public function testAddSeparator($operator , $expectedString , $calls) {
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\DbSql\AbstractSqlQuerySerialyser',
                [], '',  true, true, true, 
                ['getDriverEscaper']
                );
        
        $fixtureQuery = 'SELECT * FROM `test` WHERE ';
        $expression   = '`text` like "test"';
        
        $DriverProphecy = $this->prophesize('oat\search\base\Query\EscaperInterface');
        $DriverProphecy->dbCommand('OR')->willReturn('OR')->shouldBeCalledTimes(1);
        
        if($calls > 1) {
            $DriverProphecy->dbCommand('AND')->willReturn('AND')->shouldBeCalledTimes(1);
        }
        $DriverMock = $DriverProphecy->reveal();
        $this->instance->expects($this->any())->method('getDriverEscaper')->willReturn($DriverMock);
        $this->setInaccessibleProperty($this->instance, 'query', $fixtureQuery . $expression);
        $this->assertSame($this->instance, $this->invokeProtectedMethod($this->instance,'addSeparator' , [$operator]));
        $this->assertSame($fixtureQuery . $expression . ' ' . $expectedString . ' ' , $this->getInaccessibleProperty($this->instance, 'query'));
    }
    
    public function valiateOptionsProvider() {
        return [
            [[] , false , true],
            [['table' => 'test'] , true, false],
        ];
    }
    /**
     * @dataProvider valiateOptionsProvider
     * @param array $options
     * @param boolean $expected
     * @param boolean $exception
     */
    public function testValidateOptions(array $options , $expected, $exception) {
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\DbSql\AbstractSqlQuerySerialyser'
                );
        
        if($exception) {
            $this->setExpectedException('oat\search\base\exception\QueryParsingException' , 'table option is mandatory');
        }
        $this->assertSame($expected, $this->invokeProtectedMethod($this->instance,'validateOptions' , [$options]));
    }
    
    public function setFieldListProvider() {
        return 
        [
            [['text' , 'id' , 'create_date'] , "`text` , `id` , `create_date`" , ',', '*'],
            [[] , '*' , ',' , '*'],
        ];
    }
    
    /**
     * @dataProvider setFieldListProvider
     * @param array $list
     * @param string $expected
     * @param string $separator
     * @param string $allFields
     */
    public function testSetFieldList(array $list , $expected , $separator , $allFields) {
        
        $options = [];
        
        $DriverProphecy = $this->prophesize('oat\search\base\Query\EscaperInterface');
        $DriverProphecy->getAllFields()->willReturn($allFields)->shouldBeCalledTimes(1);
        
        if(count($list) > 0) {
            $options = [
                'fields' => $list,
            ];
            $DriverProphecy->getFieldsSeparator()->willReturn($separator)->shouldBeCalledTimes(1);
        }
        foreach ($list as $field) {
            $DriverProphecy->reserved($field)->willReturn('`' . $field . '`')->shouldBeCalledTimes(1);
        }
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\DbSql\AbstractSqlQuerySerialyser',
                [], '',  true, true, true, 
                ['getDriverEscaper']
                );
         
        $DriverMock = $DriverProphecy->reveal();
        $this->instance->expects($this->any())->method('getDriverEscaper')->willReturn($DriverMock);
        $this->assertSame($expected, $this->invokeProtectedMethod($this->instance,'setFieldList' , [$options]));
    }
    
    public function addLimitProvide() {
        return 
        [
            [0  , NULL , ''],
            [20 , NULL , 'LIMIT 20'],
            [30 , 0    , 'LIMIT 30 OFFSET 0'],
            [30 , 20   , 'LIMIT 30 OFFSET 20'],
        ];
    }
    /**
     * @dataProvider addLimitProvide
     * @param type $limit
     * @param type $offset
     * @param type $expected
     */
    public function testAddLimit($limit , $offset , $expected) {
        $DriverProphecy = $this->prophesize('oat\search\base\Query\EscaperInterface');
        
        if($limit > 0) {
            $DriverProphecy->dbCommand('LIMIT')->willReturn('LIMIT')->shouldBeCalledTimes(1);
        }
        if(!is_null($offset)) {
            $DriverProphecy->dbCommand('OFFSET')->willReturn('OFFSET')->shouldBeCalledTimes(1);
        }
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\DbSql\AbstractSqlQuerySerialyser',
                [], '',  true, true, true, 
                ['getDriverEscaper']
                );
         
        $DriverMock = $DriverProphecy->reveal();
        
        $this->instance->expects($this->any())->method('getDriverEscaper')->willReturn($DriverMock);
        $this->assertSame($expected, $this->invokeProtectedMethod($this->instance,'addLimit' , [$limit , $offset]));
    }
    
    public function addSortProvider() {
        return 
        [
            [['test' => 'asc'] , 'ORDER BY `test` ASC ' , false],
            [['test' => 'desc'] , 'ORDER BY `test` DESC ' , false],
            [['test' => 'asc' , 'id' => 'desc'] , 'ORDER BY `test` ASC , `id` DESC ' , false],
            [[] , '' , false],
            [['test'] , '' , true],
            [['id' => 'test'] , '' , true],
            
        ];
    }
    /**
     * @dataProvider addSortProvider
     * @param array $sortCriteria
     * @param string $expected
     * @param boolean $exception
     */
    public function testAddSort(array $sortCriteria , $expected, $exception) {
        
        if($exception) {
            $this->setExpectedException('\oat\search\base\exception\QueryParsingException');
        }
        
        $DriverProphecy = $this->prophesize('oat\search\base\Query\EscaperInterface');
        
        $DriverProphecy->dbCommand('ASC')->willReturn('ASC')->shouldBeCalledTimes(1);
        $DriverProphecy->dbCommand('DESC')->willReturn('DESC')->shouldBeCalledTimes(1);
        
        if (count($sortCriteria) > 0) {
            $DriverProphecy->dbCommand('ORDER BY')->willReturn('ORDER BY')->shouldBeCalledTimes(1);
           
            if(!$exception) {
                 $DriverProphecy->getFieldsSeparator()->willReturn(',')->shouldBeCalledTimes(1);
                foreach($sortCriteria as $field => $order) {

                        $DriverProphecy->reserved($field)->willReturn('`' . $field . '`')->shouldBeCalledTimes(1);

                }
            }
        }
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\DbSql\AbstractSqlQuerySerialyser',
                [], '',  true, true, true, 
                ['getDriverEscaper']
                );
         
        $DriverMock = $DriverProphecy->reveal();
        
        $this->instance->expects($this->any())->method('getDriverEscaper')->willReturn($DriverMock);
        $this->assertSame($expected, $this->invokeProtectedMethod($this->instance,'addSort' , [$sortCriteria]));
        
    }
    
    public function testFinishQuery() {
        
        $fixtureQuery        = 'SELECT * FROM `db_test` WHERE id > 100 ';
        
        $fixtureSortCriteria = ['id' => 'desc'];
        $fixtureLimit        = 1;
        $fixtureOffset       = 10;
        
        $fixtureSort         =  'ORDER BY `id` DESC ';
        $fixtureLimitOffset  =  'LIMIT ' . $fixtureLimit . ' OFFSET ' . $fixtureOffset;
        
        $expected = $fixtureQuery . $fixtureSort . ' ' . $fixtureLimitOffset . "\n";
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\DbSql\AbstractSqlQuerySerialyser',
                [], '',  true, true, true, 
                ['addSort' , 'addLimit']
                );
                
        $BuilderProphecy = $this->prophesize('oat\search\base\QueryBuilderInterface');
        
        $BuilderProphecy->getSort()->willReturn($fixtureSortCriteria)->shouldBeCalledTimes(1);
        $BuilderProphecy->getLimit()->willReturn($fixtureLimit)->shouldBeCalledTimes(1);
        $BuilderProphecy->getOffset()->willReturn($fixtureOffset)->shouldBeCalledTimes(1);
        
        $BuilderMock     = $BuilderProphecy->reveal();
        
        $this->setInaccessibleProperty($this->instance, 'criteriaList', $BuilderMock);
        $this->setInaccessibleProperty($this->instance, 'query', $fixtureQuery);
        $this->setInaccessibleProperty($this->instance, 'operationSeparator', "\n");
        $this->instance->expects($this->once())->method('addSort')->with($fixtureSortCriteria)->willReturn($fixtureSort);
        $this->instance->expects($this->once())->method('addLimit')->with($fixtureLimit , $fixtureOffset)->willReturn($fixtureLimitOffset);
        
        $this->assertSame($this->instance, $this->invokeProtectedMethod($this->instance,'finishQuery'));
        $this->assertSame($expected, $this->getInaccessibleProperty($this->instance, 'query'));
     }
    
}
