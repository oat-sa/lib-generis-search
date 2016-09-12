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

namespace oat\search\test\SearchTest\Query;

/**
 * Description of EscaperAbstractTest
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class EscaperAbstractTest extends \oat\search\test\UnitTestHelper 
{
    /**
     * @var \oat\search\base\Query\EscaperAbstract 
     */
    protected $instance;

    public function setUp() {
        $this->instance = $this->getMockForAbstractClass('oat\search\base\Query\EscaperAbstract');
    }
    
    public function testQuote() {
        
        $fixtureQuote = '"';
        $fixtureString = 'test';
        $this->setInaccessibleProperty($this->instance , 'escapeStringChar', $fixtureQuote);
        $this->assertSame($fixtureQuote, $this->instance->getQuoteChar());
        $this->assertSame($fixtureQuote . $fixtureString . $fixtureQuote, $this->instance->quote($fixtureString));
        $this->assertSame($fixtureQuote . $fixtureQuote, $this->instance->getEmpty());
    }
    
    public function testReserved() {
        
        $fixtureQuote = '`';
        $fixtureString = 'test';
        $this->setInaccessibleProperty($this->instance , 'escapeReserved', $fixtureQuote);
        $this->assertSame($fixtureQuote, $this->instance->getReservedQuote());
        $this->assertSame($fixtureQuote . $fixtureString . $fixtureQuote, $this->instance->reserved($fixtureString));
    }
    
    public function testGetFieldsSeparator() {
        
        $fixtureSeparator = ',';
        $this->setInaccessibleProperty($this->instance , 'fieldsSeparator', $fixtureSeparator);
        $this->assertSame($fixtureSeparator, $this->instance->getFieldsSeparator());
    }
    
}
