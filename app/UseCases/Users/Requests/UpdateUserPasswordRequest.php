<?php

namespace App\UseCases\Users\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\{Required, Min, Rule};

class UpdateUserPasswordRequest extends Data
{
    public function __construct(
        #[Required]
        public int $id,

        #[Required]
        public string $oldPassword,

        #[Required, Rule('string'), Min(6)]
        public string $newPassword,
    ) {}

    public static function fromValidated(array $data): self
    {
        return self::from(self::validate($data));
    }
}

