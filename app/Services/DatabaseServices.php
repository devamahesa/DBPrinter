<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use PhpOffice\PhpWord\Settings;

class DatabaseServices
{
    public static function SetConnectionToDB($data)
    {
        $data = (object)$data;
        $driver = $data->driver;
        switch ($driver) {
            case 'sqlsrv':
                DB::disconnect('oracle');
                Config::set("database.connections.sqlsrv", [
                    'driver' => $data->driver,
                    'url' =>  "",
                    'host' => $data->host,
                    'port' =>  $data->port,
                    'database' =>  "",
                    'username' => $data->username,
                    'password' =>  $data->password,
                    'charset' => 'utf8',
                    'prefix' => '',
                    'prefix_indexes' => true,
                    'encrypt' => 'yes',
                    'trust_server_certificate' => 'true'
                ]);
                break;

            case 'oracle':
                DB::disconnect('sqlsrv');
                Config::set("database.connections.oracle", [
                    'driver' => $data->driver,
                    'host' => $data->host,
                    'port' =>  $data->port,
                    'database' =>  "",
                    'service_name' => $data->service_name,
                    'username' => $data->username,
                    'password' =>   $data->password,
                    'charset' => 'AL32UTF8',
                    'prefix' => "",
                    'prefix_schema' => "",
                    'server_version' => '21c',
                    'load_balance' => 'no'
                ]);
                break;
        }
        DB::purge($data->driver);
        DB::setDefaultConnection($data->driver);
        $conn = DB::connection($data->driver);
        return $conn;
    }

    public static function getAllDB($conn, $dbdriver)
    {
        switch ($dbdriver) {
            case 'sqlsrv':
                $querydb = $conn->select('select name from sys.databases');
                break;
            case 'oracle':
                $querydb = $conn->select('select username as schema FROM all_users order by username');
                break;
        }
        return $querydb;
    }
    public static function getAllTable($conn, $dbdriver, $database)
    {
        $table_name = [];
        if ($dbdriver === 'sqlsrv') {
            $conn->statement('use ' . $database);
            $table = $conn->select('select TABLE_NAME from ' . $database . '.INFORMATION_SCHEMA.TABLES');
            foreach ($table as $key => $value) {
                $table_name[] = $value->TABLE_NAME;
            }
        }
        if ($dbdriver === 'oracle') {
            $table = $conn->select('select table_name from all_tables where owner=' . '\'' . $database . '\'');
            foreach ($table as $key => $value) {
                $table_name[] = $value->table_name;
            }
        }
        return $table_name;
    }

    public static function getAllColumns($dbdriver, $db, $connection, $table_name)
    {
        //querying all columns for each table and schema name
        switch ($dbdriver) {
            case 'sqlsrv':
                $connection->statement('use ' . $db);
                $columns = $connection->select(
                    'select distinct
                    AC.[name] as [Field],
                    TY.[name] as Type,
                    CASE WHEN AC.is_nullable=0 THEN \'NO\' ELSE \'YES\' END as [Null],
                    CASE WHEN TC.CONSTRAINT_TYPE=\'PRIMARY KEY\' THEN \'YES\' ELSE \'NO\' END as [Key],
                    object_definition(TY.default_object_id) as [Default],
                    SEP.value [Description]
                    
                    from sys.[tables] as T
                    inner join sys.[all_columns] AC on T.[object_id]=AC.[object_id]
                    inner join sys.[types] TY ON AC.[system_type_id]=TY.[system_type_id] AND AC.[user_type_id]=TY.[user_type_id]
                    left join INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE CC ON AC.[name]=CC.COLUMN_NAME
                    left join INFORMATION_SCHEMA.TABLE_CONSTRAINTS TC ON CC.CONSTRAINT_NAME=TC.CONSTRAINT_NAME
                    left join sys.extended_properties SEP on T.[object_id]=SEP.[major_id] and AC.[column_id]=SEP.minor_id and SEP.name=\'MS_Description\'
                    where T.[name]=' . '\'' . $table_name . '\''
                );
                break;

            case 'oracle':
                $columns = $connection->select(
                    'SELECT
                    col.COLUMN_NAME FIELD,
                    col.DATA_TYPE "TYPE",
                    CASE WHEN col.NULLABLE=\'N\' THEN \'NO\' ELSE \'YES\' END "NULL",
                    CASE WHEN acons.CONSTRAINT_TYPE=\'P\' THEN \'YES\' ELSE \'NO\' END "KEY",
                    col.DATA_DEFAULT "DEFAULT",
                    com.COMMENTS DESCRIPTION
                    from all_tab_columns col
                    left join all_col_comments com ON col.TABLE_NAME = com.TABLE_NAME AND col.COLUMN_NAME=com.COLUMN_NAME 
                    left JOIN all_cons_columns acoms ON col.COLUMN_NAME = acoms.COLUMN_NAME AND col.TABLE_NAME = acoms.TABLE_NAME
                    LEFT JOIN all_constraints acons ON acoms.constraint_name = acons.CONSTRAINT_NAME
                    WHERE col.TABLE_NAME=' . '\'' . $table_name . '\'' . 'AND col.owner=' . '\'' . $db . '\'' . '
                    AND com.OWNER = col.OWNER ORDER BY col.COLUMN_NAME'
                );
                break;
        }
        return $columns;
    }

