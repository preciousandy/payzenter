<?php

namespace App\Console\Commands;

use App\Jobs\SendExpoPushNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpgradeUsersLevel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:upgrade-users-level';

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
        $users = User::with('transactions')->get();
        $yesterday = Carbon::yesterday();

        $levels = [
            User::LEVEL_GREEN => [
                'min' => 0,
                'max' => User::LEVEL_LIMITS[User::LEVEL_GREEN]
            ],
            User::LEVEL_BLUE => [
                'min' => User::LEVEL_LIMITS[User::LEVEL_GREEN],
                'max' => User::LEVEL_LIMITS[User::LEVEL_BLUE]
            ],
            User::LEVEL_BLACK => [
                'min' => User::LEVEL_LIMITS[User::LEVEL_BLUE],
                'max' => User::LEVEL_LIMITS[User::LEVEL_BLACK]
            ],
            User::LEVEL_GOLD => [
                'min' => User::LEVEL_LIMITS[User::LEVEL_BLACK],
                'max' => User::LEVEL_LIMITS[User::LEVEL_GOLD]
            ]
        ];

        $levelOrder = [User::LEVEL_GREEN, User::LEVEL_BLUE, User::LEVEL_BLACK, User::LEVEL_GOLD];

        foreach ($users as $user) {
            $yesterdayTransactionVolume = $user->transactions()
                ->whereDate('created_at', $yesterday)
                ->sum('amount');

            $newLevel = User::LEVEL_GREEN; // Default level

            foreach ($levels as $level => $range) {
                if ($yesterdayTransactionVolume >= $range['min'] && $yesterdayTransactionVolume < $range['max']) {
                    $newLevel = $level;
                    break;
                }
            }

            // Check if the new level is higher than the current level
            $currentLevelIndex = array_search($user->level, $levelOrder);
            $newLevelIndex = array_search($newLevel, $levelOrder);

            if ($newLevelIndex > $currentLevelIndex) {
                $user->level = $newLevel;
                $user->if_level_changed = true;
                $user->save();

                $userTokens = $user->notificationTokens->pluck('token')->toArray();

                SendExpoPushNotification::dispatch(
                    $userTokens,
                    "Account Level Upgrade",
                    "Congratulations! Your account level has been upgraded to " . ucfirst($newLevel) . ". This comes with increased transaction limits and benefits.",
                );
            }
        }

        $this->info('Users level upgraded successfully!');
    }
}
