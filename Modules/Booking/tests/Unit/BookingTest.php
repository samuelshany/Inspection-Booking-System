<?php

namespace Modules\Booking\Tests\Unit;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Availability\Http\Controllers\AvailabilityController;
use Modules\Availability\Http\Controllers\TimeSlotController;
use Modules\Booking\Http\Controllers\BookingController;
use Modules\Teams\Http\Controllers\TeamsController;
use Tests\TestCase;

class BookingTest extends TestCase
{

    public function create_admin()
    {
        $user = User::factory()->create();
        $user->role = 'admin';
        $user->save();
        Auth::login($user);
    }
    public function create_user()
    {
        $user = User::factory()->create();
        $user->role = 'user';
        $user->save();
        Auth::login($user);
        return $user;
    }
    public function create_team()
    {
        $data = [
            'name' => 'Development Team',
            'description' => 'Handles backend APIs',
        ];
        // Initialize tenant context
        $response = app(TeamsController::class)->store(request()->merge($data));

        // Assert it's a valid JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Convert to array
        $responseData = $response->getData(true); // true = as array
        // Assert status, message, and data
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals(201, $response->status());
        return $responseData['data'];
    }
    public function create_team_avaliability()
    {
        $teamData = $this->create_team(); // array from response
        $team = \Modules\Teams\Models\Team::findOrFail($teamData['id']); // get actual model

        $data = [
            'availabilities' => [
                [
                    'day_of_week' => 1,
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                ],
                [
                    'day_of_week' => 2,
                    'start_time' => '10:00',
                    'end_time' => '18:00',
                ],
                [
                    'day_of_week' => 3,
                    'start_time' => '10:00',
                    'end_time' => '18:00',
                ],
                [
                    'day_of_week' => 4,
                    'start_time' => '10:00',
                    'end_time' => '18:00',
                ],
                [
                    'day_of_week' => 7,
                    'start_time' => '10:00',
                    'end_time' => '18:00',
                ],

            ],
        ];

        $response = app(AvailabilityController::class)->setTeamAvailability(request()->merge($data), $team);

        // Assert it's a valid JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);
        // Convert to array
        $responseData = $response->getData(true);
        // Assert status
        $this->assertEquals('success', $responseData['status']);
        return [$team, $responseData['data']];
    }
    public function generate_slotes(array $teamAndAvail)
    {
        $team = $teamAndAvail[0];
        $fromDate = now();
        $toDate = now()->addDays(7); // 1-week range

        // Make sure from_date and to_date are NOT Friday (5) or Saturday (6)
        while (in_array($fromDate->dayOfWeek, [5, 6])) {
            $fromDate->addDay();
        }

        while (in_array($toDate->dayOfWeek, [5, 6])) {
            $toDate->addDay();
        }

        $data = [
            'from' => $fromDate->format('Y-m-d'),
            'to' => $toDate->format('Y-m-d'),

        ];

        $response = app(TimeSlotController::class)->generateSlots(request()->merge($data), $team);

        $this->assertInstanceOf(JsonResponse::class, $response);

        // Convert to array
        $responseData = $response->getData(true);
        // Assert status
        $this->assertEquals('success', $responseData['status']);
        return $responseData['data'];
    }

    public function test_can_create_booking()
    {

        $user =  $this->create_user();
        $teamAndAvail =  $this->create_team_avaliability();
        $slotes = $this->generate_slotes($teamAndAvail);

        $bookingData = [
            'team_id' => $teamAndAvail[0]->id,
            'booking_date' => now()->format('Y-m-d'),
            'start_time' => Carbon::parse($slotes['slots'][0]['start_time'])->format('H:i:s'),
            'end_time' => Carbon::parse($slotes['slots'][0]['end_time'])->format('H:i:s'),
            'notes' => 'Test booking',
            'user'  => $user->id
        ];


        $controller = app(BookingController::class);
        $response = $controller->store(request()->merge($bookingData));
        $responseData = $response->getData(true);
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals(201, $response->status());
    }

    public function test_can_list_bookings()
    {
        // Create and authenticate user
        $user = $this->create_user();

        // Create a request and bind the user to it
        $request = new \Illuminate\Http\Request();
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        // Call the controller method
        $bookings = app(BookingController::class)->index($request);

        // Assert response type and data
        $this->assertInstanceOf(JsonResponse::class, $bookings);

        $responseData = $bookings->getData(true);
        $this->assertEquals('success', $responseData['status']);
    }

    /*
    public function test_can_update_booking()
    {
        $booking = Booking::factory()->create();

        $response = $this->putJson("/api/v1/bookings/{$booking->id}", [
            'team_id' => $booking->team_id,
            'booking_date' => $booking->booking_date->format('Y-m-d'),
            'start_time' => '12:00:00',
            'end_time' => '13:00:00',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'start_time' => '12:00:00']);
    }

    public function test_can_delete_booking()
    {
        $booking = Booking::factory()->create();

        $response = $this->deleteJson("/api/v1/bookings/{$booking->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
    }*/
}
