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
 *  Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\search\test\searchTest\UsableTrait;

use oat\search\base\Query\EscaperInterface;
use oat\search\test\UnitTestHelper;
use oat\search\UsableTrait\DriverSensitiveTrait;

/**
 * Description of DriverSensitiveTraitTest
 *
 * @author christophe
 */
class DriverSensitiveTraitTest extends UnitTestHelper
{

    /** @var DriverSensitiveTrait */
    protected $instance;

    public function setup(): void
    {

        $this->instance = $this->getMockForTrait(DriverSensitiveTrait::class);
    }

    public function testsSetGetDriverEscaper()
    {

        $mock = $this->getMockBuilder(EscaperInterface::class)->getMock();

        $this->assertSame($this->instance, $this->instance->setDriverEscaper($mock));
        $this->assertSame($mock, $this->instance->getDriverEscaper());
    }
}
