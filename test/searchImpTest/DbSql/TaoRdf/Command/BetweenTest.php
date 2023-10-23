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

namespace oat\search\test\searchImpTest\DbSql\TaoRdf\Command;

use oat\search\base\QueryCriterionInterface;
use oat\search\DbSql\TaoRdf\Command\Between;
use oat\search\QueryCriterion;
use oat\search\test\UnitTestHelper;

/**
 * test for Between
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class BetweenTest extends UnitTestHelper
{
    public function setUp(): void
    {
        $this->instance = new Between();
        $this->instance->setDriverEscaper(new EscaperStub());
    }

    public function convertProvider(): \Generator
    {
        yield [
            'http://www.w3.org/2000/01/rdf-schema#label' ,
            [0 , 5] ,
            '`predicate` = "http://www.w3.org/2000/01/rdf-schema#label" AND ( `object` BETWEEN "0" AND "5" ',
        ];

        yield [
            QueryCriterionInterface::VIRTUAL_URI_FIELD,
            [0 , 5] ,
            ' ( `subject` BETWEEN "0" AND "5" ',
        ];
    }
    
    /**
     * @dataProvider convertProvider
     *
     * @param string $predicate
     * @param mixed $value
     * @param string $expected
     */
    public function testConvert(string $predicate , $value, string $expected): void
    {
        $queryCriterion = new QueryCriterion();
        $queryCriterion->setName($predicate);
        $queryCriterion->setValue($value);
        
        $this->assertSame($expected, $this->instance->convert($queryCriterion));
    }

    public function testConvertException(): void
    {
        $this->expectException('\oat\search\base\exception\QueryParsingException');
        $this->instance->convert(new QueryCriterion());
    }
}
