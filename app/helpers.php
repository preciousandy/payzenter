<?php

use App\Models\User;
use App\Models\WebsiteContent;
use App\Services\TransferService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use KingFlamez\Rave\Facades\Rave;

function getStaticBanks(): array
{
  $banks = [
    ['code' => '044', 'name' => 'Access Bank'],
    ['code' => '023', 'name' => 'Citibank Nigeria'],
    ['code' => '050', 'name' => 'Ecobank Nigeria'],
    ['code' => '070', 'name' => 'Fidelity Bank'],
    ['code' => '011', 'name' => 'First Bank of Nigeria'],
    ['code' => '214', 'name' => 'First City Monument Bank (FCMB)'],
    ['code' => '00103', 'name' => 'Globus Bank'],
    ['code' => '058', 'name' => 'Guaranty Trust Bank (GTBank)'],
    ['code' => '030', 'name' => 'Heritage Bank'],
    ['code' => '301', 'name' => 'Jaiz Bank'],
    ['code' => '082', 'name' => 'Keystone Bank'],
    ['code' => '090267', 'name' => 'Kuda Bank'],
    ['code' => '303', 'name' => 'Lotus Bank'],
    ['code' => '090405', 'name' => 'Moniepoint Microfinance Bank'],
    ['code' => '100004', 'name' => 'OPay (Paycom)'],
    ['code' => '100033', 'name' => 'PalmPay'],
    ['code' => '104', 'name' => 'Parallex Bank'],
    ['code' => '076', 'name' => 'Polaris Bank'],
    ['code' => '105', 'name' => 'PremiumTrust Bank'],
    ['code' => '101', 'name' => 'Providus Bank'],
    ['code' => '090175', 'name' => 'Rubies Bank'],
    ['code' => '106', 'name' => 'Signature Bank'],
    ['code' => '221', 'name' => 'Stanbic IBTC Bank'],
    ['code' => '068', 'name' => 'Standard Chartered Bank'],
    ['code' => '232', 'name' => 'Sterling Bank'],
    ['code' => '100', 'name' => 'SunTrust Bank'],
    ['code' => '302', 'name' => 'TAJ Bank'],
    ['code' => '102', 'name' => 'Titan Trust Bank'],
    ['code' => '032', 'name' => 'Union Bank of Nigeria'],
    ['code' => '033', 'name' => 'United Bank for Africa (UBA)'],
    ['code' => '215', 'name' => 'Unity Bank'],
    ['code' => '090110', 'name' => 'VFD Microfinance Bank'],
    ['code' => '035', 'name' => 'Wema Bank'],
    ['code' => '057', 'name' => 'Zenith Bank'],
    ['code' => '090470', 'name' => 'Dot MFB'],
    ['code' => '090130', 'name' => 'Carbon'],
    ['code' => '090551', 'name' => 'FairMoney Microfinance Bank'],
  ];

  usort($banks, fn($a, $b) => strcasecmp($a['name'], $b['name']));

  return array_map(function ($b) {
    return [
      'id' => $b['code'],
      'code' => $b['code'],
      'name' => $b['name'],
      'bankCode' => $b['code'],
      'bankName' => $b['name'],
    ];
  }, $banks);
}

function getBanks()
{
  try {
    foreach ([env('FLW_LIVE_SECRET_KEY'), env('FLW_SECRET_KEY')] as $key) {
      if (!$key) continue;
      $res = Http::withToken($key)->timeout(6)->get('https://api.flutterwave.com/v3/banks/NG');
      if ($res->successful()) {
        $banks = $res->json();
        if (is_array($banks) && !empty($banks['data']) && is_array($banks['data'])) {
          $list = array_values(array_filter(array_map(function ($bank) {
            if (!is_array($bank) || empty($bank['name'])) return null;
            $code = (string) ($bank['code'] ?? $bank['id'] ?? '');
            $name = (string) ($bank['name'] ?? '');
            if ($code === '' || $name === '') return null;
            return [
              'id' => $code,
              'code' => $code,
              'name' => $name,
              'bankCode' => $code,
              'bankName' => $name,
            ];
          }, $banks['data'])));

          if (!empty($list)) {
            usort($list, fn($a, $b) => strcasecmp($a['name'], $b['name']));
            return $list;
          }
        }
      }
    }
  } catch (\Throwable $th) {
    // continue to fallback
  }

  return getDotBanks();
}

