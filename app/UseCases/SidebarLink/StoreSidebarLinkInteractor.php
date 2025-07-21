<?php

namespace App\UseCases\SidebarLink;

use App\Models\SidebarLink;
use Spatie\Permission\Models\Permission;
use App\UseCases\SidebarLink\Requests\SidebarLinkRequest;

class StoreSidebarLinkInteractor
{
    public function execute(SidebarLinkRequest $data)
    {
        $array = $data->toArray();
        if (!empty($array['permission_name'])) {
            Permission::firstOrCreate(['name' => $array['permission_name']]);
        }
        return SidebarLink::query()->create($array);
    }
}
