<?php

namespace Modules\Availability\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Availability\Services\AvailabilityService;
use Modules\Teams\Models\Team;

class AvailabilityController extends Controller
{
    public function __construct(
        private AvailabilityService $availabilityService
    ) {}

    public function setTeamAvailability(Request $request, Team $team): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            
            'availabilities' => 'required|array',
            'availabilities.*.day_of_week' => 'required|integer|between:0,6',
            'availabilities.*.start_time' => 'required|date_format:H:i',
            'availabilities.*.end_time' => 'required|date_format:H:i|after:availabilities.*.start_time',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $availabilities = $this->availabilityService
            ->setTeamAvailability($team, $request->availabilities);

        return response()->json([
            'status' => 'success',
            'message' => 'Team availability set successfully',
            'data' => $availabilities
        ]);
    }
}
