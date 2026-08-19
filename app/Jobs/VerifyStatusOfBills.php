<?php

namespace App\Jobs;

use App\Mail\ElectricityPurchaseNotification;
use App\Models\UserTransaction;
use App\Services\BillsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class VerifyStatusOfBills implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public UserTransaction $transaction;

    /**
     * Create a new job instance.
     */
    public function __construct(UserTransaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $processing_statuses = [
            'initiated-api',
            'queued-api',
            'processing-api'
        ];

        $failed_statuses = [
            'cancelled',
            'failed',
            'refunded',
            'on-hold'
        ];

        $billService = new BillsService();

        $response = $billService->checkOrderStatus($this->transaction->reference);

        if ($response['status'] == 'error') {
            // Handle error
            throw new \Exception($response['message']);
        }

        $data = $response['data'];

        // if status is in processing statuses
        if (in_array($data['status'], $processing_statuses)) {
            // Re-query the status after a delay
            $this->dispatchAfterResponse(new VerifyStatusOfBills($this->transaction), now()->addSeconds(30));
        }

        // if status is in failed statuses
        elseif (in_array($data['status'], $failed_statuses)) {
            // Update transaction status to failed
            DB::beginTransaction();
            $this->transaction->status = UserTransaction::STATUS_FAILED;
            $this->transaction->save();

            // refund the user
            $user = $this->transaction->user;
            $user->wallet_balance += $this->transaction->amount;
            $user->save();

            $refund_message = "Refund for failed " . ucfirst(str_replace('_', ' ', $this->transaction->source)) . " transaction";

            UserTransaction::create([
                'user_id' => $user->id,
                'amount' => $this->transaction->amount,
                'source' => $this->transaction->source,
                'type' => UserTransaction::TYPE_CREDIT,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'message' => $refund_message,
            ]);
            DB::commit();
        }

        // if status is successful
        elseif ($data['status'] == 'completed-api') {
            // Update transaction status to successful
            $this->transaction->status = UserTransaction::STATUS_SUCCESSFUL;
            $this->transaction->save();

            $user = $this->transaction->user;

            // check if it is an electricity transaction
            if ($this->transaction->source == UserTransaction::SOURCE_ELECTRICITY) {
                // Notify user with the token
                $mailData = [
                    'customer_name' => $data['meta_data']['customer_name'],
                    'service_name' => $data['meta_data']['electricity'],
                    'customer_id' =>  $data['meta_data']['meter_number'],
                    'token' => $data['meta_data']['electricity_token'],
                    'amount' => number_format($this->transaction->amount, 2),
                ];

                // update transaction message
                $this->transaction->message = "Electricity purchase successful for {$data['meta_data']['meter_number']}. Token: {$data['meta_data']['electricity_token']}. Amount: " . number_format($this->transaction->amount, 2);
                $this->transaction->save();

                Mail::to($user->email)->queue(new ElectricityPurchaseNotification($user, $mailData));
            }

            // for bills
            if ($this->transaction->source == UserTransaction::SOURCE_CABLE) {
                // Notify user with the bill details
                $email_message = "You cable Showmax subscription was successful. Your voucher is: " . $data['meta_data']['voucher'];


                $this->transaction->message = $email_message . ". Amount: " . number_format($this->transaction->amount, 2) . " Voucher: " . $data['meta_data']['voucher'];
                $this->transaction->save();

                $user->notify(new \App\Notifications\NotifyUser($email_message, "Showmax Subscription"));
            }
        }
    }
}
