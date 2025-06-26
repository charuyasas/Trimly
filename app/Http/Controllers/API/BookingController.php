<?php

namespace App\Http\Controllers\API;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Carbon\Carbon;


class BookingController extends Controller
{
    public function index()
    {
        return Booking::with(['customer', 'employee', 'service'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'   => 'required|exists:customers,id',
            'employee_id'   => 'required|exists:employees,id',
            'service_id'    => 'required|exists:services,id',
            'booking_date'  => 'required|date',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
            'status'        => ['nullable', Rule::in(['pending', 'confirmed', 'completed', 'cancelled'])],
            'notes'         => 'nullable|string',
        ]);

        // Check for overlapping bookings
        $overlap = Booking::where('employee_id', $validated['employee_id'])
            ->where('booking_date', $validated['booking_date'])
            ->where('status', '!=', 'cancelled') // ignore cancelled bookings
            ->where(function ($q) use ($validated) {
                $q->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                  ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                  ->orWhere(function ($query) use ($validated) {
                      $query->where('start_time', '<', $validated['start_time'])
                            ->where('end_time', '>', $validated['end_time']);
                  });
            })->exists();

        if ($overlap) {
            $nextStart = date("H:i", strtotime($validated['end_time']));
            $nextEnd = date("H:i", strtotime("+30 minutes", strtotime($nextStart)));
            return response()->json([
                'message' => 'Time slot is already booked.',
                'suggested_start_time' => $nextStart,
                'suggested_end_time' => $nextEnd
            ], 409);
        }

        $booking = Booking::create($validated);
        return response()->json($booking->load(['customer', 'employee', 'service']), 201);
    }

    public function show($id)
    {
        return Booking::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Validate only present fields (partial update allowed)
        $validated = $request->validate([
        'customer_id' => 'sometimes|required|exists:customers,id',
        'employee_id' => 'sometimes|required|exists:employees,id',
        'service_id' => 'sometimes|required|exists:services,id',
        'booking_date' => 'sometimes|required|date',
        'start_time' => 'sometimes|required',
        'end_time' => 'sometimes|required|after:start_time',
        'status' => 'sometimes|required|in:pending,confirmed,completed,cancelled',
        'notes' => 'nullable|string'
        ]);

        // Optional: if updating time, check for overlaps
        if (
         isset($validated['employee_id']) &&
         isset($validated['booking_date']) &&
         isset($validated['start_time']) &&
         isset($validated['end_time'])
        )
        {
         $startTime = Carbon::parse($validated['start_time'])->format('H:i');
         $endTime = Carbon::parse($validated['end_time'])->format('H:i');

         $conflict = Booking::where('employee_id', $validated['employee_id'])
            ->where('booking_date', $validated['booking_date'])
            ->where('id', '!=', $booking->id)
            ->where('status', '!=', 'cancelled') // ignore cancelled
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->first();

         if ($conflict) {
            $nextStart = Carbon::parse($conflict->end_time);
            $nextEnd = $nextStart->copy()->addMinutes(
                Carbon::parse($endTime)->diffInMinutes(Carbon::parse($startTime))
            );

            return response()->json([
                'message' => 'Time slot overlaps with another booking.',
                'suggested_start_time' => $nextStart->format('H:i'),
                'suggested_end_time' => $nextEnd->format('H:i'),
            ], 409);
          }

         // override validated times
         $validated['start_time'] = $startTime;
         $validated['end_time'] = $endTime;
       }

       // Update the booking
       $booking->update($validated);

       return response()->json(['message' => 'Booking updated successfully']);
    }

}
