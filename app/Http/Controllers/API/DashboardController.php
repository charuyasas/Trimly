<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\UseCases\Booking\ListBookingInteractor;
use App\UseCases\Customer\ListCustomerInteractor;
use App\UseCases\Employee\ListEmployeeInteractor;
use App\UseCases\Invoice\GetDailySalesInteractor;
use App\UseCases\Item\ListItemInteractor;
use App\UseCases\Service\ListServiceInteractor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function dailySales(Request $request, GetDailySalesInteractor $getDailySalesInteractor)
    {
        $startDate = $request->query('start');
        $endDate   = $request->query('end');

        $sales = $getDailySalesInteractor->execute($startDate, $endDate);

        return response()->json($sales);
    }

    public function todayBookings(ListBookingInteractor $listBookingInteractor): JsonResponse
    {
        $today = Carbon::today()->toDateString();
        $bookings = $listBookingInteractor->execute();

        $todayBookings = $bookings->where('booking_date', $today)
            ->sortBy('start_time')
            ->values();

        return response()->json($todayBookings);
    }

    public function getSummaryCounts(ListEmployeeInteractor $listEmployeeInteractor, ListCustomerInteractor $listCustomerInteractor, ListServiceInteractor $listServiceInteractor, ListItemInteractor $listItemInteractor): JsonResponse
    {
        $employees = $listEmployeeInteractor->execute();
        $employeeCount = $employees->count();

        $customers = $listCustomerInteractor->execute();
        $customerCount = $customers->count();

        $items = $listItemInteractor->execute();
        $itemCount = $items->count();

        $services = $listServiceInteractor->execute();
        $serviceCount = $services->count();

        return response()->json([
            'employeeCount' => $employeeCount,
            'customerCount' => $customerCount,
            'itemCount' => $itemCount,
            'serviceCount' => $serviceCount,
        ]);
    }

}
