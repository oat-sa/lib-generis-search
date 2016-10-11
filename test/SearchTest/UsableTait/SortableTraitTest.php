<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace oat\search\test\searchTest\UsableTrait;

class SortableTraitTest extends \oat\search\test\UnitTestHelper 
{
    
    protected $instance;
    
    public function setup() {
        
        $this->instance = $this->getMockForTrait('\\oat\\search\\UsableTrait\\SortableTrait');
        
    }
    /**
     * test sort and getSort
     */
    public function testSort() {
        $fixtureSortCriteria = [
            'id'   => 'asc',
            'date' => 'desc'
        ];
        $this->assertSame($this->instance, $this->instance->sort($fixtureSortCriteria));
        $this->assertSame($fixtureSortCriteria, $this->instance->getSort());
    }
    /*
     * test setRandom and getRandom
     */
    public function testSetGetRandom() {

        $this->assertFalse($this->instance->getRandom());
        $this->assertSame($this->instance, $this->instance->setRandom());
        $this->assertTrue($this->instance->getRandom());
    }
    
}

