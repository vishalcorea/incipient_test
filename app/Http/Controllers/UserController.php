<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\HeadingRowImport;
use Validator;

class UserController extends Controller
{
    function list() {
        return view('users');
    }
    public function import(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'users_file' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'fail', 'error' => $validator->errors()]);

        }

        // Check for Heading row
        $headings = (new HeadingRowImport)->toArray(request()->file('users_file'));

        $file_head = $headings[0][0];
        $head = ['first_name', 'last_name', 'email', 'password', 'phone_number', 'gender', 'address', 'biography'];
        $check_header = empty(array_diff($file_head, $head));

        if (!$check_header) {
            return response()->json(['status' => 'fail', 'error' => 'Header row missing']);
        }

        //Import Users
        $import = new UsersImport();
        $import->import(request()->file('users_file'));

        $row_count = file(request()->file('users_file'), FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);

        //Import Errors
        $row = [];
        foreach ($import->failures() as $failure) {
            $row[$failure->row()][$failure->attribute()] = [$failure->errors()];
            $row[$failure->row()]['value'] = [$failure->values()];
            $failure->values();
            $failure->attribute();
            $failure->errors();
            $failure->values();
        }
        return response()->json(["status" => "success", "msg" => "file Uploaded sucessfully", "imported_row" => $import->getRowCount(), "total_row" => count($row_count), "data" => $row]);
    }
}
