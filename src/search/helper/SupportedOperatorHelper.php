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

namespace oat\taoSearch\model\search\helper;

/**
 * Class SupportedOperatorHelper
 *
 * define constant for each supported operator.
 *
 * @package oat\taoSearch\model\search\helper
 */
class SupportedOperatorHelper {

    const EQUAL = 'equal';

    const DIFFERENT = 'notequal';

    const GREATER_THAN = 'gt';

    const GREATER_THAN_EQUAL = 'gte';

    const LESSER_THAN = 'lt';

    const LESSER_THAN_EQUAL = 'lte';

    const BETWEEN = 'between';

    const IN      = 'in';

    const MATCH   = 'match';

    const CONTAIN = 'contain';
    
    const BEGIN_BY = 'begin';

}