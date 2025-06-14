<?php

namespace Modules\Availability\Services;

use Modules\Availability\Models\TeamAvailability;
use Modules\Teams\Models\Team;

class AvailabilityService
{
    public function setTeamAvailability(Team $team, array $availabilities): array
    {
        // Delete existing availabilities
        $team->availabilities()->delete();

        $createdAvailabilities = [];

        foreach ($availabilities as $availability) {
            $createdAvailabilities[] = TeamAvailability::create([
                'team_id' => $team->id,
                'day_of_week' => $availability['day_of_week'],
                'start_time' => $availability['start_time'],
                'end_time' => $availability['end_time'],
                'is_active' => true,
            ]);
        }

        return $createdAvailabilities;
    }
}
