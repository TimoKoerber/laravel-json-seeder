<?php

namespace TimoKoerber\LaravelJsonSeeder\Utils;

class SeederResult
{
    const ERROR_NO_TABLE = 'Table does not exist!';
    const ERROR_SYNTAX_INVALID = 'JSON syntax is invalid!';
    const ERROR_NO_ROWS = 'JSON file has no rows!';
    const ERROR_FILE_EMPTY = 'JSON file is empty!';
    const ERROR_EXCEPTION = 'Exception occured! Check logfile!';
    const ERROR_FIELDS_MISSING = 'Missing fields!';
    const ERROR_FIELDS_UNKNOWN = 'Unknown fields!';

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

    /**
     * @param mixed $filename
     * @return SeederResult
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return mixed
     */
    public function getRows()
    {
        if (! $this->rows) {
            return '-';
        }

        return $this->rows;
    }

    /**
     * @param mixed $rows
     */
    public function setRows($rows): void
    {
        $this->rows = $rows;
    }

    public function addRow()
    {
        $this->rows++;
    }

    /**
     * @return mixed
     */
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

    /**
     * @param mixed $table
     */
    public function setTable($table): void
    {
        $this->table = $table;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return mixed
     */
    public function getResultMessage()
    {
        return "<info>$this->result</info>";
    }

    /**
     * @param mixed $result
     */
    public function setResult($result): void
    {
        $this->result = $result;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return mixed
     */
    public function getErrorMessage()
    {
        if ($this->status === self::STATUS_ABORTED) {
            return "<error>$this->error</error>";
        }

        return "<comment>$this->error</comment>";
    }

    /**
     * @param mixed $error
     */
    public function setError($error): void
    {
        $this->error = $error;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getStatusMessage()
    {
        if ($this->status == self::STATUS_ABORTED) {
            return "<error>$this->status</error>";
        }

        return "<info>$this->status</info>";
    }

    /**
     * @param mixed $status
     */
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

    /**
     * @return mixed
     */
    public function getTableStatus()
    {
        return $this->tableStatus;
    }

    /**
     * @param mixed $tableStatus
     */
    public function setTableStatus($tableStatus): void
    {
        $this->tableStatus = $tableStatus;
    }
}
