<?php

namespace App\UseCases\PostingAccount\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class PostingAccountRequest extends Data
{
    #[Rule(['required', 'string', 'max:255'])]
    public string $posting_account;

    #[Rule(['required', 'integer', new Exists('main_accounts', 'main_code')])]
    public int $main_code;

    #[Rule(['required', 'integer', new Exists('heading_accounts', 'heading_code')])]
    public int $heading_code;

    #[Rule(['required', 'integer', new Exists('title_accounts', 'title_code')])]
    public int $title_code;

}
