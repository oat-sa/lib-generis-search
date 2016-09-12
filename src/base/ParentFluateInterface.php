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

namespace oat\search\base;

/**
 * use it if you need to back on parent object and keep fluate interface
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
interface ParentFluateInterface {
    
    /**
     * set parent object whitch one has instanciate current object
     * @param object $parent
     * @return $this
     */
    public function setParent($parent);
    
    /**
     * return parent object whitch one has instanciate current object
     * @return object
     */
    public function getParent();
    
    /**
     * return current object
     * @return $this
     */
    public function me();
    
}
