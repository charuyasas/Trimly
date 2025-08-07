<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\UseCases\UserShift\GetOngoingShiftUserDetailsInteractor;
use App\UseCases\UserShift\GetUserShiftDetailsInteractor;
use App\UseCases\UserShift\LoadShiftIDDropdownInteractor;
use App\UseCases\UserShift\Requests\ShiftDetailsRequest;
use App\UseCases\UserShift\ShowUserShiftEndDetailsInteractor;
use App\UseCases\UserShift\StartUserShiftInteractor;
use App\UseCases\UserShift\EndUserShiftInteractor;
use Illuminate\Http\JsonResponse;

class ShiftController extends Controller
{

    public function getOngoingShiftDetails(GetOngoingShiftUserDetailsInteractor $getOngoingShiftUserDetailsInteractor): JsonResponse
    {
        $userJson = $getOngoingShiftUserDetailsInteractor->execute();
        $userData = json_decode($userJson, true);
        return response()->json($userData);
    }

    public function startUserShift(StartUserShiftInteractor $startUserShiftInteractor, ShiftDetailsRequest $shiftDetailsRequest, GetOngoingShiftUserDetailsInteractor $getOngoingShiftUserDetailsInteractor): JsonResponse
    {
        $userData = json_decode($getOngoingShiftUserDetailsInteractor->execute(), true);
        if($userData != null){
            $shift = NULL;
            $reponse = 'Already logged by another cashier.';
        }else{
            $shift = $startUserShiftInteractor->execute($shiftDetailsRequest);
            $reponse = 'Shift started successfully';
        }
        return response()->json([
            'message' => $reponse,
            'data' => $shift
        ]);
    }

    public function showUserShiftEndDetails(ShowUserShiftEndDetailsInteractor $showUserShiftEndDetailsInteractor): JsonResponse
    {
        return $showUserShiftEndDetailsInteractor->execute(auth()->id());
    }

    public function endUserShift(ShiftDetailsRequest $shiftDetailsRequest, EndUserShiftInteractor $endUserShiftInteractor): JsonResponse {
        try {
            $data = $endUserShiftInteractor->execute($shiftDetailsRequest);

            return response()->json([
                'success' => true,
                'message' => 'Shift closed successfully.',
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to close shift.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function loadShiftIDDropdown(LoadShiftIDDropdownInteractor $loadShiftIDDropdownInteractor): JsonResponse
    {
        return response()->json($loadShiftIDDropdownInteractor->execute());
    }

    public function loadShiftDetails(int $shiftID, GetUserShiftDetailsInteractor $getUserShiftDetailsInteractor): JsonResponse
    {
        return response()->json($getUserShiftDetailsInteractor->execute($shiftID));
    }

}
