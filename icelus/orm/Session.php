<?php

/**
 *
 * @author Bruno Silva
 * @license MIT Licence
 * @link https://github.com/brunoosilvas/icelus
 *
 */

namespace icelus\orm;

use icelus\orm\dialect\Dialect;

class Session
{
    private $dbc;
    private $dialect;

    public function __construct(\PDO $dbc, Dialect $dialect) {
        $this->dbc = $dbc;
        $this->dialect = $dialect;
    }

    public function getDbc()
    {
        return $this->dbc;
    }

    public function setDbc(\PDO $dbc)
    {
        $this->dbc = $dbc;
    }

    public function getDialect()
    {
        return $this->dialect;
    }

    public function setDialect(Dialect $dialect)
    {
        $this->dialect = $dialect;
    }
}