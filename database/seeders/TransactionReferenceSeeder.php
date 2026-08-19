<?php

namespace Database\Seeders;

use App\Models\UserTransaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionReferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactions = UserTransaction::all();

        DB::beginTransaction();
        foreach ($transactions as $transaction) {
            sleep(1); // sleep for 1 second to avoid duplicate reference
            $transaction->update([
                'reference' => generateTransactionReference($transaction->source ?? '')
            ]);
        }

        DB::commit();
    }
}
