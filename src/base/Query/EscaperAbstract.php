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
 * Abstract base for database driver escaper
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
abstract class EscaperAbstract implements EscaperInterface
{
   /**
    * use to quote string value
    * @var string
    */
    protected $escapeStringChar = '';
    /**
     * use to quote database system reserved name
     * @var string
     */
    protected $escapeReserved   = '';
    /**
     * All fields character
     * @var string
     */
    protected $allFieldsAlias = '*';
    /**
     * fields list separator
     * @var string 
     */
    protected $fieldsSeparator = ',';
    /**
     * empty string equivalent
     * @var string 
     */
    protected $empty = '';


    /**
     * escape string with escapeStringChar
     * @param string $stringValue
     * @return string
     */
    public function quote($stringValue) {
        return $this->escapeStringChar . $stringValue . $this->escapeStringChar;
    }
    /**
     * escape reserved table or field name with escapeReserved
     * @param string $stringValue
     * @return string
     */
    public function reserved($stringValue) {
        return $this->escapeReserved  . $stringValue . $this->escapeReserved ;
    }
    /**
     * return all fields alias (* for MySQL)
     * @return string
     */
    public function getAllFields() {
        return $this->allFieldsAlias;
    }
    /**
     * return fields list separator
     * @return string
     */
    public function getFieldsSeparator() {
        return $this->fieldsSeparator;
    }
    /**
     * return escapeStringChar
     * @return string
     */
    public function getQuoteChar() {
        return $this->escapeStringChar;
    }
    /**
     * return escapeReserved
     * @return string
     */
    public function getReservedQuote() {
        return $this->escapeReserved;
    }
    /**
     * return quoted empty string 
     */
    public function getEmpty() {
        return $this->quote($this->empty);
    }
    
}
