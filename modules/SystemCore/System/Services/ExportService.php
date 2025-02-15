<?php

namespace Modules\SystemCore\System\Services;

class ExportService
{
    private $source  = null;

    private $name    = 'Data.csv';

    private $columns = [];

    public function __construct()
    {
    }

    public function of($source)
    {
        $this->source = $source;

        return $this;
    }

    public function name($name)
    {
        $this->name = $name;

        return $this;
    }

    public function addColumn($name, $handeler)
    {
        $this->columns[$name] = $handeler;

        return $this;
    }

    public function toCsv()
    {
        $filename = $this->name;
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () {

            $handle  = fopen('php://output', 'w');
            $columns = array_keys($this->columns);

            fputcsv($handle, $columns);

            $this->source->chunk(250, function ($rows) use ($handle, $columns) {
                foreach ($rows as $row) {
                    $data = array_map(function ($column) use ($row) {
                        return $this->columns[$column]($row);
                    }, $columns);
                    fputcsv($handle, $data);
                }
            });

            fclose($handle);

        }, 200, $headers);
    }
}