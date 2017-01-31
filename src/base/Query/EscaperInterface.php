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
namespace oat\search\base\Query;
/**
 * Escapers are used to format query params
 * they quote string and escape table name (for example)
 * using the right chars
 * 
 * INTERFACE base for database driver escaper
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
interface EscaperInterface {
    
    /**
     * add quote string in function of your database querying driver 
     * 
     * @param string $stringValue
     * @return string
     */
    public function quote($stringValue);
    
    /**
     * escape string in function of your database querying driver
     * @param string $stringValue
     * @return string
     */
    public function escape($stringValue);
    
    /**
     * database system reference escpaing (like table name or field name)
     * 
     * @param string $stringValue
     * @return string
     */
    public function reserved($stringValue);
    
    /**
     * format db command 
     * 
     * @param string $stringValue
     * @return string
     */
    public function dbCommand($stringValue);
    
    /**
     * return fields list separator
     * @return string
     */
    public function getFieldsSeparator();
    
    /**
     * return string quoting character
     * @return string
     */
    public function getQuoteChar(); 
    
    /**
     * return escape string
     * @return string
     */
    public function getReservedQuote();
    
    /**
     * return all fields alias (* in MySQL)
     * @return string
     */
    public function getAllFields();
    
    /**
     * return empty string equivalent
     * @return string
     */
    public function getEmpty();
    /**
     * return random cammand as string
     * @return string
     */
    public function random();

    /**
     * return case insensitive like operator
     * @return string
     */
    public function like();

    /**
     * return case insensitive like operator
     * @return string
     */
    public function notLike();
}
