<?php

namespace Database\Seeders;

use App\Domains\Settings\CreateAccount\Jobs\SetupAccount;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $email = env('INITIAL_USER_EMAIL');
        $password = env('INITIAL_USER_PASSWORD');

        if (! $email || ! $password) {
            return;
        }

        if (User::where('email', $email)->exists()) {
            return;
        }

        $account = Account::create();

        $user = User::create([
            'account_id' => $account->id,
            'first_name' => 'Erlon',
            'last_name' => 'User',
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
            'is_account_administrator' => true,
        ]);

        (new SetupAccount)->execute([
            'account_id' => $account->id,
            'author_id' => $user->id,
        ]);
    }
}
