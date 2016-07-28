<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace oat\taoSearch\test\searchTest\UsableTrait;

/**
 * Description of LimitableTraitTest
 *
 * @author christophe
 */
class LimitableTraitTest extends \oat\taoSearch\test\UnitTestHelper 
{
    
    protected $instance;
    
    public function setup() {
        
        $this->instance = $this->getMockForTrait('\\oat\\taoSearch\\model\\search\\UsableTrait\\LimitableTrait');
        
    }
    /**
     * test sort and getSort
     */
    public function testLimit() {
        $fixtureLimit  = 10;
        $fixtureOffset = 5;
        
        $this->assertSame($this->instance, $this->instance->setOffset($fixtureLimit , $fixtureOffset));
        $this->assertSame($fixtureLimit, $this->instance->getLimit());
        $this->assertSame($fixtureOffset, $this->instance->getOffset());
    }
}
