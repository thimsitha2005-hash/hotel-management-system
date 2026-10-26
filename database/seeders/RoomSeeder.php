<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $desc = [
            'Standard' => 'WiFi, AC, TV, ទឹកក្តៅ',
            'Deluxe'   => 'WiFi, AC, Smart TV, Mini-bar, ប្រអប់ទឹកកក',
            'Suite'    => 'WiFi, AC, Smart TV, Mini-bar, បន្ទប់ទទួលភ្ញៀវ, Bathtub',
        ];

        $rooms = [
            // [លេខ, ប្រភេទ, តម្លៃ, ចំនួនមនុស្ស, ស្ថានភាព]
            ['101', 'Standard', 40, 2, 'available'],
            ['102', 'Standard', 40, 2, 'available'],
            ['103', 'Standard', 45, 2, 'available'],
            ['104', 'Standard', 45, 3, 'maintenance'],
            ['201', 'Deluxe',   70, 3, 'available'],
            ['202', 'Deluxe',   70, 3, 'available'],
            ['203', 'Deluxe',   80, 4, 'available'],
            ['301', 'Suite',   120, 4, 'available'],
            ['302', 'Suite',   150, 5, 'available'],
            ['303', 'Suite',   180, 6, 'available'],
        ];

        foreach ($rooms as [$number, $type, $price, $capacity, $status]) {
            // firstOrCreate: មិនសរសេរជាន់បន្ទប់ដែលមានស្រាប់
            Room::firstOrCreate(
                ['room_number' => $number],
                [
                    'type'        => $type,
                    'price'       => $price,
                    'capacity'    => $capacity,
                    'status'      => $status,
                    'description' => $desc[$type],
                ]
            );
        }
    }
}