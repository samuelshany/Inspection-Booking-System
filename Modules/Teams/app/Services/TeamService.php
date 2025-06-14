<?php

namespace Modules\Teams\Services;

use Modules\Teams\Models\Team;

class TeamService
{
    public function createTeam(array $data): Team
    {
        return Team::create($data);
    }

    public function updateTeam(Team $team, array $data): Team
    {
        $team->update($data);
        return $team->fresh();
    }

    public function deleteTeam(Team $team): bool
    {
        return $team->delete();
    }
}