function excludedBanks()
{
  return [
    "Opay",
    "Moniepoint Microfinance Bank",
    "Kuda",
    "PALMPAY",
    "Jaiz Bank",
    "Tajwallet",
    "Fairmoney Microfinance Bank Ltd",
    "Carbon",
    "Eyowo MFB",
    "Paycom"
  ];
}

function getDotBanks()
{
  // 1. Try Dot MFB if configured
  try {
    $baseUrl = rtrim((string) env("DOT_MFB_API_URL", ''), '/');
    $accessToken = getDotAccessToken();

    if ($baseUrl !== '' && $accessToken) {
      $res = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $accessToken
      ])->timeout(5)->get($baseUrl . "/banking/banks");

      if ($res->successful()) {
        $data = $res->json();
        $list = $data['data'] ?? $data;
        if (is_array($list) && !empty($list)) {
          $formatted = array_values(array_filter(array_map(function ($bank) {
            if (!is_array($bank)) return null;
            $code = (string) ($bank['code'] ?? $bank['bankCode'] ?? '');
            $name = (string) ($bank['name'] ?? $bank['bankName'] ?? '');
            if ($code === '' || $name === '') return null;
            return [
              'id' => $code,
              'code' => $code,
              'name' => $name,
              'bankCode' => $code,
              'bankName' => $name,
            ];
          }, $list)));

          if (!empty($formatted)) {
            usort($formatted, fn($a, $b) => strcasecmp($a['name'], $b['name']));
            return $formatted;
          }
        }
      }
    }
  } catch (\Throwable $th) {
    // continue
  }

  // 2. Try Paystack Public Bank List (returns 290+ banks)
  try {
    $secretKey = config('paystack.secret_key') ?: env('PAYSTACK_SECRET_KEY');
    $req = Http::timeout(6);
    if (!empty($secretKey)) {
      $req = $req->withHeaders(['Authorization' => 'Bearer ' . $secretKey]);
    }
    $res = $req->get('https://api.paystack.co/bank?country=nigeria');

    if ($res->successful()) {
      $data = $res->json();
      $list = $data['data'] ?? [];
      if (is_array($list) && !empty($list)) {
        $formatted = array_values(array_filter(array_map(function ($bank) {
          if (!is_array($bank)) return null;
          $code = (string) ($bank['code'] ?? '');
          $name = (string) ($bank['name'] ?? '');
          if ($code === '' || $name === '') return null;
          return [
            'id' => $code,
            'code' => $code,
            'name' => $name,
            'bankCode' => $code,
            'bankName' => $name,
          ];
        }, $list)));

        if (!empty($formatted)) {
          usort($formatted, fn($a, $b) => strcasecmp($a['name'], $b['name']));
          return $formatted;
        }
      }
    }
  } catch (\Throwable $th) {
    // continue
  }

  // 3. Try Flutterwave with active keys
  try {
    foreach ([env('FLW_LIVE_SECRET_KEY'), env('FLW_SECRET_KEY')] as $k) {
      if (!$k) continue;
      $res = Http::withToken($k)->timeout(6)->get('https://api.flutterwave.com/v3/banks/NG');
      if ($res->successful()) {
        $banks = $res->json();
        if (is_array($banks) && !empty($banks['data']) && is_array($banks['data'])) {
          $formatted = array_values(array_filter(array_map(function ($bank) {
            if (!is_array($bank)) return null;
            $code = (string) ($bank['code'] ?? $bank['id'] ?? '');
            $name = (string) ($bank['name'] ?? '');
            if ($code === '' || $name === '') return null;
            return [
              'id' => $code,
              'code' => $code,
              'name' => $name,
              'bankCode' => $code,
              'bankName' => $name,
            ];
          }, $banks['data'])));

          if (!empty($formatted)) {
            usort($formatted, fn($a, $b) => strcasecmp($a['name'], $b['name']));
            return $formatted;
          }
        }
      }
    }
  } catch (\Throwable $th) {
    // continue
  }

  // 4. Guaranteed Fallback to static list of Nigerian Banks (Never empty!)
  return getStaticBanks();
}

