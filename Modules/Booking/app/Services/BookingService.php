<?php

namespace Modules\Booking\Services;

use App\Models\User;
use Carbon\Carbon;
use Modules\Booking\Models\Booking;
use Modules\Teams\Models\Team;

class BookingService
{
    public function createBooking(?User $user, array $data): Booking
    {
        $team = Team::findOrFail($data['team_id']);

        // Validate time slot availability
        $this->validateTimeSlot($team, $data);

        // Check for conflicts
        $this->checkForConflicts($team, $data);

        return Booking::create([
            'user_id' => $user->id??$data['user'],
            'team_id' => $data['team_id'],
            'booking_date' => $data['booking_date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'notes' => $data['notes'] ?? null,
            'status' => 'confirmed',
        ]);
    }

    public function cancelBooking(Booking $booking): bool
    {
        if ($booking->status === 'cancelled') {
            throw new \Exception('Booking is already cancelled');
        }

        $booking->update(['status' => 'cancelled']);
        return true;
    }

    private function validateTimeSlot(Team $team, array $data): void
    {
        $bookingDate = Carbon::parse($data['booking_date']);
        $dayOfWeek = $bookingDate->dayOfWeek;

        $availability = $team->availabilities()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$availability) {
            throw new \Exception('Team is not available on this day');
        }

        // Combine booking date with submitted start and end time
        $startTime = Carbon::parse("{$data['booking_date']} {$data['start_time']}");
        $endTime = Carbon::parse("{$data['booking_date']} {$data['end_time']}");


        $startHour =  Carbon::parse($availability->start_time)->format('H:i:s');
        $endHour =  Carbon::parse($availability->end_time)->format('H:i:s');
        // Combine booking date with availability start and end times
        $availabilityStart = Carbon::parse("{$data['booking_date']} {$startHour}");
        $availabilityEnd = Carbon::parse("{$data['booking_date']} {$endHour}");
       // dd($availabilityStart, $availabilityEnd, $startTime, $endTime);
        if ($startTime->lt($availabilityStart) || $endTime->gt($availabilityEnd)) {
            throw new \Exception('Booking time is outside team availability');
        }
    }

    private function checkForConflicts(Team $team, array $data): void
    {
        $existingBookings = Booking::where('team_id', $team->id)
            ->where('booking_date', $data['booking_date'])
            ->where(function ($query) use ($data) {
                $query->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                    ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']]);
            })
            ->exists();

        if ($existingBookings) {
            throw new \Exception('This time slot is already booked');
        }
    }
    public function getBookingsForTeam(Team $team, Carbon $startDate, Carbon $endDate): \Illuminate\Database\Eloquent\Collection
    {
        return Booking::where('team_id', $team->id)
            ->whereBetween('booking_date', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->with(['user', 'team'])
            ->get();
    }
    public function getBookingsForUser(User $user, Carbon $startDate, Carbon $endDate): \Illuminate\Database\Eloquent\Collection
    {
        return Booking::where('user_id', $user->id)
            ->whereBetween('booking_date', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->with(['team'])
            ->get();
    }
    public function getBookingById(int $bookingId): ?Booking
    {
        return Booking::find($bookingId);
    }
    public function updateBooking(Booking $booking, array $data): Booking
    {
        // Validate time slot availability
        $this->validateTimeSlot($booking->team, $data);

        // Check for conflicts
        $this->checkForConflicts($booking->team, $data);

        $booking->update([
            'booking_date' => $data['booking_date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'notes' => $data['notes'] ?? null,
        ]);

        return $booking;
    }
    public function getBookingsByDate(Carbon $date): \Illuminate\Database\Eloquent\Collection
    {
        return Booking::whereDate('booking_date', $date->toDateString())
            ->with(['user', 'team'])
            ->get();
    }
    public function getBookingsByUserAndDate(User $user, Carbon $date): \Illuminate\Database\Eloquent\Collection
    {
        return Booking::where('user_id', $user->id)
            ->whereDate('booking_date', $date->toDateString())
            ->with(['team'])
            ->get();
    }
    public function getBookingsByTeamAndDate(Team $team, Carbon $date): \Illuminate\Database\Eloquent\Collection
    {
        return Booking::where('team_id', $team->id)
            ->whereDate('booking_date', $date->toDateString())
            ->with(['user'])
            ->get();
    }
    public function getBookingsByStatus(string $status): \Illuminate\Database\Eloquent\Collection
    {
        return Booking::where('status', $status)
            ->with(['user', 'team'])
            ->get();
    }
    public function getBookingsByUserAndStatus(User $user, string $status): \Illuminate\Database\Eloquent\Collection
    {
        return Booking::where('user_id', $user->id)
            ->where('status', $status)
            ->with(['team'])
            ->get();
    }
    public function getBookingsByTeamAndStatus(Team $team, string $status): \Illuminate\Database\Eloquent\Collection
    {
        return Booking::where('team_id', $team->id)
            ->where('status', $status)
            ->with(['user'])
            ->get();
    }
    public function getBookingsByUserAndTeam(User $user, Team $team): \Illuminate\Database\Eloquent\Collection
    {
        return Booking::where('user_id', $user->id)
            ->where('team_id', $team->id)
            ->with(['team'])
            ->get();
    }
    public function getBookingsByUserTeamAndDate(User $user, Team $team, Carbon $date): \Illuminate\Database\Eloquent\Collection
    {
        return Booking::where('user_id', $user->id)
            ->where('team_id', $team->id)
            ->whereDate('booking_date', $date->toDateString())
            ->with(['team'])
            ->get();
    }
    public function getBookingsByUserTeamAndStatus(User $user, Team $team, string $status): \Illuminate\Database\Eloquent\Collection
    {
        return Booking::where('user_id', $user->id)
            ->where('team_id', $team->id)
            ->where('status', $status)
            ->with(['team'])
            ->get();
    }
}
