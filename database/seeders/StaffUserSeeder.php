<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffUserSeeder extends Seeder
{
    /**
     * Default staff account for local / Docker dev. Override: STAFF_EMAIL, STAFF_PASSWORD.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $email = (string) env('STAFF_EMAIL', 'staff@othbar.local');
        $plainPassword = (string) env('STAFF_PASSWORD', 'password');

        $user = User::query()->firstOrNew(['email' => $email]);

        $user->forceFill([
            'name' => 'Staff Othbar',
            'password' => Hash::make($plainPassword),
            'email_verified_at' => now(),
        ])->save();

        $user->syncRoles(['staff']);

        if ($this->command !== null) {
            $this->command->info("Staff ready: {$email} / (password from STAFF_PASSWORD or default \"password\")");
        }
    }
}