function getDotAccessToken()
{
  $accessToken = Cache::remember('dot_access_token', now()->addMinutes(4), function () {
    // Logic to compute and return the value if not cached
    try {
      $transferService = new TransferService();

      $response = $transferService->fetchDotAccessToken();

      if ($response['status'] == 'success') {
        return $response['data'];
      } else {
        return null;
      }
    } catch (\Throwable $th) {

      return null;
    }
  });

  return $accessToken;
}


function getEbillsAccessToken()
{
  $period = env('APP_ENV') == 'production' ? now()->addDay() : now()->addMinutes(5);
  $accessToken = Cache::remember('ebills_access_token', $period, function () {
    // Logic to compute and return the value if not cached
    try {
      $billsService = new \App\Services\BillsService();

      $response = $billsService->fetchEbillsAccessToken();

      if ($response['status'] == 'success') {
        return $response['data'];
      }
    } catch (\Throwable $th) {

      return null;
    }
  });

  return $accessToken;
}


function getGiftcardsAccessToken()
{
  $accessToken = Cache::remember('gift_cards_access_token', now()->addDays(25), function () {
    // Logic to compute and return the value if not cached
    try {
      $giftcardsService = new \App\Services\GiftCardsService();

      $response = $giftcardsService->fetchReloadlyGiftcardsAccessToken();

      if ($response['status'] == 'success') {
        return $response['data'];
      }
    } catch (\Throwable $th) {

      return null;
    }
  });

  return $accessToken;
}



function formatNumber($number)
{
  if (!is_numeric($number)) {
    return 0;
  }
  return number_format($number, 2, '.', ',');
}

function formatDate($date)
{
  return date_format(date_create($date->toDateString()), 'd F Y');
}

function getPercentageAmount($amount, $interest)
{

  if (!$amount) return 0;

  $interestAmount = $amount * ($interest / 100);

  return  $interestAmount;
}

function getInterestGained($amount, $interest)
{
  if (!$amount) return 0;

  $interestAmount = getPercentageAmount($amount, $interest);

  return $amount + $interestAmount;
}

function generateRandomId($prefix = 'element')
{
  return $prefix . '-' . uniqid() . '-' . bin2hex(random_bytes(4));
}

function getTotalFee($amount, $fixed_fee = 0, $percentage_fee = 0, $flat_rate = 0)
{
  $pecentage = getPercentageAmount($amount, $percentage_fee);

  return $pecentage + $fixed_fee + $flat_rate;
}

if (!function_exists('mask_email')) {
  function mask_email($email)
  {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
      list($first, $domain) = explode('@', $email);

      $len = strlen($first);
      $mask_length = ceil($len / 2);
      $start = $len - $mask_length;

      $first = substr($first, 0, $start) . str_repeat('*', $mask_length);

      return $first . '@' . $domain;
    }
    return $email;
  }
}

function splitLivewireTmpPath($path)
{
  // Find the position of "/livewire-tmp"
  $position = strpos($path, '/livewire-tmp');

  if ($position === false) {
    return ['path' => $path, 'livewire_tmp' => null];
  }

  // Split the string
  $basePath = substr($path, 0, $position);
  $livewireTmpPath = substr($path, $position);

  return $livewireTmpPath;
}


function randomImage($name)
{
  return 'https://ui-avatars.com/api/?background=random&name=' . $name . '&rounded=true&bold=true';
}

function generatePriceRange(float $basePrice, int $numberOfItems, float $range): array
{
  // Input validation
  if ($basePrice <= 0) {
    throw new InvalidArgumentException("Base price must be greater than 0");
  }

  if ($numberOfItems <= 1) {
    throw new InvalidArgumentException("Number of items must be greater than 1");
  }

  if ($range <= 0) {
    throw new InvalidArgumentException("Range must be greater than 0");
  }

  if ($range >= $basePrice) {
    throw new InvalidArgumentException("Range cannot be greater than or equal to base price");
  }

  $minPrice = $basePrice - $range;
  $prices = [];

  // Generate one less random price since base price will be the last item
  for ($i = 0; $i < $numberOfItems - 1; $i++) {
    // Generate random float between min and max price
    // mt_rand() is used for better random number generation
    $randomPrice = $minPrice + (mt_rand() / mt_getrandmax()) * $range;

    // Round to 2 decimal places to maintain currency format
    $prices[] = round($randomPrice, 2);
  }

  // Add the base price as the last element
  $prices[] = round($basePrice, 2);

  return $prices;
}

