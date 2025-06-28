<?php 

namespace App\UseCases\Employee\Requests;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class EmployeeRequest extends Data{

    public ?string $id;
    #[Rule('required')]
    #[Unique(table: 'employees', column:'employee_id', ignoreColumn:'id',)]
    public string $employee_id;
    #[Rule('required')]
    #[Max(255)]
    public string $name;
    #[Rule('required')]
    #[Max(255)]
    public string $address;
    #[Rule('required','digits:10')]
    public string $contact_no;
}