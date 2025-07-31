<?php
namespace App\UseCases\Employee;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;

class ListEmployeeInteractor {

    public function execute(): Collection
    {
        return Employee::all();
    }

 }