function getLastNDays(int $numberOfDays, string $format = 'Y-m-d'): array
{
  // Input validation
  if ($numberOfDays <= 0) {
    throw new InvalidArgumentException("Number of days must be greater than 0");
  }

  $dates = [];
  $today = new DateTime();

  // Generate dates
  for ($i = $numberOfDays - 1; $i >= 0; $i--) {
    $date = clone $today;
    $date->modify("-$i days");
    $dates[] = $date->format($format);
  }

  return $dates;
}


function calculatePercentageChange(float $percentageChange, float $amount): float
{
  // Convert percentage to decimal
  $decimal = $percentageChange / 100;

  // Calculate the change amount
  $changeAmount = $amount * abs($decimal);

  // If percentage is negative, subtract the change
  // If percentage is positive, add the change
  return $percentageChange < 0
    ? $amount - $changeAmount
    : $amount + $changeAmount;
}

function generateTransactionReference(string $source): string
{
  // Capitalize the source
  $source = strtoupper($source);

  // Replace underscore with space
  $source = str_replace('_', ' ', $source);

  // Split the source into words
  $words = explode(' ', $source);

  // Initialize reference prefix
  $prefix = '';

  if (count($words) === 1) {
    // If single word, take first two letters
    $prefix = substr($words[0], 0, 2);
  } else {
    // If multiple words, take first letter of each word
    foreach ($words as $word) {
      $prefix .= substr($word, 0, 1);
    }
  }

  // Generate timestamp
  $timestamp = time();

  // Combine prefix and timestamp
  return 'PGNG' . $prefix . $timestamp;
}

function getRandomElement($array = ['savings', 'referral'])
{
  // Check if the array is not empty
  if (empty($array)) {
    return null;
  }

  // Use array_rand to get a random key
  $randomKey = array_rand($array);

  // Return the element at the random key
  return $array[$randomKey];
}

function get_website_content($column)
{
  try {
    if (!Schema::hasTable('website_contents')) {
      return '';
    }

    $record = WebsiteContent::first();

    if (!$record) {
      return '';
    }

    return $record->$column ?? '';
  } catch (\Throwable $e) {
    return '';
  }
}

function formatUsername(string $username): string
{
  return str_starts_with($username, '@')
    ? substr($username, 1)
    : $username;
}

function getCompanyAbbreviation(): string
{
  $name = config('app.name');
  if (!$name) return 'PG';

  $words = explode(' ', $name);
  $abbr = '';

  foreach ($words as $word) {
    $abbr .= strtoupper(substr($word, 0, 1));
  }

  return $abbr;
}

