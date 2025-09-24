<?php

namespace App\Console\Commands;

use App\Mail\CartReminderMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CartReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cart-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::query()->whereHas('carts')->get();

        foreach ($users as $index => $user) {
            $cartItems = $user->carts()->get();

            if ($cartItems->count() > 0) {

                Mail::to($user->email)
                    ->later(now('Asia/Jakarta')->addMinutes(1), new CartReminderMail($user, $cartItems));

                $this->info("Sending reminder to {$user->name}");
            }
        }


        $this->info("Chart Reminder  sent to {$users->count()} user");
        Log::info("Chart reminder sent to {$users->count()} user");
    }
}