    public static function print($dbname, $table_name, $columns, $format)
    {
        //method for print document
        $format_title_db = [
            'name' => 'calibri',
            'bold' => true,
            'size' => 14
        ];
        $format_table_name = [
            'name' => 'calibri',
            'bold' => true,
            'size' => 12
        ];
        $format_body = [
            'name' => 'calibri',
            'size' => 12
        ];
        $table_format = [
            'borderSize' => 6,
            'borderColor' => '000000'
        ];
        $header = ["NO", "FIELD", "TYPE", "NULL", "KEY", 'DEFAULT', "DESCRIPTION"];

        $phpword = new \PhpOffice\PhpWord\PhpWord();
        $section1 = $phpword->addSection();
        $section1->addText('Database Name : ' . $dbname, $format_title_db);
        $section1->addText('<w:br/>');
        $table = $section1->addTable();
        if (count($table_name) == 0) {
            $section1->addText('No contain table <w:br/>', $format_body);
        } else {
            for ($i = 0; $i < count($table_name); $i++) { //loop for printing whole table
                $section1->addText('Table Name : ' . $table_name[$i], $format_table_name);
                if (count($columns[$i]) == 0) { //if no contain column in the table, then print title only and continue to next table 
                    $section1->addText('No contain column <w:br/>', $format_body);
                } else {
                    $table = $section1->addTable($table_format);
                    $table->addRow();
                    foreach ($header as $index => $value) { //loop for print header
                        $table->addCell(2000)->addText($value, $format_body);
                    }
                    for ($j = 0; $j < count($columns[$i]); $j++) { //loop for adding row and number
                        $table->addRow();
                        $table->addCell(2000)->addText(str($j + 1), $format_body);
                        foreach ($columns[$i][$j] as $parameter => $value) { //loop for adding column
                            $table->addCell(2000)->addText($value, $format_body);
                        }
                    }
                }
                $section1->addText('<w:br/>');
            }
        }
        Settings::setZipClass(Settings::PCLZIP);
        switch ($format) {
            case 'docx':
                $writer = 'Word2007';
                Settings::setCompatibility(false);
                break;

            case 'pdf':
                $writer = 'PDF';
                $domPdfPath = base_path('/vendor/dompdf/dompdf');
                Settings::setPdfRendererPath($domPdfPath);
                Settings::setPdfRendererName('DomPDF');
                break;
        }
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpword, $writer);
        if (ob_get_contents()) ob_end_clean();
        $objWriter->save($dbname . '.' . $format);
        return response()->download(public_path($dbname . '.' . $format));
    }
}