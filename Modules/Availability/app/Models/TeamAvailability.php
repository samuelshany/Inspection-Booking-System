<?php

namespace Modules\Availability\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Teams\Models\Team;

class TeamAvailability extends Model
{


    protected $fillable = [
        'team_id',
        'booking_date',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [

        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
