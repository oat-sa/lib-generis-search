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
namespace oat\taoSearch\model\search\UsableTrait;

/**
 * use with sortableInterface
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
trait SortableTrait {
    /**
     * array of sort criterias
     * @var array
     */
    protected $sort = [];

    /**
     * return sort criterias
     * @return array
     * @see \oat\taoSearch\model\search\SortableInterface::getSort
     */
    public function getSort() {
        return $this->sort;
    }
    /**
     * set up sort criteria
     * as ['name' => 'desc' , 'age' => 'asc']
     *
     * @param array $sortCriteria
     * @return $this
     *
     * @see \oat\taoSearch\model\search\SortableInterface::sort
     */
    public function sort(array $sortCriteria) {
        $this->sort = $sortCriteria;
        return $this;
    }
    
}
