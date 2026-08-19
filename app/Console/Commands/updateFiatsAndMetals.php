<?php

namespace App\Console\Commands;

use App\Jobs\SendExpoPushNotification;
use App\Models\ExchangeRate;
use App\Models\MetalRate;
use App\Models\UserNotificationToken;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class updateFiatsAndMetals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-fiats-and-metals';

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
        try {
            $res = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->get(env("METALS_API_URL") . "/latest", [
                'api_key' => env('METALS_API_KEY'),
                'currency' => 'NGN',
            ]);

            $data = $res->json();

            if ($res->successful()) {
                $rates = $data['currencies'];

                $currencies = ExchangeRate::all();

                foreach ($currencies as $currency) {
                    $currency->rate = $rates[$currency->code];
                    $currency->save();
                }

                $metals = $data['metals'];

                foreach ($metals as $key => $value) {
                    MetalRate::updateOrCreate(
                        ['metal' => $key],
                        ['price' => $value]
                    );
                }

                // $usersTokens = UserNotificationToken::get()->toArray();

                // SendExpoPushNotification::dispatch(
                //     $usersTokens,
                //     "New Exchange Rates",
                //     "Check out the latest exchange rates for fiat currencies and metals.",
                //     []
                // );
            } else {
                throw new \Exception($data['message'] ?? "An error occured while updating the rates.");
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }
}
