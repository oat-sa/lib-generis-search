<?php

use oat\search\base\Query\EscaperAbstract;
use oat\search\DbSql\Driver\MySQL;

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
 *  Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

/**
 * Description of MySQLTest
 *
 * @author christophe
 */
class MySQLTest extends \oat\search\test\UnitTestHelper  {
    
    /**
     * @var EscaperAbstract
     */
    protected $instance;
    
    public function setUp() {
        $this->instance = new MySQL;
    }

    public function testDbCommand() {
        
        $fitureCommand = 'From';
        $expected      = 'FROM';
        
        $this->assertSame($expected, $this->instance->dbCommand($fitureCommand));
    }
    
    public function testRandom() {
        $this->assertSame('RAND()', $this->instance->random());
    }
    
}
