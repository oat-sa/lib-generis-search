<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace oat\search\test\searchTest\UsableTrait;

use oat\search\test\UnitTestHelper;

/**
 * Description of LimitableTraitTest
 *
 * @author christophe
 */
class LimitableTraitTest extends UnitTestHelper
{

    protected $instance;

    public function setup(): void
    {

        $this->instance = $this->getMockForTrait('\\oat\\search\\UsableTrait\\LimitableTrait');

    }

    /**
     * test sort and getSort
     */
    public function testLimit()
    {
        $fixtureLimit = 10;
        $fixtureOffset = 5;

        $this->assertSame($this->instance, $this->instance->setOffset($fixtureOffset));
        $this->assertSame($this->instance, $this->instance->setLimit($fixtureLimit));
        $this->assertSame($fixtureLimit, $this->instance->getLimit());
        $this->assertSame($fixtureOffset, $this->instance->getOffset());
    }
}
