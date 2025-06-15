<?php

namespace Modules\Availability\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Availability\Services\TimeSlotService;
use Modules\Teams\Models\Team;

class TimeSlotController extends Controller
{
    public function __construct(
        private TimeSlotService $timeSlotService
    ) {}

    public function generateSlots(Request $request, Team $team): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required|date|after_or_equal:today',
            'to' => 'required|date|after:from',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $slots = $this->timeSlotService->generateTimeSlots(
            $team,
            $request->from,
            $request->to
        );

        return response()->json([
            'status' => 'success',
            'data' => [
                'team' => $team->name,
                'date_range' => [
                    'from' => $request->from,
                    'to' => $request->to,
                ],
                'slots' => $slots,
                'total_slots' => count($slots),
            ]
        ]);
    }
}
