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
 *  Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\search\test\searchImpTest;

/**
 *  AbstractSearchGateWay Test
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class AbstractSearchGateWayTest extends \oat\search\test\UnitTestHelper {
    
    /**
     * test init
     */
    public function testInit() {
        $fixtureDriver  = 'taoRdf';
        $fixtureOptions = [
            'driver' => $fixtureDriver,
        ];
        
        $fixtureDriverList = [
            'taoRdf' => 'search.driver.mysql'
        ];
        
        $DriverProphecy    = $this->prophesize('oat\search\base\Query\EscaperInterface');
        $DriverMock        = $DriverProphecy->reveal();
        
        $ServiceManager    = $this->prophesize('\Zend\ServiceManager\ServiceManager');
        $ServiceManager->get('search.options')->willReturn($fixtureOptions);
        $ServiceManager->get('search.driver.mysql')->willReturn($DriverMock);
        $ServiceManagerMock = $ServiceManager->reveal();
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\AbstractSearchGateWay',
                [], '',  true, true, true, 
                ['getServiceLocator' , 'setOptions' , 'setDriverEscaper']
                );
        
        $this->instance->expects($this->exactly(2))->method('getServiceLocator')->willReturn($ServiceManagerMock);
        $this->instance->expects($this->once())->method('setOptions')->with($fixtureOptions)->willReturn($this->instance);
        $this->instance->expects($this->once())->method('setDriverEscaper')->with($DriverMock)->willReturn($this->instance);
        
        $this->setInaccessibleProperty($this->instance, 'driverList', $fixtureDriverList);
        
        $this->assertSame($this->instance, $this->instance->init());
        
    }
    /**
     * test parse
     * verify serialyser is called and parse query is stored
     */
    public function testParse() {
        
        $fixtureQuery = 'select * from toto where id = 2';
        
        $builder        = $this->prophesize('\oat\search\base\QueryBuilderInterface');
        $BuilderMock    = $builder->reveal();
        
        $SerialyserProphecy = $this->prophesize('\oat\search\base\QuerySerialyserInterface');
        
        $SerialyserProphecy->setCriteriaList($BuilderMock)->willReturn($SerialyserProphecy);
        $SerialyserProphecy->serialyse()->willReturn($fixtureQuery);
        
        $SerialyserMock     = $SerialyserProphecy->reveal();
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\AbstractSearchGateWay',
                [], '',  true, true, true, 
                ['getSerialyser']
            );
        
        $this->instance->expects($this->once())->method('getSerialyser')->willReturn($SerialyserMock);
        
        $this->assertSame($this->instance, $this->instance->serialyse($BuilderMock));
        $this->assertSame($fixtureQuery, $this->getInaccessibleProperty($this->instance, 'parsedQuery'));
    }
    
    /**
     * verify driverName property is returned
     */
    public function testGetDriverName() {
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\AbstractSearchGateWay'
            );
        
        $fixtureDriverName = 'mysql';
        $this->setInaccessibleProperty($this->instance, 'driverName' , $fixtureDriverName);
        $this->assertSame($fixtureDriverName, $this->instance->getDriverName());
    
    }
    
    /**
     * test connector getter and setter
     */
    public function testSetGetConnector() {
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\AbstractSearchGateWay'
            );
        
        $connectorMock = $this->prophesize('\PDO')->reveal();

        $this->assertSame($this->instance, $this->instance->setConnector($connectorMock));
        $this->assertSame($connectorMock, $this->instance->getConnector());
    }
    
    /**
     * verify serialyser initialisation
     */
    public function testGetSerialyser() {
        
        $fixtureDriver  = 'taoRdf';
        $fixtureOptions = [
            'driver' => $fixtureDriver,
        ];
        
        $fixtureSerialyserList = [
            'taoRdf' => 'search.tao.serialyser'
        ];
        
        $DriverMock     = $this->prophesize('oat\search\base\Query\EscaperInterface')->reveal();
        
        $ServiceManager    = $this->prophesize('\Zend\ServiceManager\ServiceManager');
        $ServiceManagerMock = $ServiceManager->reveal();
        
        $SerialyserProphecy = $this->prophesize('\oat\search\base\QuerySerialyserInterface');
        $SerialyserProphecy->setServiceLocator($ServiceManagerMock)->willReturn($SerialyserProphecy);
        $SerialyserProphecy->setDriverEscaper($DriverMock)->willReturn($SerialyserProphecy);
        $SerialyserProphecy->setOptions($fixtureOptions)->willReturn($SerialyserProphecy);
        $SerialyserProphecy->prefixQuery()->willReturn($SerialyserProphecy);
        $SerialyserMock     = $SerialyserProphecy->reveal();
        
        $ServiceManager->get('search.tao.serialyser')->willReturn($SerialyserMock);
        $ServiceManagerMock = $ServiceManager->reveal();  
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\AbstractSearchGateWay',
                [], '',  true, true, true, 
                ['getServiceLocator']
                );
        
        $this->instance->expects($this->once())->method('getServiceLocator')->willReturn($ServiceManagerMock);
        
        $this->setInaccessibleProperty($this->instance, 'serialyserList', $fixtureSerialyserList);
        $this->setInaccessibleProperty($this->instance, 'options', $fixtureOptions);
        $this->setInaccessibleProperty($this->instance, 'driverName', $fixtureDriver);
        $this->setInaccessibleProperty($this->instance, 'driverEscaper', $DriverMock);
        $this->setInaccessibleProperty($this->instance, 'serviceLocator', $ServiceManagerMock);
         
        $this->assertSame($SerialyserMock, $this->instance->getSerialyser());
    }
     /**
     * test resultSetClassName getter and setter
     */
    public function testSetGetResultSetClassName() {
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\AbstractSearchGateWay'
            );
        
        $fixtureResultSetName = 'search.result.tao';
        $this->assertSame($this->instance, $this->instance->setResultSetClassName($fixtureResultSetName));
        $this->assertSame($fixtureResultSetName, $this->instance->getResultSetClassName());
    }
    
     /**
     * test builderClassName getter and setter
     */
    public function testSetGetBuilderClassName() {
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\AbstractSearchGateWay'
            );
        
        $fixtureResultSetName = 'search.query.builder.test';
        $this->assertSame($this->instance, $this->instance->setBuilderClassName($fixtureResultSetName));
        $this->assertSame($fixtureResultSetName, $this->instance->getBuilderClassName());
    }
    
    /**
     * verify builder initialisation
     */
    public function testQuery() {
        $fixtureDriver  = 'taoRdf';
        
        $fixtureOptions = [
            'driver' => $fixtureDriver,
        ];
        
        $fixtureBuilderService = 'search.query.builder.test';
        
        $ServiceManager    = $this->prophesize('\Zend\ServiceManager\ServiceManager');
        $ServiceManagerMock = $ServiceManager->reveal();
        
        $QueryProphecy = $this->prophesize('\oat\search\base\QueryBuilderInterface');
        $QueryProphecy->setServiceLocator($ServiceManagerMock)->willReturn($QueryProphecy);
        $QueryProphecy->setOptions($fixtureOptions)->willReturn($QueryProphecy);

        $QueryMock    = $QueryProphecy->reveal();
        
        $ServiceManager->get($fixtureBuilderService)->willReturn($QueryMock);
        $ServiceManagerMock = $ServiceManager->reveal();  
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\search\AbstractSearchGateWay',
                [], '',  true, true, true, 
                ['getServiceLocator']
                );
        
        $this->instance->expects($this->once())->method('getServiceLocator')->willReturn($ServiceManagerMock);
        
        $this->setInaccessibleProperty($this->instance, 'options', $fixtureOptions);
        $this->setInaccessibleProperty($this->instance, 'serviceLocator', $ServiceManagerMock);
        $this->setInaccessibleProperty($this->instance, 'builderClassName', $fixtureBuilderService);
        
        $this->assertSame($QueryMock, $this->instance->query());
    }
}
