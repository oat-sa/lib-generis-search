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
use oat\search\QueryBuilder;
use oat\search\test\UnitTestHelper;

/**
 * QueryBuilder test
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class QueryBuilderTest extends UnitTestHelper {
    
    /**
     *
     * @var QueryBuilder
     */
    protected $instance;
    
    public function setUp() {
        $this->instance = new QueryBuilder;
    }
    
    public function testSetQueryClassName() {
        
        $fixtureClassName = 'stdClass';
        
        $this->assertSame($this->instance, $this->instance->setQueryClassName($fixtureClassName));
        $this->assertSame($fixtureClassName, $this->getInaccessibleProperty($this->instance , 'queryClassName'));
        
    }
    
    public function testSetQueryFactory() {
        
        $Factory = new QueryFactory;
        
        $this->assertSame($this->instance , $this->instance->setQueryFactory($Factory));
        $this->assertSame($Factory , $this->getInaccessibleProperty($this->instance , 'factory'));
        
    }
    
    public function testGetStoredQueries() {
        
        $fixtureStoredQueries = [
            new Query(),
            new Query(),
            new Query(),
            new Query(),
        ];
        
        $this->setInaccessibleProperty($this->instance , 'storedQueries', $fixtureStoredQueries);
        $this->assertSame($fixtureStoredQueries, $this->instance->getStoredQueries());
    }
    
    public function testNewQuery() {
        
        $fixtureQueryClass = 'stdClass';

        $ServiceManager = $this->prophesize('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager = $ServiceManager->reveal();
 
        $mockQuery = $this->prophesize('\oat\search\Query');
        $mockQuery->setParent($this->instance)->willreturn($mockQuery);
        $mockQuery = $mockQuery->reveal();
        
        $mockFactoryProphecy = $this->prophesize('\oat\search\factory\FactoryInterface');
        $mockFactoryProphecy->setServiceLocator($mockServiceManager)->willReturn($mockFactoryProphecy)->shouldBeCalledTimes(1);
        $mockFactoryProphecy->get($fixtureQueryClass)->willReturn($mockQuery)->shouldBeCalledTimes(1);
        
        $mockFactory = $mockFactoryProphecy->reveal();
        
        $this->setInaccessibleProperty($this->instance , 'queryClassName', $fixtureQueryClass);
        $this->setInaccessibleProperty($this->instance , 'factory', $mockFactory);
        $this->setInaccessibleProperty($this->instance , 'serviceLocator', $mockServiceManager);
        
        $this->assertSame($mockQuery , $this->instance->newQuery());
        
    }
}
