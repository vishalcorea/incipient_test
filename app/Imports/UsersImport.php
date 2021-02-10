<?php

namespace App\Imports;

use App\Models\User;
use Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToModel, WithValidation, WithBatchInserts, WithHeadingRow, SkipsOnFailure
{
    use Importable, SkipsFailures, SkipsErrors;

    private $rows = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        ++$this->rows;
        return new User([
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'email' => $row['email'],
            'password' => Hash::make($row['password']),
            'phone' => $row['phone_number'],
            'gender' => $row['gender'],
            'address' => $row['address'],
            'biography' => $row['biography'],
        ]);
    }
    public function rules(): array
    {
        return [
            'email' => Rule::unique('users'),
            'phone_number' => 'regex:/[0-9]{10}/',
        ];
    }

    public function batchSize(): int
    {
        return 500;
    }
    public function getRowCount(): int
    {
        return $this->rows;
    }
}
