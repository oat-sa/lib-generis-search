<?php
/**
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

namespace oat\search\DbSql\Driver;

use oat\search\base\Query\EscaperAbstract;

/**
 * Escapers are used to format query params
 * they quote string and escape table name (for example)
 * using the right chars
 * used for postgresql databases
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class PostgreSQL extends EscaperAbstract
{
    /**
    * use to quote string value
    * @var string
    */
    protected $escapeStringChar = '\'';
    /**
     * use to quote database system reserved name
     * @var string
     */
    protected $escapeReserved   = '"';
    /**
     * format Database instructions
     * @param string $stringValue
     * @return string
     */
    public function dbCommand($stringValue) {
        return strtoupper($stringValue);
    }
    /**
     * escape string value
     * @param string $stringValue
     * @return string
     */
    public function escape($stringValue) {
        return pg_escape_string($stringValue);
    }
    
    public function random() {
        return 'RANDOM()';
    }
    
    public function like() {
        return 'ILIKE';
    }
    
    public function notLike() {
        return 'NOT ' . $this->like();
    }
}
