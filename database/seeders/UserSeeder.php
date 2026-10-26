<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // [ឈ្មោះ, អ៊ីមែល, ទូរស័ព្ទ, Role]
            ['Admin Hotel',     'admin@hotel.com',     '012000001', 'admin'],
            ['Reception Dara',  'reception@hotel.com', '012000002', 'receptionist'],
            ['Sok Vireak',      'vireak@example.com',  '012111001', 'guest'],
            ['Chan Sophea',     'sophea@example.com',  '012111002', 'guest'],
            ['Lim Rithy',       'rithy@example.com',   '012111003', 'guest'],
            ['Kim Sreyneang',   'sreyneang@example.com', '012111004', 'guest'],
            ['Ly Panha',        'panha@example.com',   '012111005', 'guest'],
        ];

        foreach ($users as [$name, $email, $phone, $role]) {
            $user = User::firstOrNew(['email' => $email]);

            // forceFill ដើម្បីកុំជាប់បញ្ហា $fillable
            $user->forceFill([
                'name'              => $name,
                'phone'             => $phone,
                'role'              => $role,
                'password'          => Hash::make('12345678'),
                'email_verified_at' => now(),
            ])->save();
        }
    }
}