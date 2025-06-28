<?php 

namespace App\UseCases\Service\Requests;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class ServiceRequest extends Data{

    public ?string $id;
    #[Rule('required')]
    #[Unique('services','code')]
    public string $code;
    #[Rule('required')]
    #[Max(255)]
    public string $description;
    #[Rule('required')]
    public string $price;
    
}