<?php

namespace Modules\Booking\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Booking\Http\Requests\BookingRequest;
use Modules\Booking\Models\Booking;
use Modules\Booking\Services\BookingService;

class BookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Booking::with(['team', 'user'])
            ->orderBy('booking_date', 'desc');

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $bookings = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $bookings
        ]);
    }

    public function store(BookingRequest $request): JsonResponse
    {
       

        try {
            $booking = $this->bookingService->createBooking(
                $request->user(),
                $request->validated()
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Booking created successfully',
                'data' => $booking->load(['team', 'user'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(Booking $booking): JsonResponse
    {
        try {
            $this->bookingService->cancelBooking($booking);

            return response()->json([
                'status' => 'success',
                'message' => 'Booking cancelled successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
