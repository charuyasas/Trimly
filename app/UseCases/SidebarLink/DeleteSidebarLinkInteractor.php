<?php

namespace App\UseCases\SidebarLink;

use App\Models\SidebarLink;

class DeleteSidebarLinkInteractor
{
    public function execute(SidebarLink $sidebarLink): ?bool
    {
        return $sidebarLink->delete();
    }
}
