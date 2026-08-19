<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UserService
{
    public function generateAccountNumber(User $user, $bvn, $customerDetails = [])
    {
        $abbr = getCompanyAbbreviation();
        $email = $customerDetails['email'] ?? $user->email;
        $name = $customerDetails['name'] ?? ($user->first_name . " " . $user->last_name);
        
        if (!str_starts_with($name, $abbr)) {
            $name = $abbr . " " . $name;
        }

        try {
            $res = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env("FLW_LIVE_SECRET_KEY")
            ])->post(env("FLW_API_URL") . "/virtual-account-numbers", [
                "email" => $email,
                "bvn" => $bvn,
                "tx_ref" => $customerDetails['merchant_ref'] ?? $user->email . '_' . time(),
                "narration" => $name,
                "is_permanent" => true
            ]);

            $data = $res->json();


            if ($res->successful() && $data['status'] == "success") {
                return  [
                    'status' => 'success',
                    'data' => $data['data']
                ];
            } else {
                throw new \Exception($data['message']);
            }
        } catch (\Throwable $th) {
            return [
                'status' => 'error',
                'message' => $th->getMessage()
            ];
        }
    }

    public function generatePalmPayVirtualAccount(User $user, $bvn, $customerDetails = [])
    {
        try {
            $abbr = getCompanyAbbreviation();
            $palmPay = new PalmPayService();
            $customerName = $customerDetails['name'] ?? ($user->first_name . ' ' . $user->last_name);
            $virtualAccountName = $customerName;

            if (!str_starts_with($virtualAccountName, $abbr)) {
                $virtualAccountName = $abbr . " " . $virtualAccountName;
            }

            $email = $customerDetails['email'] ?? $user->email;
            $phone = $customerDetails['phone'] ?? null;

            $baseVaPayload = [
                'virtualAccountName' => $virtualAccountName,
                'email' => $email,
                'customerName' => $customerName,
                'accountReference' => $customerDetails['merchant_ref'] ?? ('vva_' . Str::lower(Str::random(16))),
            ];

            if (!empty($phone)) {
                $baseVaPayload['phoneNumber'] = $phone;
            }

            $identityStrategies = [];

            if (!empty($bvn)) {
                $identityStrategies[] = ['identityType' => 'bvn', 'licenseNumber' => (string) $bvn];
                $identityStrategies[] = ['identityType' => 'BVN', 'licenseNumber' => (string) $bvn];
                $identityStrategies[] = ['identityType' => 'personal', 'licenseNumber' => (string) $bvn];
            }

            $identityStrategies[] = []; // empty strategy (no identityType)

            if (!empty($phone)) {
                $digits = preg_replace('/\D/', '', $phone);
                if ($digits !== '') {
                    $identityStrategies[] = ['identityType' => 'phone', 'licenseNumber' => $digits];
                }
            }

            $identityStrategies[] = ['identityType' => 'email', 'licenseNumber' => $email];

            $lastError = null;
            foreach ($identityStrategies as $strat) {
                try {
                    $payload = array_merge($baseVaPayload, $strat);
                    $response = $palmPay->createVirtualAccount($payload);
                    if (($response['respCode'] ?? null) === '00000000') {
                        return [
                            'status' => 'success',
                            'data' => $response['data']
                        ];
                    }
                    $lastError = $response['respMsg'] ?? 'Unable to create PalmPay virtual account';
                } catch (\Throwable $ex) {
                    $lastError = $ex->getMessage();
                }
            }

            throw new \Exception($lastError ?? 'Unable to create PalmPay virtual account');
        } catch (\Throwable $th) {
            return [
                'status' => 'error',
                'message' => $th->getMessage()
            ];
        }
    }

    public function generateDotVirtualAccount(User $user)
    {
        return $this->createDotVirtualAccount([
            'dob' => $user->kyc->date_of_birth ?? '1990-01-01',
            'external_id' => "SWP" . $user->id,
            'firstname' => $user->first_name,
            'lastname' => $user->last_name,
            'gender' => $user->kyc->gender ?? 'MALE'
        ]);
    }

    public function createDotVirtualAccount(array $details)
    {
        $accessToken = getDotAccessToken();

        if (!$accessToken) {
            return [
                'status' => 'error',
                'message' => 'Unable to fetch access token for Dot MFB API'
            ];
        }

        try {
            $abbr = getCompanyAbbreviation();
            $firstname = $details['firstname'];
            if (!str_starts_with($firstname, $abbr)) {
                $firstname = $abbr . " " . $firstname;
            }

            $res = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken
            ])->post(env("DOT_MFB_API_URL") . "/vaas", [
                "dateOfBirth" => $details['dob'] ?? '1990-01-01',
                "externalId" => ($details['external_id'] ?? $abbr) . env('APP_ENV') . time(),
                "firstname" => $firstname,
                "lastname" => $details['lastname'],
                "gender" => strtoupper($details['gender'] ?? 'MALE')
            ]);

            $data = $res->json();

            if ($res->successful()) {
                return  [
                    'status' => 'success',
                    'data' => $data
                ];
            }
            
            return [
                'status' => 'error',
                'message' => $data['message'] ?? 'Unable to generate virtual account'
            ];
        } catch (\Throwable $th) {
            return [
                'status' => 'error',
                'message' => $th->getMessage()
            ];
        }
    }

    public function generateQuidaxWallet(User $user)
    {
        $email = generateQuidaxEmail($user);

        try {
            $abbr = getCompanyAbbreviation();
            $firstname = $user->first_name;
            if (!str_starts_with($firstname, $abbr)) {
                $firstname = $abbr . " " . $firstname;
            }

            $res = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env("QUIDAX_SECRET_API_KEY")
            ])->post(env("QUIDAX_API_URL") . "/users", [
                'email' => $email,
                'first_name' => $firstname,
                'last_name' => $user->last_name,
            ]);

            $data = $res->json();


            if ($res->successful() && $data['status'] == "success") {
                return  [
                    'status' => 'success',
                    'data' => $data['data']
                ];
            } else {
                throw new \Exception($data['data']['message'] ?? 'An error occurred');
            }
        } catch (\Throwable $th) {
            try {

                $response = (new CryptoService)->getAllSubAccounts();

                if ($response['status'] == 'success') {
                    $accounts = $response['data'];

                    $userAccount = collect($accounts)->first(function ($account) use ($user) {
                        return $account['email'] == $user->quidax_email;
                    });

                    if (!$userAccount) {
                        return [
                            'status' => 'error',
                            'message' => $th->getMessage()
                        ];
                    }



                    return [
                        'status' => 'success',
                        'data' => $userAccount
                    ];
                } else {
                    throw new \Exception($response['message']);
                }
            } catch (\Throwable $th) {
                return [
                    'status' => 'error',
                    'message' => $th->getMessage()
                ];
            }
        }
    }

    public function getQuidaxCurrencyWallet($user_id, $currency_code)
    {

        try {
            $res = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env("QUIDAX_SECRET_API_KEY")
            ])->get(env("QUIDAX_API_URL") . "/users" . "/$user_id" . "/wallets" . "/$currency_code");

            $data = $res->json();



            if ($res->successful() && $data['status'] == "success") {
                return  [
                    'status' => 'success',
                    'data' => $data['data']
                ];
            } else {
                throw new \Exception($data['data']['message'] ?? 'An error occurred');
            }
        } catch (\Throwable $th) {
            return [
                'status' => 'error',
                'message' => $th->getMessage()
            ];
        }
    }


    public function getQuidaxCurrencyPaymentAddress($user_id, $network, $currency_code)
    {

        try {
            $res = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env("QUIDAX_SECRET_API_KEY")
            ])->post(env("QUIDAX_API_URL") . "/users" . "/$user_id" . "/wallets" . "/$currency_code" . "/addresses", [
                'network' => $network
            ]);

            $data = $res->json();


            if ($res->successful() && $data['status'] == "success") {
                return  [
                    'status' => 'success',
                    'data' => $data['data']
                ];
            } else {
                throw new \Exception($data['message'] ?? 'An error occurred');
            }
        } catch (\Throwable $th) {
            return [
                'status' => 'error',
                'message' => $th->getMessage()
            ];
        }
    }
}
