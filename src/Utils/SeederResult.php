<?php

namespace TimoKoerber\LaravelJsonSeeder\Utils;

class SeederResult
{
    public const ERROR_NO_TABLE = 'Table does not exist!';
    public const ERROR_SYNTAX_INVALID = 'JSON syntax is invalid!';
    public const ERROR_NO_ROWS = 'JSON file has no rows!';
    public const ERROR_FILE_EMPTY = 'JSON file is empty!';
    public const ERROR_EXCEPTION = 'Exception occured! Check logfile!';
    public const ERROR_FIELDS_MISSING = 'Missing fields!';
    public const ERROR_FIELDS_UNKNOWN = 'Unknown fields!';

    public const STATUS_SUCCEEDED = 'succeeded';
    public const STATUS_ABORTED = 'aborted';

    public const TABLE_STATUS_EXISTS = 'exists';
    public const TABLE_STATUS_NOT_FOUND = 'not-found';

    protected $filename;

    protected $rows = 0;

    protected $table;

    protected $tableStatus;

    protected $result;

    protected $error;

    protected $status;

    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getRows()
    {
        if (!$this->rows) {
            return '-';
        }

        return $this->rows;
    }

    public function setRows($rows): void
    {
        $this->rows = $rows;
    }

    public function addRow()
    {
        $this->rows++;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getTableMessage()
    {
        if ($this->tableStatus === self::TABLE_STATUS_EXISTS) {
            return '<info>'.$this->getTable().'</info>';
        }

        return '<error>'.$this->getTable().'</error>';
    }

    public function setTable($table): void
    {
        $this->table = $table;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getResultMessage()
    {
        return '<info>'.$this->result.'</info>';
    }

    public function setResult($result): void
    {
        $this->result = $result;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getErrorMessage()
    {
        if ($this->status === self::STATUS_ABORTED) {
            return '<error>'.$this->error.'</error>';
        }

        return '<comment>'.$this->error.'</comment>';
    }

    public function setError($error): void
    {
        $this->error = $error;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getStatusMessage()
    {
        if ($this->status === self::STATUS_ABORTED) {
            return '<error>'.$this->status.'</error>';
        }

        return '<info>'.$this->status. '</info>';
    }

    public function setStatus($status): void
    {
        $this->status = $status;
    }

    public function setStatusAborted()
    {
        $this->status = self::STATUS_ABORTED;
    }

    public function setStatusSucceeded()
    {
        $this->status = self::STATUS_SUCCEEDED;
    }

    public function getTableStatus()
    {
        return $this->tableStatus;
    }

    public function setTableStatus($tableStatus)
    {
        $this->tableStatus = $tableStatus;
    }
}
