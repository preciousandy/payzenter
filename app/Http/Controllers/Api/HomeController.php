<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MultipleTransfer;
use App\Models\SwiftpayTransfer;
use App\Models\Transfer;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HomeController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {
            $data = [];

            $data['wallet_balance'] = auth()->user()->wallet_balance;

            $data['if_account_level_changed'] = auth()->user()->if_level_changed ? true : false;

            $data['kyc_status'] = auth()->user()->kyc_status;

            $data['if_swiftpay_tag'] = auth()->user()->username ? true : false;

            $data['international_transfer_total'] = auth()->user()->internationalTransfers()->sum('amount_deducted_in_local_currency');

            $data['holdings_total'] = auth()->user()->holdings()->active()->sum('amount');

            $data['investments_total'] = auth()->user()->investments()->active()->sum('amount_invested');

            $data['ajo_savings_balance'] = (int) auth()->user()->ajoSavings()->active()->sum('balance');

            $data['ajo_contribution_wallet'] = auth()->user()->ajo_contribution_wallet;

            $data['send_to_africa_total'] =  $this->calculateSendToAfricaTotal();

            return  $this->success($data, 'Data sent successfully');
        } catch (\Exception $e) {
            return  $this->error($e->getMessage());
        }
    }

    private function calculateSendToAfricaTotal()
    {
        $transfers = collect([])
            ->merge(
                SwiftpayTransfer::where('source_link', 'send_to_africa')
                    ->where('user_id', auth()->id())
                    ->get()
                    ->map(fn($transfer) => [
                        'reference' => $transfer->reference,
                        'amount' => $transfer->amount,
                        'account_name' => $transfer->receiver->name,
                        'type' => "SwiftPay Transfer",
                        'date' => $transfer->created_at->format('d M, Y'),
                    ])
            )
            ->merge(
                Transfer::where('source_link', 'send_to_africa')
                    ->where('user_id', auth()->id())
                    ->get()
                    ->map(fn($transfer) => [
                        'reference' => $transfer->reference,
                        'amount' => $transfer->amount,
                        'account_name' => $transfer->account_name,
                        'type' => "Bank Transfer",
                        'date' => $transfer->created_at->format('d M, Y'),
                    ])
            )
            ->merge(
                MultipleTransfer::where('source_link', 'send_to_africa')
                    ->where('user_id', auth()->id())
                    ->get()
                    ->map(fn($transfer) => [
                        'reference' => $transfer->reference,
                        'amount' => $transfer->total_amount,
                        'account_name' => $transfer->accounts,
                        'type' => $transfer->type == 'bank' ? "Multiple Bank Transfer" : "Multiple SwiftPay Transfer",
                        'date' => $transfer->created_at->format('d M, Y'),
                    ])
            )
            ->toArray();


        $sendToAfricaTotal = collect($transfers)->sum('amount');

        return $sendToAfricaTotal;
    }
}
