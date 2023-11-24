<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Services\DatabaseServices;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Input;
use App\Exceptions\Handler;
use Yajra\Pdo\Oci8;
use OCICollection;
use OCILob;
use PDO;
use PDOStatement;
use Yajra\Pdo\Oci8\Exceptions\Oci8Exception;
use Yajra\Pdo\Oci8\Statement;

class DatabaseController extends Controller
{

    public function formlogin()
    {
        return view('dbdriverlogin');
    }

    public function authenticate(Request $data)
    {
        $conn = DatabaseServices::SetConnectionToDB($data);
        //VALIDATION FOR DATABASE INPUT LOGIN
        //if input is false
        try {
            $conn->getPdo();
        } catch (Exception $error) {
            return Redirect::back()->withInput()->withError($error->getMessage());
        }
        //if input is true
        Session::put([
            'driver' => $data->driver,
            'host' => $data->host,
            'port' => $data->port,
            'service_name' => $data->service_name,
            'username' => $data->username,
            'password' => $data->password
        ]);
        return redirect()->to('listdb');
    }

    public function chooseDB()
    {
        //viewing all database in specific driver
        $conn = DatabaseServices::SetConnectionToDB(Session::all());
        $database_driver = Session::get('driver');
        $querydb = DatabaseServices::getAllDB($conn, $database_driver);
        return view('listdb', compact('querydb', 'database_driver'));
    }

    public function showTable(Request $request)
    {
        if ((count($request->toArray()) < 3)) {
            return Redirect::back()->withinput()->withErrors(['msg' => 'Masukkan database yang valid']);
        } else {
            Session::put([
                'db_printed' => $request->db_printed
            ]);
            return redirect()->to('printedtable');
        }
    }

    public function chooseTable()
    {
        $conn = DatabaseServices::SetConnectionToDB(Session::all());
        $table_name = DatabaseServices::getAllTable($conn, Session::get('driver'), session::get('db_printed'));
        return view('tableprint', compact('table_name'));
    }

    public function get_tables_columns(Request $data)
    {
        if (count($data->toArray()) < 3) {
            return Redirect::back()->withinput()->withErrors(['msg' => 'Pilih tabel dan ekstensi yang valid']);
        } else {
            $conn = DatabaseServices::SetConnectionToDB(Session::all());
            $data = $data->toArray();
            $table_name = $data['list'];
            for ($i = 0; $i < count($table_name); $i++) {
                $columns[$i] = DatabaseServices::getAllColumns(session::get('driver'), session::get('db_printed'), $conn, $table_name[$i]);
            }
            return DatabaseServices::print(session::get('db_printed'), $table_name, $columns, $data['export_ext']);
        }
        //method for get all table and columns, and return to printing document method
    }
}