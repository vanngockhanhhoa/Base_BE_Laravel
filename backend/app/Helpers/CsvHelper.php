<?php

namespace App\Helpers;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CsvHelper
{
    /**
     * Load csv data
     *
     * @param $path_file_csv
     * @return Collection
     * @throws Exception
     */
    public function loadCsvData($path_file_csv): Collection
    {
        $file = fopen($path_file_csv, "r");

        $result = collect();
        $row = 0;
        $row_header = 0;
        $data_header = [];
        while (!feof($file)) {
            $data_row = fgetcsv($file);
            if ($row == $row_header) {
                $data_header = $this->convertChartsetString($data_row);
                // $data_header = $data_row;
            } elseif ($row > $row_header) {
                $data = $this->convertToDataInsert($data_header, $data_row);
                if (!empty($data)) {
                    $result->push($data);
                }
            }
            $row++;
        }

        return $result;
    }

    /**
     * convertToDataInsert
     *
     * @param array $list_column : list_column will insert into database
     * @param array|bool $data_row : data in file csv (1 row)
     * @return array
     * @throws Exception
     */
    private function convertToDataInsert(array $list_column = [], $data_row = false): array
    {
        if (!$data_row) {
            return [];
        }
        $data = [];
        if (count($list_column) != 0) {
            if (count($list_column) !== count($data_row)) {
                throw new ModelNotFoundException(trans('messages.import_csv.wrong_file_format'));
//                throw new \Exception(
//                    'Data not enough:'
//                    . ' [Length column in DB - ' . count($list_column) . '] ,'
//                    . ' [Data in csv - ' . count($data_row) . ']'
//                    . ' [Data in row - ' . json_encode($data_row) . ']'
//                );
            }
            $i = 1;
            foreach ($list_column as $index => $column) {
                $column = trim($column);
                if ( isset($data[$column]) ) {
                    $column = $column . '_' . $i;
                    $i++;
                }
                $str = $this->convertChartsetString($data_row[$index]);
                $data[$column] = $str;
            }
        }
        return $data;
    }

    /**
     * convertChartsetCode
     *
     * @param string string
     * @return string
     * @throws Exception
     */
    private function convertChartsetString($values): array|string {
        if(is_array($values)){
            $results = [];
            foreach($values as $value){
                $results[] = mb_convert_encoding($value, "UTF-8", "SJIS");
            }
            return $results;
        }
        return mb_convert_encoding($values, "UTF-8", "SJIS");
    }
}
