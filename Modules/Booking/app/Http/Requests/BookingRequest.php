<?php

namespace Modules\Booking\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Allow all authenticated users (adjust if needed)
        return true;
    }

    public function rules(): array
    {
        return [
            'team_id' => 'required|exists:teams,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
            'notes' => 'nullable|string|max:500',
        ];
    }
}
