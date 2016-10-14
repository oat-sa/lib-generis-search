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

namespace oat\search\base;

/**
 * interface for sortable queries
 *
 * @author christophe
 */
interface SortableInterface {
    
    /**
     * set up sort criteria
     * as ['name' => 'desc' , 'age' => 'asc']
     *
     * @param array $sortCriteria
     * @return $this
     */
    public function sort(array $sortCriteria);
    
    /**
     * return sort criterias
     * @return array
     */
    public function getSort();
    
    /**
     * set up random sort
     * @return $this
     */
    public function setRandom();
    
    /**
     * return random mod value
     * @return boolean
     */
    public function getRandom();
    
}
