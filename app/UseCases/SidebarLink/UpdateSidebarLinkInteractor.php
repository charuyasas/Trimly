<?php

namespace App\UseCases\SidebarLink;

use App\Models\SidebarLink;

class UpdateSidebarLinkInteractor
{
    public function execute(SidebarLink $sidebarLink, $data)
    {
        $sidebarLink->update($data->toArray());
        return $sidebarLink;
    }
}
