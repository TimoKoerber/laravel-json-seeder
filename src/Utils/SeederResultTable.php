<?php

namespace TimoKoerber\LaravelJsonSeeder\Utils;

class SeederResultTable
{
    /**
     * @var SeederResult[]
     */
    protected $rows;

    public function addRow(SeederResult $row)
    {
        $this->rows[] = $row;

        return $this;
    }

    public function getHeader()
    {
        return ['File', 'Table', 'Rows', 'Status', 'Message'];
    }

    public function getResult()
    {
        $result = [];
        foreach ($this->rows as $row) {
            $element = [];
            $element[] = $row->getFilename();
            $element[] = $row->getTableMessage();
            $element[] = $row->getRows();
            $element[] = $row->getStatusMessage();
            $element[] = $row->getResultMessage().$row->getErrorMessage();
            $result[] = $element;
        }

        return $result;
    }
}
