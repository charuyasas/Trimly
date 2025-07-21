<?php

namespace App\UseCases\SidebarLink;

use App\Models\SidebarLink;

class ListSidebarLinkInteractor
{
    public function execute($user)
    {
        $allLinks = SidebarLink::with('children')->get();
        $allowed = $allLinks->filter(function ($link) use ($user) {
            return $link->url === '#' || $user->can($link->permission_name);
        });
        // Build tree
        function buildTree($links, $parentId = null) {
            return $links->where('parent_id', $parentId)->map(function ($link) use ($links) {
                $children = buildTree($links, $link->id);
                return [
                    'id' => $link->id,
                    'display_name' => $link->display_name,
                    'url' => $link->url,
                    'icon_path' => $link->icon_path,
                    'children' => $children->values(),
                ];
            })->values();
        }
        return buildTree($allowed);
    }
} 