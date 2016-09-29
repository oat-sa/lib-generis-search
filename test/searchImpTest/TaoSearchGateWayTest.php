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

/**
 * TaoSearchGateWay Test
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class TaoSearchGateWayTest extends \oat\search\test\UnitTestHelper {
    
    public function setUp() {
        $this->instance = new \oat\search\TaoSearchGateWay();
    }
    /**
     * try connection
     */
    public function testConnect() {
        $this->assertTrue($this->instance->connect());
    }
    
    /**
     * print query verification
     */
    public function testPrintQuery() {
        $fixtureQuery = 'select * from toto where id = 2';
        $this->setInaccessibleProperty($this->instance, 'parsedQuery', $fixtureQuery);
        ob_start();
        $this->assertSame( $this->instance, $this->instance->printQuery());
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertSame( $fixtureQuery, $contents);
    }
    
    public function testSearch() {
        
        $this->instance = $this->getMock('\oat\search\TaoSearchGateWay' , ['serialyse']);
        $builderMock = $this->prophesize('oat\search\base\QueryBuilderInterface')->reveal();
        
        $fixtureQuery = 'select * from toto where id = 2';
        
        $this->instance->expects($this->once())
                ->method('serialyse')
                ->with($builderMock)
                ->willReturn($fixtureQuery);
        
        $this->setInaccessibleProperty($this->instance, 'parsedQuery', $fixtureQuery);
        $this->assertSame( $fixtureQuery, $this->instance->search($builderMock));
    }

    public function teaDown() {
        $this->instance = null;
    }
    
}
