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

use \oat\search\base\QueryCriterionInterface;
/**
 * create a condition to search if object contain value
 *
 * @author christophe
 */
class LikeContain extends Like {
    /**
     * operator
     * @var string
     */
    protected $operator = 'LIKE';
    /**
     * convert Query Param to mysql query string
     * @param QueryCriterionInterface $query
     * @return string
     */
    public function convert(QueryCriterionInterface $query) {
        $value = $this->getDriverEscaper()->quote($this->getDriverEscaper()->escape('%' . $query->getValue() . '%'));
        return '' .$this->setPropertyName($query->getName()) . ' ' . $this->getDriverEscaper()->reserved('object') . ' ' . $this->getOperator() . ' ' . $value . '';
    }
    
}
