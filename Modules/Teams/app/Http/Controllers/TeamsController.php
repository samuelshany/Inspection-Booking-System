<?php

namespace Modules\Teams\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Teams\Models\Team;
use Modules\Teams\Services\TeamService;

class TeamsController extends Controller
{
    public function __construct(
        private TeamService $teamService
    ) {}

    public function index(): JsonResponse
    {
        $teams = Team::with('availabilities')->get();

        return response()->json([
            'status' => 'success',
            'data' => $teams
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $team = $this->teamService->createTeam($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Team created successfully',
            'data' => $team
        ], 201);
    }

    public function show(Team $team): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $team->load('availabilities')
        ]);
    }
}
