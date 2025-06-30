<?php 

namespace App\UseCases\Service\Requests;

use Spatie\LaravelData\Data;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Spatie\LaravelData\Attributes\Validation\Max;

class ServiceRequest extends Data{

    public ?string $id;
    public string $code;
    #[SpatieRule('required')]
    #[Max(255)]
    public string $description;
    #[SpatieRule('required')]
    public string $price;

    public static function rules(): array
    {
        return [
            'code' => [
                'required',
                Rule::unique('services', 'code')->ignore(request()->input('id')),
            ],
        ];
    }
    
}