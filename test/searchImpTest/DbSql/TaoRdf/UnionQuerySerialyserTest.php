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
 *  Copyright (c) 2019 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\search\test\searchImpTest\DbSql\TaoRdf;

use oat\search\base\Query\EscaperInterface;
use oat\search\DbSql\TaoRdf\UnionQuerySerialyser;
use oat\search\test\UnitTestHelper;
use PHPUnit\Framework\MockObject\MockClass;

/**
 * Tests for UnionsQuerySerialyser class
 */
class UnionQuerySerialyserTest extends UnitTestHelper
{
    /**
     * @dataProvider modelsToTest
     *
     * @param $expected
     * @param $model
     */
    public function testAddOperator($implodedModels, $model)
    {
        $queryBeginning = 'Beginning of the query';
        $table = 'tableName';
        $operationSeparator = ' ';
        $expression = 'a condition';
        $userLanguage = 'language of the user';

        $subject = new UnionQuerySerialyser();

        /** @var EscaperInterface|MockClass $driverEscaper */
        $driverEscaper = $this->getMockBuilder(EscaperInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['dbCommand', 'reserved'])
            ->getMockForAbstractClass();
        $driverEscaper->method('dbCommand')->willReturnArgument(0);
        $driverEscaper->method('reserved')->willReturnArgument(0);

        $subject->setDriverEscaper($driverEscaper);
        $subject->setOptions(['table' => $table]);

        $this->setInaccessibleProperty($subject, 'query', $queryBeginning);
        $this->setInaccessibleProperty($subject, 'operationSeparator', $operationSeparator);
        $this->setInaccessibleProperty($subject, 'model', $model);
        $this->setInaccessibleProperty($subject, 'userLanguage', $userLanguage);

        // Generated query.
        $expected = $queryBeginning . '(SELECT DISTINCT subject FROM ' . $table . ' WHERE' . $operationSeparator . $userLanguage . ' ' . $expression;
        if ($model !== null) {
            $expected .= 'AND modelid IN (' . $implodedModels . ')' . $operationSeparator;
        }
        $expected .= ' ))';

        $this->assertEquals($subject, $subject->addOperator($expression));
        $this->assertEquals($expected, $this->getInaccessibleProperty($subject, 'query'));
    }

    public function modelsToTest()
    {
        /** @var SmoothModel|MockClass $emptyModel */
        $emptyModel = $this->getMockBuilder(SmoothModel::class)
            ->disableOriginalConstructor()
            ->setMethods(['getReadableModels'])
            ->getMock();
        $emptyModel->method('getReadableModels')->willReturn([]);

        $modelId1 = 'model id #1';
        $modelId2 = 'model id #2';
        $modelId3 = 'model id #3';

        /** @var SmoothModel|MockClass $fullModel */
        $fullModel = $this->getMockBuilder(SmoothModel::class)
            ->disableOriginalConstructor()
            ->setMethods(['getReadableModels'])
            ->getMock();
        $fullModel->method('getReadableModels')->willReturn([$modelId1, $modelId2, $modelId3]);

        return [
            ["''", null],
            ["", $emptyModel],
            [$modelId1 . "," . $modelId2 . "," . $modelId3, $fullModel],
        ];
    }
}

/**
 * This class is here just for the sake of testing this library alone.
 * The original core_kernel_persistence_smoothsql_SmoothModel class is part or oat-sa/generis
 * which is not required by the library.
 */
class SmoothModel
{
    public function getReadableModels()
    {
    }
}
