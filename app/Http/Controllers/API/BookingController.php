<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\UseCases\Booking\Requests\BookingRequest;
use App\UseCases\Booking\ListBookingInteractor;
use App\UseCases\Booking\StoreBookingInteractor;
use App\UseCases\Booking\ShowBookingInteractor;
use App\UseCases\Booking\UpdateBookingInteractor;
use App\UseCases\Booking\DeleteBookingInteractor;
use App\UseCases\Booking\CalendarBookingInteractor;
use App\UseCases\Booking\CalendarEmployeeInteractor;


class BookingController extends Controller
{
    public function index(ListBookingInteractor $list)
    {
        return response()->json($list->execute());
    }

    public function store(StoreBookingInteractor $store)
    {
        $data = BookingRequest::validateAndCreate(request());
        $result = $store->execute($data);

        if (isset($result['error'])) {
            return response()->json(['message' => 'Time slot already booked.'], 409);
        }

        return response()->json(['message' => 'Booking created', 'data' => $result], 201);
    }

    public function show(Booking $booking, ShowBookingInteractor $show)
    {
        return response()->json($show->execute($booking));
    }

   public function update(Booking $booking, UpdateBookingInteractor $update)
    {
        $data = BookingRequest::validateAndCreate(request());

        $updated = $update->execute($booking, $data);

        return response()->json(['message' => 'Booking updated', 'data' => $updated]);
    }

    public function destroy(Booking $booking, DeleteBookingInteractor $delete)
    {
        $delete->execute($booking);
        return response()->json(['message' => 'Booking deleted']);
    }

    public function calendarBookings(CalendarBookingInteractor $calendarBooking)
    {
        $result = $calendarBooking->execute();
        return response()->json($result);
    }

    public function calendarEmployees(CalendarEmployeeInteractor $calendarEmployee)
    {
        $result = $calendarEmployee->execute();
        return response()->json($result);
    }
}
