<?php
/**  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\search\DbSql\TaoRdf\Command;

use oat\search\base\exception\QueryParsingException;
use oat\search\base\QueryCriterionInterface;

/**
 *  IsNULL operator
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class IsNULL extends AbstractRdfOperator {
    
    /**
     * operator
     * @var string
     */
    protected $operator = 'IS NULL';
    
    /**
     * convert Query Param to mysql query string
     * @param QueryCriterionInterface $query
     * @return string
     * @throws QueryParsingException
     */
    public function convert(QueryCriterionInterface $query) {
        return '' .$this->setPropertyName($query->getName()) . ' ' . $this->getDriverEscaper()->reserved('object') . ' ' . $this->getOperator() . ' ';
    }
    
}
