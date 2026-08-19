<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use App\Models\InternationalTransfer;
use App\Models\MultipleTransfer;
use App\Models\SwiftpayTransfer;
use App\Models\Transfer;
use App\Models\TransferRate;
use App\Models\UserTransaction;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use KingFlamez\Rave\Facades\Rave as Flutterwave;


class InternationationalTransferController extends Controller
{
    use ApiResponseTrait;

    public function sendAbroadPage(Request $request)
    {
        try {
            $transfers = InternationalTransfer::whereUserId(auth()->id())->latest()->get();

            $data['totalVolume'] = $transfers->sum('amount_deducted_in_local_currency');

            $data['transfers'] = $transfers;

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function transferAbroadPage(Request $request)
    {
        try {

            $fixedFee = SettingHelper::get(SettingHelper::INTERNATIONAL_TRANFER_FEE_FIXED);
            $percentageFee = SettingHelper::get(SettingHelper::INTERNATIONAL_TRANFER_FEE_PERCENTAGE);

            $data['fixed_fee'] = (float) $fixedFee;
            $data['percentage_fee'] = (float) $percentageFee;

            $data['currencies'] =  [
                'GBP' => [
                    'UK'
                ],
                'USD' => [
                    'Nigeria (domiciliary account)',
                    'US'
                ],
                'EUR' => [
                    'Europe'
                ]
            ];

            $data['currency_logos'] = [
                'USD' => ExchangeRate::where('code', 'USD')->first()->logo_url,
                'GBP' => ExchangeRate::where('code', 'GBP')->first()->logo_url,
                'EUR' => ExchangeRate::where('code', 'EUR')->first()->logo_url,
            ];

            $data['domiciliary_banks'] = [
                "214" => "First City Monument Bank",
                "101" => "Providus Bank",
                "044" => "Other Banks"
            ];

            $data['int_transfer_rates'] = TransferRate::all();

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function  transferMoneyAbroad(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'total_fee' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'account_number' => 'required|string|max:20',
            'account_name' => 'required|string|max:100',
            'bank_name' => 'required|string|max:100',
            'swift_code' => 'nullable|string|max:11',
            'routing_number' => 'nullable|string|max:9',
            'selectedCurrency' => 'required|string',
            'country' => 'required|string',
            'description' => 'required|string',
            'postal_code' => 'required|string',
            'street_name' => 'required|string',
            'street_number' => 'required|string',
            'city' => 'required|string',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        if ($request->total_amount > auth()->user()->wallet_balance) {
            return $this->error('Insufficient funds');
        }



        try {
            $data = [];

            if ($request->selectedCurrency == 'USD' && $request->country == "US") {
                $data = [
                    "amount" => $request->amount,
                    "narration" => $request->description,
                    "currency" => "USD",
                    "beneficiary_name" => $request->account_name,
                    "meta" => [
                        [
                            "account_number" => $request->account_number,
                            "routing_number" => $request->routing_number,
                            "swift_code" => $request->swift_code,
                            "bank_name" => $request->bank_name,
                            "beneficiary_name" => $request->account_name,
                            "beneficiary_country" => "US"
                        ]
                    ]
                ];
            }



            if ($request->selectedCurrency == 'GBP') {
                $data = [
                    "amount" => $request->amount,
                    "narration" => $request->description,
                    "currency" => "GBP",
                    "beneficiary_name" => $request->account_name,
                    "meta" => [
                        [
                            "account_number" => $request->account_number,
                            "routing_number" => $request->routing_number,
                            "swift_code" => $request->swift_code,
                            "bank_name" => $request->bank_name,
                            "beneficiary_name" => $request->account_name,
                            "beneficiary_country" => "UK",
                            "postal_code" => $request->postal_code,
                            "street_name" => $request->street_name,
                            "street_number" => $request->street_number,
                            "city" => $request->city
                        ]
                    ]
                ];
            }

            if ($request->selectedCurrency == 'EUR') {
                $data = [
                    "amount" => $request->amount,
                    "narration" => $request->description,
                    "currency" => "EUR",
                    "beneficiary_name" => $request->account_name,
                    "meta" => [
                        [
                            "account_number" => $request->account_number,
                            "routing_number" => $request->routing_number,
                            "swift_code" => $request->swift_code,
                            "bank_name" => $request->bank_name,
                            "beneficiary_name" => $request->account_name,
                            "beneficiary_country" => "DE",
                            "postal_code" => $request->postal_code,
                            "street_name" => $request->street_name,
                            "street_number" => $request->street_number,
                            "city" => $request->city
                        ]
                    ]
                ];
            }

            $reference = Flutterwave::generateReference();

            $data['reference'] = $reference;

            $transaction = Flutterwave::transfers()->initiate($data);

            DB::beginTransaction();

            if ($transaction['status'] == 'success') {
                $transfer = InternationalTransfer::create([
                    'user_id' => auth()->id(),
                    'reference' => $transaction['data']['reference'],
                    'status' => InternationalTransfer::STATUS_PENDING,
                    'recipient_name' => $request->account_name,
                    'recipient_account_number' => $request->account_number,
                    'recipient_bank' => $request->bank_name,
                    'bank_swift_code' => $request->swift_code,
                    'bank_routing_number' => $request->routing_number,
                    'recipient_amount' => $request->amount,
                    'recipient_currency' => $request->selectedCurrency,
                    'description' => $request->description,
                    'amount_deducted_in_local_currency' => $request->total_amount,
                    'bank_code' => $request->bank_code,
                    'postal_code' => $request->postal_code,
                    'street_name' => $request->street_name,
                    'street_number' => $request->street_number,
                    'city' => $request->city,
                ]);

                auth()->user()->decrement('wallet_balance', $request->total_amount);

                UserTransaction::create([
                    'user_id' => auth()->id(),
                    'amount' => $request->total_amount,
                    'type' => UserTransaction::TYPE_DEBIT,
                    'message' => 'International transfer to ' . $request->account_name,
                    'status' => UserTransaction::STATUS_PENDING,
                    'source' => UserTransaction::SOURCE_INTERNATIONAL_TRANSFER,
                    'fee' => $request->total_fee
                ]);

                DB::commit();

                return $this->success($transaction, 'Transfer initiated successfully');
            } else {
                throw new \Exception($transaction['message']);
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function sentToAfricaPage(Request $request)
    {
        try {

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
                            'status' => $transfer->status == 'success' ? 'successful' : $transfer->status,
                            'profile_pic' => $transfer->receiver->profile_photo,
                            'created_at' => $transfer->created_at,
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
                            'status' => $transfer->status,
                            'created_at' => $transfer->created_at,
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
                            'status' => $transfer->status,
                            'created_at' => $transfer->created_at,
                        ])
                )
                ->sortByDesc('created_at')
                ->values()
                ->toArray();

            $data['totalVolume'] = collect($transfers)->sum('amount');

            $data['dollarRate'] = ExchangeRate::where('code', 'USD')->first()->price;

            $data['transfers'] = $transfers;

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
