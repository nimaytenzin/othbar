<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Default admin for local / Docker dev. Override: ADMIN_EMAIL, ADMIN_PASSWORD.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $email = (string) env('ADMIN_EMAIL', 'admin@othbar.local');
        $plainPassword = (string) env('ADMIN_PASSWORD', 'password');

        $user = User::query()->firstOrNew(['email' => $email]);

        $user->forceFill([
            'name' => 'Admin Othbar',
            'password' => Hash::make($plainPassword),
            'email_verified_at' => now(),
        ])->save();

        $user->assignRole('administrator');

        if ($this->command !== null) {
            $this->command->info("Admin ready: {$email} / (password from ADMIN_PASSWORD or default \"password\")");
        }
    }
}
