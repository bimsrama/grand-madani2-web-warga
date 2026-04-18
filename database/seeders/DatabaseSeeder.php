<?php

namespace Database\Seeders;

use App\Models\Camera;
use App\Models\FinancialReport;
use App\Models\IplTransaction;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin RT 03 ───────────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@rt03.com'],
            [
                'name'      => 'Pengurus RT 03 Grand Madani',
                'password'  => Hash::make('GrandMadani2025!'),
                'rt_number' => '3',
            ]
        );

        $this->command->info('✅ Admin RT 03 dibuat.');

        // ── Sample Residents RT 03 ────────────────────────────────────────────
        $residents = [
            ['block' => 'A', 'number' => '01', 'owner_name' => 'Warga A01', 'wa_number' => '6281234560001'],
            ['block' => 'A', 'number' => '02', 'owner_name' => 'Warga A02', 'wa_number' => '6281234560002'],
            ['block' => 'B', 'number' => '01', 'owner_name' => 'Warga B01', 'wa_number' => '6281234560003'],
            ['block' => 'B', 'number' => '02', 'owner_name' => 'Warga B02', 'wa_number' => '6281234560004'],
        ];

        $residentModels = [];
        foreach ($residents as $data) {
            $residentModels[] = Resident::firstOrCreate(
                ['block' => $data['block'], 'number' => $data['number'], 'rt_number' => '3'],
                array_merge($data, ['is_active' => true, 'rt_number' => '3'])
            );
        }

        // ── Sample Financial Reports (6 bulan terakhir) ────────────────────────
        for ($i = 0; $i < 6; $i++) {
            $month = now()->subMonths($i)->month;
            $year  = now()->subMonths($i)->year;
            FinancialReport::firstOrCreate(
                ['month' => $month, 'year' => $year, 'rt_number' => '3'],
                [
                    'income'      => rand(8, 15) * 100000,
                    'expense'     => rand(2, 6) * 100000,
                    'description' => "Rekap RT 03 Bulan {$month}/{$year}.",
                    'rt_number'   => '3',
                ]
            );
        }

        // ── Sample IPL Transactions ────────────────────────────────────────────
        foreach ($residentModels as $resident) {
            for ($m = 1; $m <= now()->month; $m++) {
                IplTransaction::firstOrCreate(
                    ['resident_id' => $resident->id, 'month' => $m, 'year' => now()->year, 'rt_number' => '3'],
                    [
                        'amount'    => 150000,
                        'status'    => $m < now()->month ? 'lunas' : 'belum',
                        'paid_at'   => $m < now()->month ? now()->subMonths(now()->month - $m)->startOfMonth() : null,
                        'rt_number' => '3',
                    ]
                );
            }
        }

        // ── Sample CCTV ────────────────────────────────────────────────────────
        $cam = Camera::firstOrCreate(
            ['name' => 'CCTV RT 03 - Pintu Masuk', 'rt_number' => '3'],
            ['location' => 'Gerbang Utama', 'embed_url' => 'about:blank', 'is_active' => true, 'rt_number' => '3']
        );

        if ($residentModels) {
            $cam->residents()->syncWithoutDetaching([$residentModels[0]->id]);
        }

        $this->command->info('');
        $this->command->info('════════════════════════════════════════');
        $this->command->info('  ✅ Seeding RT 03 Selesai!');
        $this->command->info('  📧 Email  : admin@rt03.com');
        $this->command->info('  🔑 Password: GrandMadani2025!');
        $this->command->info('════════════════════════════════════════');
    }
}
