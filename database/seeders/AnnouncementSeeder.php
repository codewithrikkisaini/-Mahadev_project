<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $adminId = $admin ? $admin->id : 1;

        $announcements = [
            [
                'title' => '🕉️ Monthly Committee Review Meeting & Bhandara Planning',
                'message' => 'All committee members are cordially invited to attend the monthly committee review meeting this Sunday at 6:00 PM at the Temple Sabha Bhavan. Agenda includes financial review of collections and planning for upcoming Bhandara.',
                'priority' => 'important',
                'publish_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'status' => 'published',
                'created_by' => $adminId,
            ],
            [
                'title' => '🚩 Saffron Dhwajarohan & Shravan Maas Celebrations',
                'message' => 'Special Rudrabhishek and Maha Aarti will take place on all Mondays of Shravan. Members interested in specific Seva duties may contact the committee office.',
                'priority' => 'urgent',
                'publish_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'status' => 'published',
                'created_by' => $adminId,
            ],
            [
                'title' => '🙏 Monthly Seva Contribution Due Date',
                'message' => 'Members are kindly requested to complete their monthly Seva contribution of ₹200 before the 10th of every month using the QR/UPI portal for seamless tracking and receipt generation.',
                'priority' => 'normal',
                'publish_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'status' => 'published',
                'created_by' => $adminId,
            ],
        ];

        foreach ($announcements as $item) {
            Announcement::create($item);
        }
    }
}
