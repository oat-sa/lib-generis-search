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
use oat\search\DbSql\TaoRdf\Command\IsNotNull;
use oat\search\QueryCriterion;
use oat\search\test\UnitTestHelper;

/**
 * test for Is Not NULL
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class IsNotNullTest extends UnitTestHelper
{
    public function setUp(): void
    {
        $this->instance = new IsNotNull();
        $this->instance->setDriverEscaper(new EscaperStub());
    }

    public function convertProvider(): \Generator
    {
        yield [
            'http://www.w3.org/2000/01/rdf-schema#label',
            '`predicate` = "http://www.w3.org/2000/01/rdf-schema#label" AND ( `object` IS NOT NULL '
        ];

        yield [
            '',
            '`object` IS NOT NULL '
        ];

        yield [
            QueryCriterionInterface::VIRTUAL_URI_FIELD,
            ' ( `subject` IS NOT NULL ',
        ];
    }

    /**
     * @dataProvider convertProvider
     *
     * @param string $predicate
     * @param string $expected
     */
    public function testConvert(string $predicate, string $expected): void
    {
        $queryCriterion = new QueryCriterion();
        $queryCriterion->setName($predicate);

        $this->assertSame($expected, $this->instance->convert($queryCriterion));
    }
}
