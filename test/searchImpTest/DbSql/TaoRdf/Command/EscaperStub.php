<?php
/*
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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\search\test\searchImpTest\DbSql\TaoRdf\Command;

use oat\search\base\Query\EscaperInterface;

class EscaperStub implements EscaperInterface
{
    public function quote($stringValue)
    {
        return sprintf('"%s"', $stringValue);
    }

    public function escape($stringValue)
    {
        return $stringValue;
    }

    public function reserved($stringValue)
    {
        return sprintf('`%s`', $stringValue);
    }

    public function dbCommand($stringValue)
    {
        return $stringValue;
    }

    public function getFieldsSeparator()
    {
        return ',';
    }

    public function getQuoteChar()
    {
        return "'";
    }

    public function getReservedQuote()
    {
        return "`";
    }

    public function getAllFields()
    {
        return '*';
    }

    public function getEmpty()
    {
        return 'EMPTY';
    }

    public function random()
    {
        return 'RANDOM';
    }

    public function like()
    {
        return 'LIKE';
    }

    public function notLike()
    {
        return 'NOT LIKE';
    }
}
