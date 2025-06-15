<?php

namespace Modules\Availability\Services;

use Carbon\Carbon;
use Modules\Booking\Models\Booking;
use Modules\Teams\Models\Team;
use Illuminate\Support\Collection;

class TimeSlotService
{
    public function generateTimeSlots(Team $team, string $fromDate, string $toDate): array
    {
        $startDate = Carbon::parse($fromDate)->startOfDay();
        $endDate = Carbon::parse($toDate)->endOfDay();
        $slots = [];

        // Get team availabilities
        $availabilities = $team->availabilities()
            ->where('is_active', true)
            ->get()
            ->keyBy('day_of_week');

        // Get existing bookings for the date range and group by date (Y-m-d)
        $existingBookings = Booking::where('team_id', $team->id)
            ->whereBetween('booking_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->groupBy(function ($booking) {
                return Carbon::parse($booking->booking_date)->format('Y-m-d');
            });

        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dayOfWeek = $currentDate->dayOfWeek;

            if ($availabilities->has($dayOfWeek)) {
                $availability = $availabilities[$dayOfWeek];

                $bookingsForDate = collect($existingBookings->get($currentDate->format('Y-m-d'), []));
                
                $dateSlots = $this->generateDaySlots(
                    $currentDate,
                    $availability,
                    $bookingsForDate
                );

                $slots = array_merge($slots, $dateSlots);
            }

            $currentDate->addDay();
        }

        return $slots;
    }

    private function generateDaySlots(Carbon $date, $availability, Collection $existingBookings): array
    {
        $slots = [];

        $startTime = Carbon::parse($availability->start_time);
        $endTime = Carbon::parse($availability->end_time);

        $currentSlot = $date->copy()->setTime($startTime->hour, $startTime->minute);
        $dayEnd = $date->copy()->setTime($endTime->hour, $endTime->minute);

        while ($currentSlot->copy()->addHour()->lte($dayEnd)) {
            $slotStart = $currentSlot->copy();
            $slotEnd = $currentSlot->copy()->addHour();

            // Check if the slot conflicts with existing bookings
            $isBooked = $existingBookings->some(function ($booking) use ($slotStart, $slotEnd) {
                $bookingDate = Carbon::parse($booking->booking_date)->format('Y-m-d');
                $bookingStart = Carbon::parse("{$bookingDate} {$booking->start_time}");
                $bookingEnd = Carbon::parse("{$bookingDate} {$booking->end_time}");

                return $slotStart->lt($bookingEnd) && $slotEnd->gt($bookingStart);
            });

            // Add slot only if it's not booked and is in the future
            if (!$isBooked && $slotStart->gte(now())) {
                $slots[] = [
                    'start_time' => $slotStart->format('Y-m-d H:i:s'),
                    'end_time' => $slotEnd->format('Y-m-d H:i:s'),
                    'date' => $date->format('Y-m-d'),
                    'is_available' => true,
                ];
            }

            $currentSlot->addHour(); // Next slot
        }

        return $slots;
    }
}