function convertBase64ImagesToFiles($htmlContent)
{
  $dom = new \DOMDocument();
  // Prevent HTML5 parsing errors
  libxml_use_internal_errors(true);
  $dom->loadHTML(mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8'));
  libxml_clear_errors();

  $images = $dom->getElementsByTagName('img');

  // Iterate backwards as we'll be modifying the DOM
  for ($i = $images->length - 1; $i >= 0; $i--) {
    $image = $images->item($i);
    $src = $image->getAttribute('src');

    if (preg_match('/^data:image\/(\w+);base64,/', $src, $type)) {
      // Get the image data
      $imageData = substr($src, strpos($src, ',') + 1);
      $imageType = strtolower($type[1]); // png, jpg, etc

      // Generate unique filename
      $filename = 'email_image_' . uniqid() . '.' . $imageType;

      // Store path - adjust this to your storage configuration
      $path = 'email-images/' . $filename;

      // Decode and store the image
      Storage::put($path, base64_decode($imageData));

      // Get the public URL
      $publicUrl = Storage::url($path);

      // Update the image src in the DOM
      $image->setAttribute('src', url($publicUrl));
    }
  }

  return $dom->saveHTML();
}

function airtimeBundles()
{
  return [
    ['amount' => 100, 'label' => '100'],
    ['amount' => 200, 'label' => '200'],
    ['amount' => 500, 'label' => '500'],
    ['amount' => 1000, 'label' => '1000'],
    ['amount' => 2000, 'label' => '2000'],
    ['amount' => 5000, 'label' => '5000'],
  ];
}


function generateQuidaxEmail(User $user)
{
  $username = $user->username;

  if (!$username) {
    return null;
  }

  $quidaxEmail = $username  . '@swiftpaymfb.com';

  return $quidaxEmail;
}

function truncateText($text, $length = 10, $remainder = '...')
{
  if (strlen($text) <= $length) {
    return $text;
  }
  return substr($text, 0, $length) . $remainder;
}

function getQuidaxSupportedCryptos()
{
  return  [
    'USD',
    'BTC',
    'LTC',
    'ETH',
    'XRP',
    'USDT',
    'DASH',
    'TRX',
    'DOGE',
    // 'XRP', not working well
    'BNB',
    'AXS',
    'SAFEMOON',
    'CAKE',
    'XLM',
    'AAVE',
    'LINK',
    'USDC',
    'SOL',
    'BCH',
    'ADA',
    'DOT'
  ];
}

function createFlutterwaveVirtualAccount(array $details): array
{
  try {
    $flwSecret = (string) env('FLW_LIVE_SECRET_KEY', '');
    if ($flwSecret === '') {
      $flwSecret = (string) env('FLW_SECRET_KEY', '');
    }
    $flwBaseUrl = rtrim((string) env('FLW_API_URL', ''), '/');

    if ($flwBaseUrl === '' || $flwSecret === '') {
      return [
        'status' => 'error',
        'message' => 'Flutterwave credentials not configured'
      ];
    }

    $email = $details['email'] ?? 'customer' . time() . '@payzenter.local';
    $bvn = $details['bvn'] ?? '22325148306';
    $txRef = $details['merchant_ref'] ?? ('flw_va_' . time() . '_' . bin2hex(random_bytes(4)));
    $narration = $details['narration'] ?? ('PayZenter ' . ($details['firstname'] ?? 'Customer'));
    $isPermanent = $details['is_permanent'] ?? false;
    $amount = $details['amount'] ?? null;

    $payload = [
      "email" => $email,
      "bvn" => $bvn,
      "tx_ref" => $txRef,
      "narration" => $narration,
      "is_permanent" => $isPermanent,
    ];

    if ($amount !== null) {
      $payload["amount"] = (int) $amount;
    }

    $abbr = getCompanyAbbreviation();
    $firstname = $details['firstname'] ?? 'Customer';
    if (!str_starts_with($firstname, $abbr)) {
      $firstname = $abbr . " " . $firstname;
    }
    $lastname = $details['lastname'] ?? 'PayZenter';

    if (str_starts_with($flwBaseUrl, 'https://api.flutterwave.com') || str_contains($flwBaseUrl, 'flutterwave.com')) {
      $res = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $flwSecret
      ])->post($flwBaseUrl . "/virtual-account-numbers", $payload);
    } else {
      $payload["firstname"] = $firstname;
      $payload["lastname"] = $lastname;
      if (!isset($payload["phone_number"])) {
        $payload["phone_number"] = $details['phone'] ?? '08000000000';
      }
      $res = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $flwSecret
      ])->post($flwBaseUrl . "/v2/virtual-accounts", $payload);
    }

    $data = $res->json();

    if ($res->successful() && ($data['status'] ?? null) === 'success') {
      $d = $data['data'] ?? [];
      $accountNo = $d['account_number'] ?? $d['accountNumber'] ?? $d['nuban'] ?? null;
      $bankName = $d['bank_name'] ?? $d['bankName'] ?? 'WEMA Bank';
      $accountName = $d['account_name'] ?? $d['accountName'] ?? ($firstname . ' ' . $lastname);
      $flwRef = $d['reference'] ?? $d['flw_ref'] ?? $txRef;
      $note = $d['note'] ?? ($d['meta']['authorization']['note'] ?? null);

      return [
        'status' => 'success',
        'data' => [
          'accountNo' => $accountNo,
          'bankName' => $bankName,
          'firstname' => explode(' ', $accountName, 2)[0] ?? $firstname,
          'lastname' => explode(' ', $accountName, 2)[1] ?? $lastname,
          'accountName' => $accountName,
          'reference' => $flwRef,
          'order_ref' => $txRef,
          'note' => $note,
          'raw' => $d,
        ]
      ];
    }

    $msg = $data['message'] ?? ($data['error'] ?? 'Unable to generate Flutterwave virtual account');
    return [
      'status' => 'error',
      'message' => $msg,
      'raw' => $data,
    ];
  } catch (\Throwable $th) {
    return [
      'status' => 'error',
      'message' => $th->getMessage()
    ];
  }
}

