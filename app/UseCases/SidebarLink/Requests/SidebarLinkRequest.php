<?php

namespace App\UseCases\SidebarLink\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Exists;

class SidebarLinkRequest extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $display_name,

        #[Required, StringType, Max(255)]
        public string $url,

        #[Nullable, StringType, Max(255)]
        public ?string $icon_path,

        #[Nullable, Exists('sidebar_links', 'id')]
        public ?int $parent_id,

        #[Nullable, StringType, Max(255)]
        public ?string $permission_name,
    ) {}
} 