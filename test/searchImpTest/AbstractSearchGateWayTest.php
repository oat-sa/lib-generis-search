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

namespace oat\taoSearch\test\searchImpTest;

/**
 *  AbstractSearchGateWay Test
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class AbstractSearchGateWayTest extends \oat\taoSearch\test\UnitTestHelper {
    
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
        
        $DriverProphecy    = $this->prophesize('oat\taoSearch\model\search\Query\EscaperInterface');
        $DriverMock        = $DriverProphecy->reveal();
        
        $ServiceManager    = $this->prophesize('\Zend\ServiceManager\ServiceManager');
        $ServiceManager->get('search.options')->willReturn($fixtureOptions);
        $ServiceManager->get('search.driver.mysql')->willReturn($DriverMock);
        $ServiceManagerMock = $ServiceManager->reveal();
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\taoSearch\model\searchImp\AbstractSearchGateWay',
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
     * verify parser is called and parse query is stored
     */
    public function testParse() {
        
        $fixtureQuery = 'select * from toto where id = 2';
        
        $builder        = $this->prophesize('\oat\taoSearch\model\search\QueryBuilderInterface');
        $BuilderMock    = $builder->reveal();
        
        $ParserProphecy = $this->prophesize('\oat\taoSearch\model\search\QueryParserInterface');
        
        $ParserProphecy->setCriteriaList($BuilderMock)->willReturn($ParserProphecy);
        $ParserProphecy->parse()->willReturn($fixtureQuery);
        
        $ParserMock     = $ParserProphecy->reveal();
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\taoSearch\model\searchImp\AbstractSearchGateWay',
                [], '',  true, true, true, 
                ['getParser']
            );
        
        $this->instance->expects($this->once())->method('getParser')->willReturn($ParserMock);
        
        $this->assertSame($this->instance, $this->instance->parse($BuilderMock));
        $this->assertSame($fixtureQuery, $this->getInaccessibleProperty($this->instance, 'parsedQuery'));
    }
    
    /**
     * verify driverName property is returned
     */
    public function testGetDriverName() {
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\taoSearch\model\searchImp\AbstractSearchGateWay'
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
                'oat\taoSearch\model\searchImp\AbstractSearchGateWay'
            );
        
        $connectorMock = $this->prophesize('\PDO')->reveal();

        $this->assertSame($this->instance, $this->instance->setConnector($connectorMock));
        $this->assertSame($connectorMock, $this->instance->getConnector());
    }
    
    /**
     * verify parser initialisation
     */
    public function testGetParser() {
        
        $fixtureDriver  = 'taoRdf';
        $fixtureOptions = [
            'driver' => $fixtureDriver,
        ];
        
        $fixtureParserList = [
            'taoRdf' => 'search.tao.parser'
        ];
        
        $DriverMock     = $this->prophesize('oat\taoSearch\model\search\Query\EscaperInterface')->reveal();
        
        $ServiceManager    = $this->prophesize('\Zend\ServiceManager\ServiceManager');
        $ServiceManagerMock = $ServiceManager->reveal();
        
        $ParserProphecy = $this->prophesize('\oat\taoSearch\model\search\QueryParserInterface');
        $ParserProphecy->setServiceLocator($ServiceManagerMock)->willReturn($ParserProphecy);
        $ParserProphecy->setDriverEscaper($DriverMock)->willReturn($ParserProphecy);
        $ParserProphecy->setOptions($fixtureOptions)->willReturn($ParserProphecy);
        $ParserProphecy->prefixQuery()->willReturn($ParserProphecy);
        $ParserMock     = $ParserProphecy->reveal();
        
        $ServiceManager->get('search.tao.parser')->willReturn($ParserMock);
        $ServiceManagerMock = $ServiceManager->reveal();  
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\taoSearch\model\searchImp\AbstractSearchGateWay',
                [], '',  true, true, true, 
                ['getServiceLocator']
                );
        
        $this->instance->expects($this->once())->method('getServiceLocator')->willReturn($ServiceManagerMock);
        
        $this->setInaccessibleProperty($this->instance, 'parserList', $fixtureParserList);
        $this->setInaccessibleProperty($this->instance, 'options', $fixtureOptions);
        $this->setInaccessibleProperty($this->instance, 'driverName', $fixtureDriver);
        $this->setInaccessibleProperty($this->instance, 'driverEscaper', $DriverMock);
        $this->setInaccessibleProperty($this->instance, 'serviceLocator', $ServiceManagerMock);
         
        $this->assertSame($ParserMock, $this->instance->getParser());
    }
     /**
     * test resultSetClassName getter and setter
     */
    public function testSetGetResultSetClassName() {
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\taoSearch\model\searchImp\AbstractSearchGateWay'
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
                'oat\taoSearch\model\searchImp\AbstractSearchGateWay'
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
        
        $QueryProphecy = $this->prophesize('\oat\taoSearch\model\search\QueryBuilderInterface');
        $QueryProphecy->setServiceLocator($ServiceManagerMock)->willReturn($QueryProphecy);
        $QueryProphecy->setOptions($fixtureOptions)->willReturn($QueryProphecy);

        $QueryMock    = $QueryProphecy->reveal();
        
        $ServiceManager->get($fixtureBuilderService)->willReturn($QueryMock);
        $ServiceManagerMock = $ServiceManager->reveal();  
        
        $this->instance = $this->getMockForAbstractClass(
                'oat\taoSearch\model\searchImp\AbstractSearchGateWay',
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
