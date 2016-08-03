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

namespace oat\search\base\command;

use oat\search\base\exception\QueryParsingException;
use oat\search\base\QueryCriterionInterface;
use oat\search\base\Query\DriverSensitiveInterface;

/**
 * Interface OperatorConverterInterface
 *
 * use for command design pattern on query parsing
 * with a specific operator
 *
 * @package oat\search\base\command
 */
interface OperatorConverterInterface extends DriverSensitiveInterface
{
    /**
     * create a part of query exploitable by database driver
     * throw an exception if value data type isn't compatible with operator
     *
     * @throws QueryParsingException
     * @param QueryCriterionInterface $query
     * @return mixed
     */
    public function convert(QueryCriterionInterface $query);

}