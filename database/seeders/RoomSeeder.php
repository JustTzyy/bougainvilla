<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Level;
use App\Models\Accommodation;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get existing levels and accommodations
        $levels = Level::all();
        $accommodations = Accommodation::all();

        if ($levels->isEmpty() || $accommodations->isEmpty()) {
            $this->command->warn('Please run Level and Accommodation seeders first.');
            return;
        }

        // Sample room data
        $rooms = [
            [
                'room' => '101',
                'status' => 'Active',
                'type' => 'Available',
                'level_id' => $levels->first()->id,
                'accommodations' => $accommodations->take(2)->pluck('id')->toArray()
            ],
            [
                'room' => '102',
                'status' => 'Active',
                'type' => 'Occupied',
                'level_id' => $levels->first()->id,
                'accommodations' => $accommodations->skip(1)->take(2)->pluck('id')->toArray()
            ],
            [
                'room' => '201',
                'status' => 'Under Maintenance',
                'type' => 'Available',
                'level_id' => $levels->skip(1)->first()->id ?? $levels->first()->id,
                'accommodations' => $accommodations->take(1)->pluck('id')->toArray()
            ],
            [
                'room' => '202',
                'status' => 'Active',
                'type' => 'Reserved',
                'level_id' => $levels->skip(1)->first()->id ?? $levels->first()->id,
                'accommodations' => $accommodations->skip(2)->take(2)->pluck('id')->toArray()
            ],
            [
                'room' => '301',
                'status' => 'Active',
                'type' => 'Booked',
                'level_id' => $levels->skip(2)->first()->id ?? $levels->first()->id,
                'accommodations' => []
            ]
        ];

        foreach ($rooms as $roomData) {
            $accommodations = $roomData['accommodations'];
            unset($roomData['accommodations']);

            $room = Room::create($roomData);
            
            if (!empty($accommodations)) {
                $room->accommodations()->attach($accommodations);
            }
        }

        $this->command->info('Rooms seeded successfully!');
    }
}