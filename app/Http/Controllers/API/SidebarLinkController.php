<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\SidebarLink;
use App\UseCases\SidebarLink\ListSidebarLinkInteractor;
use App\UseCases\SidebarLink\StoreSidebarLinkInteractor;
use App\UseCases\SidebarLink\UpdateSidebarLinkInteractor;
use App\UseCases\SidebarLink\DeleteSidebarLinkInteractor;
use App\UseCases\SidebarLink\Requests\SidebarLinkRequest;

class SidebarLinkController extends Controller
{
    public function index(Request $request, ListSidebarLinkInteractor $listSidebarLinkInteractor): JsonResponse
    {
        $user = $request->user();
        return response()->json($listSidebarLinkInteractor->execute($user));
    }

    public function store(Request $request, StoreSidebarLinkInteractor $storeSidebarLinkInteractor): JsonResponse
    {
        $user = $request->user();
        if (!$user->can('manage sidebar')) {
            abort(403);
        }
        $validated = SidebarLinkRequest::validate($request->all());
        $sidebarLinkRequest = SidebarLinkRequest::from($validated);
        $sidebarLink = $storeSidebarLinkInteractor->execute($sidebarLinkRequest);
        return response()->json($sidebarLink, 201);
    }

    public function update(Request $request, $id, UpdateSidebarLinkInteractor $updateSidebarLinkInteractor): JsonResponse
    {
        $user = $request->user();
        if (!$user->can('manage sidebar')) {
            abort(403);
        }
        $sidebarLink = SidebarLink::findOrFail($id);
        $validated = SidebarLinkRequest::validate(array_merge($sidebarLink->toArray(), $request->all()));
        $sidebarLinkRequest = SidebarLinkRequest::from($validated);
        $updated = $updateSidebarLinkInteractor->execute($sidebarLink, $sidebarLinkRequest);
        return response()->json($updated);
    }

    public function destroy(Request $request, $id, DeleteSidebarLinkInteractor $deleteSidebarLinkInteractor): JsonResponse
    {
        $user = $request->user();
        if (!$user->can('manage sidebar')) {
            abort(403);
        }
        $sidebarLink = SidebarLink::findOrFail($id);
        $deleteSidebarLinkInteractor->execute($sidebarLink);
        return response()->json(null, 204);
    }
}
