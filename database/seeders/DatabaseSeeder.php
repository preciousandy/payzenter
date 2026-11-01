<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Helpers\SettingHelper;
use App\Models\ExchangeRate;
use App\Models\Setting;
use App\Models\Transfer;
use App\Models\TransferRate;
use Flutterwave\Enum\Currency;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Role::create(['name' => 'admin']);
        // Role::create(['name' => 'user']);

        // $this->call(DataPlanSeeder::class);

        // $this->call(CableSeeder::class);

        // $this->call(ElectricProviderSeeder::class);

        // $popularCurrencies = array(
        //     "USD" => "US Dollar",
        //     "EUR" => "Euro",
        //     "JPY" => "Japanese Yen",
        //     "GBP" => "British Pound",
        //     "AUD" => "Australian Dollar",
        //     "CAD" => "Canadian Dollar",
        //     "CHF" => "Swiss Franc",
        //     "CNY" => "Chinese Renminbi (Yuan)",
        //     "NZD" => "New Zealand Dollar",
        //     "SGD" => "Singapore Dollar"
        // );


        // foreach ($popularCurrencies as $code => $name) {
        //     ExchangeRate::updateOrCreate(
        //         [
        //             'code' => $code,
        //         ],
        //         [
        //             'currency' => $name
        //         ]
        //     );
        // }

        $this->call(SettingSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(PaymentTransactionSeeder::class);

        // $this->call(PageContentSeeder::class);

        // $this->call(UserTestimonialSeeder::class);

       //  $this->call(SocialLinkSeeder::class);

        // $this->call(FaqSeeder::class);

       //  $this->call(LiveSupportTopicSeeder::class);

        // $this->call(AddVtuNetworkCode::class);

        // Setting::create([
        //     'key' => SettingHelper::RELOADLY_ACCESS_TOKEN,
        //     'value' => 'eyJraWQiOiI5MTYxZDA4Zi05ODhjLTRiYjItYTI5NS03ODc5NmQ2MzJlM2YiLCJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIyNDc5OCIsImlzcyI6Imh0dHBzOi8vcmVsb2FkbHkuYXV0aDAuY29tLyIsImh0dHBzOi8vcmVsb2FkbHkuY29tL3NhbmRib3giOmZhbHNlLCJodHRwczovL3JlbG9hZGx5LmNvbS9wcmVwYWlkVXNlcklkIjoiMjQ3OTgiLCJndHkiOiJjbGllbnQtY3JlZGVudGlhbHMiLCJhdWQiOiJodHRwczovL2dpZnRjYXJkcy5yZWxvYWRseS5jb20iLCJuYmYiOjE3Mjg1MTM2MjYsImF6cCI6IjI0Nzk4Iiwic2NvcGUiOiJkZXZlbG9wZXIiLCJleHAiOjE3MzM3MDEyMjYsImh0dHBzOi8vcmVsb2FkbHkuY29tL2p0aSI6ImY4ODRkNjQ1LWMyZGItNGE2ZC1iODdmLTVjZTM0NGQxZjNmOCIsImlhdCI6MTcyODUxMzYyNiwianRpIjoiOTVhMzg1NjAtM2IyNS00NzUzLTkyZGEtNmJlYWQyNTUzZjYxIn0.fkrohJ0Koqo8wj-xT17W83Zh0n9ZuP_RLfzjcF5-dTc',
        //     'type' => 'token',
        // ]);


        // $this->call(UpdateCablePackagesSeeder::class);

        // $this->call(ChangeUsersLevelSeeder::class);

        // $this->call(CurrencySymbolSeeder::class);

        // $this->call(PageSeeder::class);

       //  $this->call(TransactionReferenceSeeder::class);

        // $this->call(BettingProviderSeeder::class);
    }
}
