<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace oat\taoSearch\factoryTest;

use InvalidArgumentException;
use oat\search\test\UnitTestHelper;

/**
 * Description of FactoryAbstractTest
 *
 * @author christophe
 */
class FactoryAbstractTest extends UnitTestHelper
{

    protected $instance;


    public function setUp(): void
    {

        $this->instance = $this->getMockForAbstractClass('oat\search\factory\FactoryAbstract');

    }

    public function isValidClassProvide()
    {

        return [
            ['\\oat\\search\\QueryCriterion', true, false],
            ['\\oat\\search\\Query', null, true]
        ];

    }

    /**
     *
     * @param type $class
     * @param type $return
     * @param type $exception
     * @dataProvider isValidClassProvide
     */
    public function testIsValidClass($class, $return, $exception)
    {

        if ($exception) {
            $this->expectException(InvalidArgumentException::class);
        }

        $this->setInaccessibleProperty($this->instance, 'validInterface', 'oat\\search\\base\\QueryCriterionInterface');

        $class = new $class();

        $this->assertSame($return, $this->invokeProtectedMethod($this->instance, 'isValidClass', [$class]));

    }

}
