<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin
        User::updateOrCreate(
            ['email' => 'admin@mandirseva.com'],
            [
                'name' => 'Pawan Sharma (Pradhan)',
                'mobile' => '9876543210',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // 2. 15 Committee Members
        $membersData = [
            ['name' => 'Ram Kumar', 'email' => 'ram@example.com', 'mobile' => '9812345671', 'address' => 'House No. 12, Shiv Chowk, Sector 4', 'joining_date' => '2026-01-01'],
            ['name' => 'Suresh Sharma', 'email' => 'suresh@example.com', 'mobile' => '9812345672', 'address' => 'Gali No. 3, Mandir Marg, Sector 7', 'joining_date' => '2026-01-01'],
            ['name' => 'Vijay Verma', 'email' => 'vijay@example.com', 'mobile' => '9812345673', 'address' => 'B-45, Shastri Colony', 'joining_date' => '2026-01-01'],
            ['name' => 'Ramesh Gupta', 'email' => 'ramesh@example.com', 'mobile' => '9812345674', 'address' => 'Shop No. 5, Main Bazar', 'joining_date' => '2026-01-01'],
            ['name' => 'Manoj Tiwari', 'email' => 'manoj@example.com', 'mobile' => '9812345675', 'address' => 'Plot 88, Shivpuri Extension', 'joining_date' => '2026-01-01'],
            ['name' => 'Anil Patel', 'email' => 'anil@example.com', 'mobile' => '9812345676', 'address' => 'A-120, Mahadev Enclave', 'joining_date' => '2026-01-01'],
            ['name' => 'Rajesh Joshi', 'email' => 'rajesh@example.com', 'mobile' => '9812345677', 'address' => 'House 43, Teachers Colony', 'joining_date' => '2026-01-01'],
            ['name' => 'Deepak Saini', 'email' => 'deepak@example.com', 'mobile' => '9812345678', 'address' => 'Ward 11, Old City', 'joining_date' => '2026-01-01'],
            ['name' => 'Sunil Yadav', 'email' => 'sunil@example.com', 'mobile' => '9812345679', 'address' => 'C-18, Model Town', 'joining_date' => '2026-01-01'],
            ['name' => 'Amit Choudhary', 'email' => 'amit@example.com', 'mobile' => '9812345680', 'address' => 'Near Water Tank, Shiv Nagar', 'joining_date' => '2026-01-01'],
            ['name' => 'Vikram Singh', 'email' => 'vikram@example.com', 'mobile' => '9812345681', 'address' => 'H-302, Green Valley Apartments', 'joining_date' => '2026-02-01'],
            ['name' => 'Pankaj Agarwal', 'email' => 'pankaj@example.com', 'mobile' => '9812345682', 'address' => 'Kothi 9, Civil Lines', 'joining_date' => '2026-02-01'],
            ['name' => 'Rakesh Mehta', 'email' => 'rakesh@example.com', 'mobile' => '9812345683', 'address' => 'D-77, Defence Colony', 'joining_date' => '2026-03-01'],
            ['name' => 'Ajay Malhotra', 'email' => 'ajay@example.com', 'mobile' => '9812345684', 'address' => 'Street 6, Krishna Gali', 'joining_date' => '2026-03-01'],
            ['name' => 'Sanjay Bhatia', 'email' => 'sanjay@example.com', 'mobile' => '9812345685', 'address' => 'Flat 204, Om Residency', 'joining_date' => '2026-04-01'],
        ];

        foreach ($membersData as $index => $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'mobile' => $data['mobile'],
                    'password' => Hash::make('password'),
                    'role' => 'member',
                    'status' => 'active',
                ]
            );

            $memberCode = sprintf('MSC%05d', $index + 1);

            Member::updateOrCreate(
                ['member_code' => $memberCode],
                [
                    'user_id' => $user->id,
                    'name' => $data['name'],
                    'mobile' => $data['mobile'],
                    'email' => $data['email'],
                    'address' => $data['address'],
                    'joining_date' => $data['joining_date'],
                    'status' => 'active',
                    'notes' => 'Dedicated Mandir Seva Committee Member',
                ]
            );
        }
    }
}
