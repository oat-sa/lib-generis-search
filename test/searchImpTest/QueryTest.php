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

use oat\search\factory\QueryFactory;
use oat\search\Query;
use oat\search\QueryCriterion;
use oat\search\test\UnitTestHelper;

/**
 * Description of Querytest
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class QueryTest extends UnitTestHelper {
    
    /**
     *
     * @var Query
     */
    protected $instance;
    
    public function setUp() {
        $this->instance = new Query();
    }
    
    public function testSetQueryCriterionClassName() {
        $fixtureClassName = 'QueryCriterion';
        $this->assertSame($this->instance, $this->instance->setQueryCriterionClassName($fixtureClassName));
        $this->assertSame($fixtureClassName, $this->getInaccessibleProperty($this->instance , 'queryParamClassName'));
        
    }

    public function testSetQueryCriterionFactory() {
        
        $Factory = new QueryFactory;
        
        $this->assertSame($this->instance , $this->instance->setQueryCriterionFactory($Factory));
        $this->assertSame($Factory , $this->getInaccessibleProperty($this->instance , 'factory'));
        
    }
    
    public function testGetStoredQueryCriterions() {
        
        $fixtureStoredQueries = [
            new QueryCriterion(),
            new QueryCriterion(),
            new QueryCriterion(),
            new QueryCriterion(),
        ];
        
        $this->setInaccessibleProperty($this->instance , 'storedQueryCriterions', $fixtureStoredQueries);
        $this->assertSame($fixtureStoredQueries, $this->instance->getStoredQueryCriterions());
        
    }
    
    public function testReset() {
        $fixtureStoredQueries = [
            new QueryCriterion(),
            new QueryCriterion(),
            new QueryCriterion(),
            new QueryCriterion(),
        ];
        
        $this->setInaccessibleProperty($this->instance , 'storedQueryCriterions', $fixtureStoredQueries);
        $this->assertSame($this->instance , $this->instance->reset());
        $storedQueries = $this->getInaccessibleProperty($this->instance, 'storedQueryCriterions');
        $this->assertEmpty($storedQueries);
    }
    
    public function testAddCriterium() {
        $fixtureName      = 'text';
        $fixtureOperator  = 'equal';
        $fixtureValue     = 'test';
        $fixtureSeparator =  false;
        
        $fixtureQueryClass = 'stdClass';
        
        $ServiceManager = $this->prophesize('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager = $ServiceManager->reveal();
        
        
        $mockQuery = $this->prophesize('\oat\search\QueryCriterion');
        $mockQuery->setParent($this->instance)->willreturn($mockQuery);
        $mockQuery = $mockQuery->reveal();
        
        $mockFactoryProphecy = $this->prophesize('\oat\search\factory\FactoryInterface');
        $mockFactoryProphecy->setServiceLocator($mockServiceManager)->willReturn($mockFactoryProphecy)->shouldBeCalledTimes(1);
        $mockFactoryProphecy->get($fixtureQueryClass , [$fixtureName, $fixtureOperator, $fixtureValue, $fixtureSeparator])->willReturn($mockQuery)->shouldBeCalledTimes(1);
        $mockFactory = $mockFactoryProphecy->reveal();
        
        $this->setInaccessibleProperty($this->instance , 'queryParamClassName', $fixtureQueryClass);
        $this->setInaccessibleProperty($this->instance , 'factory', $mockFactory);
        $this->setInaccessibleProperty($this->instance , 'serviceLocator', $mockServiceManager);
        
        $this->assertSame($mockQuery , $this->instance->addCriterium($fixtureName, $fixtureOperator, $fixtureValue, $fixtureSeparator));
        $this->assertTrue(in_array($mockQuery , $this->getInaccessibleProperty($this->instance , 'storedQueryCriterions')));
        
    }

    public function tearDown() {
        $this->instance = null;
    }
    
}
