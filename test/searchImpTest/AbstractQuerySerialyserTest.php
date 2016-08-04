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

namespace oat\search\test\searchImpTest;

use oat\search\test\UnitTestHelper;

/**
 * test AbstractQuerySerialyserTest
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class AbstractQuerySerialyserTest extends UnitTestHelper {
    
    protected $instance;


    public function testSetCriteriaList() {
        
        $instance = $this->getMockForAbstractClass('oat\search\AbstractQuerySerialyser');
        
        $builderProphecy = $this->prophesize('oat\search\QueryBuilder');
        $builder         = $builderProphecy->reveal();
        
        $this->assertSame($instance, $instance->setCriteriaList($builder));
        $this->assertSame($builder, $this->getInaccessibleProperty($instance, 'criteriaList'));
    }
    
    public function testSerialyse() {
        
        $instance = $this->getMockForAbstractClass(
                'oat\search\AbstractQuerySerialyser',
                [], '',  true, true, true, 
                ['parseQuery' , 'finishQuery']
                );
        
        $fixturePrefix = 'SELECT FROM .....';
        
        $QueryProphecy = $this->prophesize('oat\search\Query');
        
        $MockQuery = $QueryProphecy->reveal();
        
        $builderProphecy = $this->prophesize('oat\search\QueryBuilder');
        
        $storedQueries = [$MockQuery];
        
        $builderProphecy->getStoredQueries()->willReturn($storedQueries)->shouldBeCalledTimes(1);
        
        $builder         = $builderProphecy->reveal();
        
        $instance->expects($this->once())->method('parseQuery')->with($MockQuery)->willReturn($instance);
        
        $instance->expects($this->once())->method('finishQuery')->willReturn($instance);
        
        $this->setInaccessibleProperty($instance, 'queryPrefix', $fixturePrefix);
        $this->setInaccessibleProperty($instance, 'criteriaList', $builder);
        
        $returnQuery = $instance->serialyse();
        
        $this->assertSame($returnQuery, $this->getInaccessibleProperty($instance, 'query'));
        $this->assertContains($fixturePrefix, $returnQuery);
    }
    
    public function testParseQuery() {
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\AbstractQuerySerialyser',
                [], '',  true, true, true, 
                ['parseOperation']
                );
        
        $ParamProphecy = $this->prophesize('oat\search\QueryCriterion');
        $mockParam     = $ParamProphecy->reveal();
        $storedParams = [$mockParam];
        
        $QueryProphecy = $this->prophesize('oat\search\Query');
        
        $QueryProphecy->getStoredQueryCriteria()->willReturn($storedParams)->shouldBeCalledTimes(1);
        $MockQuery = $QueryProphecy->reveal();
        
        $this->instance->expects($this->once())->method('parseOperation')->with($mockParam)->willReturn($this->instance);
        
        $this->assertSame($this->instance , $this->invokeProtectedMethod($this->instance , 'parseQuery' , [$MockQuery]));
    }
    
    public function testSetNextSeparator() {
        
        $fixtureAnd = true;
        $fixtureOr  = false;
        
         $this->instance = $this->getMockForAbstractClass(
                'oat\search\AbstractQuerySerialyser',
                [], '',  true, true, true, 
                ['addSeparator']
                );
        $this->instance->expects($this->once())->method('addSeparator')->with($fixtureAnd)->willReturn($this->instance);
        
        $this->assertSame($this->instance, $this->invokeProtectedMethod($this->instance ,'setNextSeparator' , [$fixtureAnd]));
        $this->assertSame($fixtureAnd, $this->getInaccessibleProperty($this->instance, 'nextSeparator'));
        
        $this->assertSame($this->instance, $this->invokeProtectedMethod($this->instance ,'setNextSeparator' , [$fixtureOr]));
        $this->assertSame($fixtureOr, $this->getInaccessibleProperty($this->instance, 'nextSeparator'));
    }
    
    public function testSetConditions() {
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\AbstractQuerySerialyser',
                [], '',  true, true, true, 
                ['getOperator' , 'mergeCondition']
                );
        
        $operator = 'equal';
        
        $convertOperation = 'test = "toto"';
        
        $ParamProphecy = $this->prophesize('oat\search\QueryCriterion');
        
        $ParamProphecy->getOperator()->willReturn($operator)->shouldBeCalledTimes(1);
        $mockParam     = $ParamProphecy->reveal();
        
        $OperatorProphecy = $this->prophesize('oat\search\base\command\OperatorConverterInterface');
        
        $OperatorProphecy->convert($mockParam)->willReturn($convertOperation)->shouldBeCalledTimes(1);
        
        $OperatorMock = $OperatorProphecy->reveal();
        
        $fixtureCommand       = 'name = toto';
        $fixtureConditionList = [$mockParam];
        $fixtureSeparator     = 'and';
        
        $this->instance->expects($this->once())
                ->method('getOperator')
                ->willReturn($OperatorMock);
        
        $this->instance->expects($this->once())
                ->method('mergeCondition')
                ->with($fixtureCommand , $convertOperation , $fixtureSeparator)
                ->willReturn($fixtureCommand);
        
        $this->assertSame($fixtureCommand , $this->invokeProtectedMethod($this->instance ,'setConditions' , [&$fixtureCommand , $fixtureConditionList , $fixtureSeparator]));
    }
    
    public function testGetOperator() {
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\AbstractQuerySerialyser',
                [], '',  true, true, true, 
                ['getServiceLocator' , 'getDriverEscaper']
                );
        
        $fixtureOperator  = 'contain';
        $fixtureNameSpace = 'MySQL';
        $fixtureClass     = 'Contain';
        $fixtureSupportedOperator = [
            $fixtureOperator =>  $fixtureClass
        ];
        
        $driverEscaperProphecy = $this->prophesize('oat\search\base\Query\EscaperInterface');
        $mockDriverEscaper = $driverEscaperProphecy->reveal();
        
        $OperatorProphecy  = $this->prophesize('oat\search\base\command\OperatorConverterInterface');
        $OperatorProphecy->setDriverEscaper($mockDriverEscaper)->willReturn($OperatorProphecy)->shouldBeCalledTimes(1);
        
        $mockOperator = $OperatorProphecy->reveal();
        
        $ServiceLocatorProphecy = $this->prophesize('\Zend\ServiceManager\ServiceManager');
        $ServiceLocatorProphecy->get($fixtureNameSpace . '\\' . $fixtureClass)->willReturn($mockOperator)->shouldBeCalledTimes(1);
        $mockServiceLocator = $ServiceLocatorProphecy->reveal();
        
        $this->instance->expects($this->once())->method('getDriverEscaper')->willReturn($mockDriverEscaper);
        $this->instance->expects($this->once())->method('getServiceLocator')->willReturn($mockServiceLocator);
        
        $this->setInaccessibleProperty($this->instance, 'supportedOperators', $fixtureSupportedOperator);
        $this->setInaccessibleProperty($this->instance, 'operatorNameSpace', $fixtureNameSpace);
        
        $this->assertSame($mockOperator, $this->invokeProtectedMethod($this->instance,'getOperator' , [$fixtureOperator]));
    }
    
    public function testGetOperatorFailed() {
    
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\AbstractQuerySerialyser'
                );
        
        $fixtureOperator  = 'contain';
        $this->setExpectedException('\oat\search\base\exception\QueryParsingException');
        $this->setInaccessibleProperty($this->instance, 'supportedOperators', []);
        $this->invokeProtectedMethod($this->instance,'getOperator' , [$fixtureOperator]);
    }
    
    public function testParseOperation() {
        
        $convertOperation = 'convertOperation';
        $fixtureOperator  = 'equal';
        $fixtureValue     = '666';
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\AbstractQuerySerialyser',
                [], '',  true, true, true, 
                ['getOperator' , 'setNextSeparator' , 'prepareOperator' , 'setConditions' , 'getOperationValue']
                );
        
        $ParamProphecy = $this->prophesize('oat\search\QueryCriterion');
        $ParamProphecy->getValue()->willReturn($fixtureValue)->shouldBeCalledTimes(1);
        $ParamProphecy->setValue($fixtureValue)->willReturn($ParamProphecy)->shouldBeCalledTimes(1);
        $ParamProphecy->getOperator()->willReturn($fixtureOperator)->shouldBeCalledTimes(1);
        $ParamProphecy->getAnd()->willReturn([])->shouldBeCalledTimes(1);
        $ParamProphecy->getOr()->willReturn([])->shouldBeCalledTimes(1);
        
        $mockParam = $ParamProphecy->reveal();
        
        $OperatorProphecy = $this->prophesize('oat\search\base\command\OperatorConverterInterface');
        $OperatorProphecy->convert($mockParam)->willReturn($convertOperation)->shouldBeCalledTimes(1); 
        $OperatorMock = $OperatorProphecy->reveal();
        
        $this->instance->expects($this->once())->method('prepareOperator')->willReturn($this->instance);
        $this->instance->expects($this->once())->method('getOperator')->with($fixtureOperator)->willReturn($OperatorMock);
        $this->instance->expects($this->once())->method('addOperator')->with($convertOperation)->willReturn($this->instance);
        $this->instance->expects($this->once())->method('getOperationValue')->with($fixtureValue)->willReturn($fixtureValue);
        
        $this->instance->expects($this->exactly(2))->method('setConditions')
                ->withConsecutive(
                        [$convertOperation , [], 'and'],
                        [$convertOperation , [], 'or']
                        )
                ->willReturnOnConsecutiveCalls($this->instance , $this->instance);
        
        $this->assertSame($this->instance, $this->invokeProtectedMethod($this->instance,'parseOperation' , [$mockParam]));
    }
    
    public function testGetOperationValueQuery() {
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\AbstractQuerySerialyser',
                [], '',  true, true, true, 
                ['parseQuery' ]
                );
        $fixtureValue = 'select * from `test`';
        
        $QueryProphecy = $this->prophesize('oat\search\Query');
        
        $MockQuery = $QueryProphecy->reveal();
        
        $this->instance->expects($this->once())->method('parseQuery')->with($MockQuery)->willReturn($fixtureValue);
        $this->assertSame($fixtureValue, $this->invokeProtectedMethod($this->instance,'getOperationValue' , [$MockQuery]));
    }
    
    public function testGetOperationValueBuilder() {
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\AbstractQuerySerialyser',
                [], '',  true, true, true, 
                ['setCriteriaList' , 'parse' , ]
                );
        $fixtureValue = 'select * from `test`';
        
        $BuilderProphecy = $this->prophesize('oat\search\QueryBuilder');
        
        $MockBuilder = $BuilderProphecy->reveal();
        
        $this->instance->expects($this->once())->method('setCriteriaList')->with($MockBuilder)->willReturn($this->instance);
        $this->instance->expects($this->once())->method('parse')->willReturn($fixtureValue);
        
        $this->assertSame($fixtureValue, $this->invokeProtectedMethod($this->instance,'getOperationValue' , [$MockBuilder]));
    }
    
    public function testGetOperationValueString() {
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\AbstractQuerySerialyser'
                );
        $fixtureValue = 'toto';
        
        $this->assertSame($fixtureValue, $this->invokeProtectedMethod($this->instance,'getOperationValue' , [$fixtureValue]));
    }
    
}