function createFlutterwaveHostedPayment(array $details): array
{
  try {
    $flwSecret = (string) env('FLW_LIVE_SECRET_KEY', '');
    if ($flwSecret === '') {
      $flwSecret = (string) env('FLW_SECRET_KEY', '');
    }
    $flwBaseUrl = rtrim((string) env('FLW_API_URL', ''), '/');

    if ($flwBaseUrl === '' || $flwSecret === '') {
      return [
        'status' => 'error',
        'message' => 'Flutterwave credentials not configured'
      ];
    }

    $txRef = $details['tx_ref'] ?? ('flw_hosted_' . time() . '_' . bin2hex(random_bytes(4)));
    $amount = (float) ($details['amount'] ?? 0);
    $currency = $details['currency'] ?? 'NGN';
    $email = $details['email'] ?? 'customer' . time() . '@payzenter.local';
    $firstname = $details['firstname'] ?? 'Customer';
    $lastname = $details['lastname'] ?? 'PayZenter';
    $phone = $details['phone'] ?? null;
    $redirectUrl = $details['redirect_url'] ?? config('app.url');
    $narration = $details['narration'] ?? ('PayZenter Payment ' . $txRef);
    $title = $details['title'] ?? 'PayZenter Secure Payment';
    $description = $details['description'] ?? $narration;
    $logo = $details['logo'] ?? null;
    $meta = $details['meta'] ?? [];

    $payload = [
      "tx_ref" => $txRef,
      "amount" => $amount,
      "currency" => $currency,
      "redirect_url" => $redirectUrl,
      "customer" => [
        "email" => $email,
        "name" => trim($firstname . ' ' . $lastname),
      ],
      "customizations" => [
        "title" => $title,
        "description" => $description,
      ],
      "meta" => (object) $meta,
    ];

    if ($phone !== null && $phone !== '') {
      $payload["customer"]["phonenumber"] = $phone;
    }

    if ($logo !== null && $logo !== '') {
      $payload["customizations"]["logo"] = $logo;
    }

    if (isset($details['payment_options'])) {
      $payload["payment_options"] = $details['payment_options'];
    }

    $res = Http::withHeaders([
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer ' . $flwSecret
    ])->post($flwBaseUrl . "/payments", $payload);

    $data = $res->json();

    if ($res->successful() && ($data['status'] ?? null) === 'success') {
      $d = $data['data'] ?? [];
      return [
        'status' => 'success',
        'data' => [
          'link' => $d['link'] ?? null,
          'reference' => $txRef,
          'flw_ref' => $d['flw_ref'] ?? null,
          'amount' => $amount,
          'currency' => $currency,
          'raw' => $d,
        ]
      ];
    }

    $msg = $data['message'] ?? ($data['error'] ?? 'Unable to generate Flutterwave hosted payment link');
    return [
      'status' => 'error',
      'message' => $msg,
      'raw' => $data,
    ];
  } catch (\Throwable $th) {
    return [
      'status' => 'error',
      'message' => $th->getMessage()
    ];
  }
}

function getPublicStorageUrl(?string $path): ?string
{
  if (empty($path)) {
    return null;
  }
  if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
    return $path;
  }
  $cleanPath = ltrim(str_replace('public/', '', $path), '/');
  if (str_starts_with($cleanPath, 'storage/')) {
    return '/' . $cleanPath;
  }
  return '/storage/' . $cleanPath;
}
