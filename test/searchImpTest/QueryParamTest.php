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
 * Description of QueryCriterionTest
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class QueryCriterionTest extends \oat\search\test\UnitTestHelper {
    
    /**
     *
     * @var QueryCriterion
     */
    protected $instance;
    
    public function setUp() {
        $this->instance = new \oat\search\QueryCriterion;
    }
    
    public function testSetGetName() {
        $fixtureName = 'text';
        $this->assertSame($this->instance, $this->instance->setName($fixtureName));
        $this->assertSame($fixtureName, $this->instance->getName());
    }
    
    public function testSetGetOperator() {
        $fixtureOperator = 'contain';
        $this->assertSame($this->instance, $this->instance->setOperator($fixtureOperator));
        $this->assertSame($fixtureOperator, $this->instance->getOperator());
    }
    
    public function testSetGetValue() {
        $fixtureValue = 'christophe';
        $this->assertSame($this->instance, $this->instance->setValue($fixtureValue));
        $this->assertSame($fixtureValue, $this->instance->getValue());
    }
    

    public function testAddGetAnd() {
        
        $fixtureValue    = 'christophe';
        $fixtureOperator = 'equal';
        
        $this->assertSame($this->instance, $this->instance->addAnd($fixtureValue , $fixtureOperator));
        $and = $this->instance->getAnd();
        $this->assertSame($fixtureValue, $and[0]->getValue());
        $this->assertSame($fixtureOperator, $and[0]->getOperator());
    }
    
    public function testAddGetOr() {
        
        $fixtureValue    = 'christophe';
        $fixtureOperator = 'equal';
        
        $this->assertSame($this->instance, $this->instance->addOr($fixtureValue , $fixtureOperator));
        $or = $this->instance->getOr();
        $this->assertSame($fixtureValue, $or[0]->getValue());
        $this->assertSame($fixtureOperator, $or[0]->getOperator());
    }
    
    public function setDefaultOperatorProvider() {
        return 
        [
            ['equal' , 'contain' ,'contain'],
            ['equal' , null ,'equal'],
        ];
    }
    /**
     * @dataProvider setDefaultOperatorProvider
     * @param string $defaultOperator
     * @param string|null $operator
     * @param string $expected
     */
    public function testSetDefaultOperator($defaultOperator , $operator , $expected) {
        $this->setInaccessibleProperty($this->instance, 'operator', $defaultOperator);
        $this->assertSame($expected, $this->invokeProtectedMethod($this->instance,'setDefaultOperator' , [$operator]));
    }

    public function tearDown() {
        $this->instance = null;
    }
    
}
