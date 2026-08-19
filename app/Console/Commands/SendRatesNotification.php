<?php

namespace App\Console\Commands;

use App\Jobs\SendExpoPushNotification;
use App\Models\UserNotificationToken;
use Illuminate\Console\Command;

class SendRatesNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-rates-notification';

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
        $usersTokens = UserNotificationToken::get()->pluck('token')->toArray();

        // $usersTokens = [
        //     'ExponentPushToken[raToIZEOKt6vilLGJoiEBg]',
        //     'ExponentPushToken[_i-BRcAQCudUFyWjA5yws3]',
        //     'ExponentPushToken[30zIGeA4jFe35V-2MdBwde]',
        //     'ExponentPushToken[x7w3KQLKfFVw0B-7OLW9VH]',
        //     'ExponentPushToken[P9SWTnCQlF88favlhrRSds]',
        //     'ExponentPushToken[gPEkyKKcXIWPKADVxy0NRB]',
        //     'ExponentPushToken[n1fLZGNp25PcIx9u7fp11T]',
        //     'ExponentPushToken[okdl0DJM3r0-jmxzMjieQe]',
        //     'ExponentPushToken[IKVFuVLAzBqzzo0RWVInrA]',
        // ];




        SendExpoPushNotification::dispatch(
            $usersTokens,
            "New Exchange Rates",
            "Check out the latest exchange rates for Fiat currencies and Crypto*",
            [
                'url' => 'swiftpay://Rates'
            ]
        );

        $this->info('Exchange rates notification sent successfully.');
    }
}
