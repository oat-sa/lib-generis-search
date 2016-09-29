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

namespace oat\search;

use oat\search\base\exception\SearchGateWayExeption;
use oat\search\base\QueryBuilderInterface;

/**
 * specific tao gateWay.
 * 
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class TaoSearchGateWay extends AbstractSearchGateWay 
{
     /**
     * try to connect to database. throw an exception
     * if connection failed.
     *
     * @throws SearchGateWayExeption
     * @return $this
     */
    public function connect() {
        return true;
    }
    /**
     * not implemented just use to print query
     * @todo use generis persistence
     */
    public function search(QueryBuilderInterface $Builder) {
        $this->serialyse($Builder);
        return $this->parsedQuery;
    }
    
    /**
     * print parsed query
     * @return $this
     */
    public function printQuery() {
        echo $this->parsedQuery;
        return $this;
    }

    public function count(QueryBuilderInterface $Builder) {
         return $this->getSerialyser()->setCriteriaList($Builder)->count(true)->serialyse(); 
    }

}
