<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace oat\taoSearch\test\searchTest\UsableTrait;

class SortableTraitTest extends \oat\taoSearch\test\UnitTestHelper 
{
    
    protected $instance;
    
    public function setup() {
        
        $this->instance = $this->getMockForTrait('\\oat\\taoSearch\\model\\search\\UsableTrait\\SortableTrait');
        
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
    
}

