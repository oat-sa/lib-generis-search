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

/**
 * Description of QuerySerialyserTest
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class QuerySerialyserTest extends oat\search\test\UnitTestHelper {
    
    public function setUp() {
        $this->instance = new \oat\search\DbSql\TaoRdf\QuerySerialyser();
    }
    
    public function testPrefixQuery() {
        
        $fixtureTable = 'test';
        $fixtureLanguage = 'fr-FR';
        $fixtureOptions  = [
            'table'    => $fixtureTable,
            'language' => $fixtureLanguage,
        ];
        
        $fixtureQuery = 'SELECT * FROM `test` WHERE ';
        
        $instance = $this->getMock(
                'oat\search\DbSql\TaoRdf\QuerySerialyser',
                ['getOptions' , 'validateOptions' , 'setFieldList' , 'getDriverEscaper' , 'initQuery' , 'setLanguageCondition']
                );
        
        $instance->expects($this->once())->method('getOptions')->willReturn($fixtureOptions);
        $instance->expects($this->once())->method('validateOptions')->with($fixtureOptions)->willReturn(true);
        $instance->expects($this->once())->method('initQuery')->with()->willReturn($fixtureQuery);
        $instance->expects($this->once())
                ->method('setLanguageCondition')
                ->with($fixtureLanguage , true)
                ->willReturn(
                        '(`l_language` = "fr-FR" OR `l_language` = "")'
                        );
        
        $this->assertSame($instance, $instance->prefixQuery());
        $this->assertSame($fixtureQuery, $this->getInaccessibleProperty($instance, 'queryPrefix'));
    }

    public function testInitQuery() {
        $fixtureTable = 'test';
        $fixtureLanguage = 'es-ES';
        
        $fixtureOptions  = [
            'table'    => $fixtureTable,
            'language' => $fixtureLanguage,
        ];
        
        $expectedQuery = 'SELECT DISTINCT(`subject`)  FROM `test`  WHERE `subject` IN (SELECT `subject` FROM  (SELECT DISTINCT(`subject`)  FROM `test` WHERE ';
        
        $DriverProphecy = $this->prophesize('oat\search\base\Query\EscaperInterface');
        
        $DriverProphecy->dbCommand('SELECT') ->willReturn('SELECT')->shouldBeCalledTimes(3);  
        $DriverProphecy->dbCommand('FROM')->willReturn('FROM')->shouldBeCalledTimes(3);    
        $DriverProphecy->reserved($fixtureTable)->willReturn('`'.$fixtureTable.'`')->shouldBeCalledTimes(2);           
        $DriverProphecy->dbCommand('WHERE')->willReturn('WHERE')->shouldBeCalledTimes(2); 
        $DriverProphecy->dbCommand('IN')->willReturn('IN')->shouldBeCalledTimes(1);
        $DriverProphecy->reserved('subject')->willReturn('`subject`')->shouldBeCalledTimes(4);    
        $DriverProphecy->dbCommand('DISTINCT')->willReturn('DISTINCT')->shouldBeCalledTimes(2);
        
        $DriverMock = $DriverProphecy->reveal();
        
        $this->setInaccessibleProperty($this->instance, 'operationSeparator', " ");
        $this->setInaccessibleProperty($this->instance, 'options', $fixtureOptions);
        $this->setInaccessibleProperty($this->instance, 'driverEscaper', $DriverMock);
        $this->assertSame($expectedQuery, $this->invokeProtectedMethod($this->instance,'initQuery'));
    }
    
    public function setLanguageConditionProvider() {
        return 
        [
            ['fr-FR' , true ,  '(`l_language` = fr-FR OR `l_language` = "") AND' . "\n"],
            ['en-US' , false , '(`l_language` = en-US) AND'. "\n"]
        ];
    }
    
    /**
     * @dataProvider setLanguageConditionProvider
     * @param string $language
     * @param string $emptyAvailable
     * @param string $expected
     */
    public function testSetLanguageCondition($language , $emptyAvailable , $expected) {
        $DriverProphecy = $this->prophesize('oat\search\base\Query\EscaperInterface');
        
        $DriverProphecy->reserved('l_language')->willreturn('`l_language`')->shouldBeCalledTimes(1);
        $DriverProphecy->escape($language)->willreturn($language)->shouldBeCalledTimes(1);
        $DriverProphecy->quote($language)->willreturn($language)->shouldBeCalledTimes(1);
        $DriverProphecy->dbCommand('AND')->willreturn('AND')->shouldBeCalledTimes(1);
        
        if($emptyAvailable) {
            $DriverProphecy->dbCommand('OR')->willreturn('OR')->shouldBeCalledTimes(1);
            $DriverProphecy->getEmpty()->willreturn('""')->shouldBeCalledTimes(1);
        }
        
        $DriverMock = $DriverProphecy->reveal();
        
        $this->setInaccessibleProperty($this->instance, 'operationSeparator', "\n");
        $this->setInaccessibleProperty($this->instance, 'driverEscaper', $DriverMock);
        
        $this->assertSame($expected, $this->invokeProtectedMethod($this->instance,'setLanguageCondition' , [$language , $emptyAvailable]));
    }
    
    public function testAddOperator() {
        $prefix = '( `subject` IN' . "\n" . '(SELECT DISTINCT `subject` FROM `statements` WHERE';
        $fixtureExpression = '(`predicate` = "http://www.w3.org/2000/01/rdf-schema#label") AND `object` = "test")';

        $language = '(`l_language` = fr-FR OR `l_language` = "") AND ';
        $expected = $prefix . "\n" . $language . "\n" . $fixtureExpression . '))';
        
        $DriverProphecy = $this->prophesize('oat\search\base\Query\EscaperInterface');
        
        $DriverProphecy->reserved('subject')->willReturn('`subject`')->shouldBeCalledTimes(2);
        $DriverProphecy->reserved('statements')->willReturn('`statements`')->shouldBeCalledTimes(1);
        $DriverProphecy->dbCommand('SELECT')->willReturn('SELECT')->shouldBeCalledTimes(1);
        $DriverProphecy->dbCommand('FROM')->willReturn('FROM')->shouldBeCalledTimes(1);
        $DriverProphecy->dbCommand('WHERE')->willReturn('WHERE')->shouldBeCalledTimes(1);
        $DriverProphecy->dbCommand('DISTINCT')->willReturn('DISTINCT')->shouldBeCalledTimes(1);
        $DriverProphecy->dbCommand('IN')->willReturn('IN')->shouldBeCalledTimes(1);
        
        $DriverMock = $DriverProphecy->reveal();
        
        $this->setInaccessibleProperty($this->instance, 'language', $language);
        $this->setInaccessibleProperty($this->instance, 'operationSeparator', "\n");
        $this->setInaccessibleProperty($this->instance, 'options', ['table' => 'statements']);
        $this->setInaccessibleProperty($this->instance, 'driverEscaper', $DriverMock);
        
        $this->assertSame($this->instance, $this->invokeProtectedMethod($this->instance,'addOperator' , [$fixtureExpression]));
        
        $this->assertSame($expected , $this->getInaccessibleProperty($this->instance, 'query'));
    }
    
    public function mergeConditionProvider() {
        return  
        [
            [
                'SELECT * FROM `table` WHERE id IN ("1","2","3","4")',
                '`text` LIKE "toto"',
                'and',
                'SELECT * FROM `table` WHERE id IN ("1","2","3","4") AND `text` LIKE "toto"' . "\n"
            ],
            [
                'SELECT * FROM `table` WHERE id IN ("1","2","3","4")',
                '`text` LIKE "toto"',
                'or',
                'SELECT * FROM `table` WHERE id IN ("1","2","3","4") OR `text` LIKE "toto"'. "\n"
            ],
            [
                'SELECT * FROM `table` WHERE',
                '`text` LIKE "toto"',
                null,
                'SELECT * FROM `table` WHERE `text` LIKE "toto"'. "\n"
            ],
        ];
        
    }
    
    /**
     * @dataProvider mergeConditionProvider
     * @param string $command
     * @param string $condition
     * @param string|null $separator
     * @param string $expected
     */
    public function testMergeCondition($command , $condition, $separator , $expected) {
        $DriverProphecy = $this->prophesize('oat\search\base\Query\EscaperInterface');
        if(!is_null($separator)) {
            $DriverProphecy->dbCommand($separator)->willReturn(strtoupper($separator))->shouldBeCalledTimes(1);
        }
        $DriverMock = $DriverProphecy->reveal();
        
        $this->setInaccessibleProperty($this->instance, 'operationSeparator', "\n");
        $this->setInaccessibleProperty($this->instance, 'driverEscaper', $DriverMock);
        
        $arguments = [&$command , $condition, $separator];
        $this->assertSame($this->instance, $this->invokeProtectedMethod($this->instance,'mergeCondition' , $arguments));
        $this->assertSame($expected , $arguments[0]);
    }
    
    public function testFinishQuery() {
        
        $fixtureSort   = ['date' => 'asc'];
        $fixtureLimit  = 20;
        $fixtureOffset = 10;
        
        $limitString   = 'LIMIT 20 OFFSET 10';
        $sortString    = 'ORDER BY `date` ASC';
        
        $expected      = ' LIMIT 20 OFFSET 10 ) '. "\n" .
                         'AS subQuery ) ' . "\n" .
                         'ORDER BY `date` ASC';
        
        $this->instance = $this->getMock(
                'oat\search\DbSql\TaoRdf\QuerySerialyser',
                ['addLimit' , 'addSort' , 'getDriverEscaper']
                );
        $DriverProphecy = $this->prophesize('oat\search\base\Query\EscaperInterface');

        $DriverProphecy->dbCommand('AS')->willReturn(strtoupper('AS'))->shouldBeCalledTimes(1);
        
        $DriverMock = $DriverProphecy->reveal();
        
        $BuilderProphecy = $this->prophesize('oat\search\base\QueryBuilderInterface');
        
        $BuilderProphecy->getLimit()->willReturn($fixtureLimit)->shouldBeCalledTimes(1);
        $BuilderProphecy->getOffset()->willReturn($fixtureOffset)->shouldBeCalledTimes(1);
        $BuilderProphecy->getSort()->willReturn($fixtureSort)->shouldBeCalledTimes(1);
        
        $BuilderMock     = $BuilderProphecy->reveal();
        $this->instance->expects($this->once())->method('getDriverEscaper')->willReturn($DriverMock);
        $this->instance->expects($this->once())->method('addLimit')
                ->with($fixtureLimit , $fixtureOffset)
                ->willReturn($limitString);
        $this->instance->expects($this->once())->method('addSort')
                ->with($fixtureSort)
                ->willReturn($sortString);
        
        $this->setInaccessibleProperty($this->instance, 'operationSeparator', "\n");
        $this->setInaccessibleProperty($this->instance, 'criteriaList', $BuilderMock);
        
        $this->assertSame($this->instance, $this->invokeProtectedMethod($this->instance,'finishQuery' ));
        $this->assertSame($expected , $this->getInaccessibleProperty($this->instance, 'query'));
    }

    public function tearDown() {
        $this->instance = null;
    }
    
}
