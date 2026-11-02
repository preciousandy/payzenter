<?php

use App\Helpers\SettingHelper;
use App\Http\Controllers\Api\UtilityController;
use App\Http\Controllers\CryptoWebhookController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransferWebhookController;
use App\Http\Controllers\WebhookController;
use App\Livewire\AboutUs;
use App\Livewire\AccountLevels;
use App\Livewire\AddCardDetails;
use App\Livewire\Admin\AllCablePackages;
use App\Livewire\Admin\AllCryptoCurrency;
use App\Livewire\Admin\AllDataPlans;
use App\Livewire\Admin\AllElectricProviders;
use App\Livewire\Admin\AllFeedbacks;
use App\Livewire\Admin\AllInternationalTransfers;
use App\Livewire\Admin\AllNetworkProviders;
use App\Livewire\Admin\AllPaymentTransactions;
use App\Livewire\Admin\ApiKeys;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\EditProfile as AdminEditProfile;
use App\Livewire\Admin\ErrorLogs;
use App\Livewire\Admin\ExchangeRate\Index as ExchangeRateIndex;
use App\Livewire\Admin\FxOrder;
use App\Livewire\Admin\LiveSupport\Index as LiveSupportIndex;
use App\Livewire\Admin\Login;
use App\Livewire\Admin\PendingKyc;
use App\Livewire\Admin\Settings;
use App\Livewire\Admin\Transactions;
use App\Livewire\Admin\Users;
use App\Livewire\Admin\Users\Show as UsersShow;
use App\Livewire\Admin\WebsiteContent;
use App\Livewire\AirtimeData;
use App\Livewire\AjoContributions\Create as AjoContributionsCreate;
use App\Livewire\AjoContributions\History as AjoContributionsHistory;
use App\Livewire\AjoContributions\Index as AjoContributionsIndex;
use App\Livewire\AjoContributions\Invite as AjoContributionsInvite;
use App\Livewire\AjoContributions\Join;
use App\Livewire\AjoContributions\Show as AjoContributionsShow;
use App\Livewire\AjoContributions\Success;
use App\Livewire\AjoContributions\Welcome as AjoContributionsWelcome;
use App\Livewire\AjoSavings\Create as AjoSavingsCreate;
use App\Livewire\AjoSavings\History as AjoSavingsHistory;
use App\Livewire\AjoSavings\Index as AjoSavingsIndex;
use App\Livewire\AjoSavings\Show as AjoSavingsShow;
use App\Livewire\AjoSavings\Transactions as AjoSavingsTransactions;
use App\Livewire\AjoSavings\Welcome as AjoSavingsWelcome;
use App\Livewire\Assets\Show as AssetsShow;
use App\Livewire\BankTransfer;
use App\Livewire\Beneficiaries\Index as BeneficiariesIndex;
use App\Livewire\Bills\Airtime;
use App\Livewire\Bills\Betting;
use App\Livewire\Bills\Cable;
use App\Livewire\Bills\Data;
use App\Livewire\Bills\Electricity;
use App\Livewire\Bills\Index;
use App\Livewire\BuDaChange\Buy\Crypto\BuyNow;
use App\Livewire\BuDaChange\Buy\Crypto\Index as BuyCryptoIndex;
use App\Livewire\BuDaChange\Buy\Welcome as BuyWelcome;
use App\Livewire\BuDaChange\Index as BuDaChangeIndex;
use App\Livewire\BuDaChange\SellCrypto;
use App\Livewire\BuDaChange\SellCrytoOrder;
use App\Livewire\BuDaChange\Welcome as BuDaChangeWelcome;
use App\Livewire\BuyFx\Index as BuyFxIndex;
use App\Livewire\BuyFx\Show as BuyFxShow;
use App\Livewire\BVN\Validate;
use App\Livewire\BVN\Verify;
use App\Livewire\Cards\Index as CardsIndex;
use App\Livewire\ComingSoon;
use App\Livewire\ContactUs;
use App\Livewire\ConvertCurrency;
use App\Livewire\CurrencyRates;
use App\Livewire\Feedback\Create as FeedbackCreate;
use App\Livewire\Fiats\Show as FiatsShow;
use App\Livewire\ForgotPassword;
use App\Livewire\FundWallet\Failed;
use App\Livewire\FundWallet\Index as FundWalletIndex;
use App\Livewire\FundWallet\Success as FundWalletSuccess;
use App\Livewire\GiftCard\Buy;
use App\Livewire\GiftCard\Countries;
use App\Livewire\GiftCard\Products;
use App\Livewire\GroupSavings\History as GroupSavingsHistory;
use App\Livewire\GroupSavings\Success as GroupSavingsSuccess;
use App\Livewire\GroupSavings\Welcome as GroupSavingsWelcome;
use App\Livewire\Holdings\Assets;
use App\Livewire\Holdings\Create as HoldingsCreate;
use App\Livewire\Holdings\History;
use App\Livewire\Holdings\Index as HoldingsIndex;
use App\Livewire\Holdings\Welcome as HoldingsWelcome;
use App\Livewire\Home;
use App\Livewire\InterestSavings\History as InterestSavingsHistory;
use App\Livewire\InterestSavings\Welcome as InterestSavingsWelcome;
use App\Livewire\Internationaltrandfer\Index as InternationaltrandferIndex;
use App\Livewire\Internationaltrandfer\Receive;
use App\Livewire\Internationaltrandfer\Send;
use App\Livewire\Internationaltrandfer\Welcome as InternationaltrandferWelcome;
use App\Livewire\Internationaltransfer\SendAbroad;
use App\Livewire\Investments\Create as InvestmentsCreate;
use App\Livewire\Investments\CryptoShow;
use App\Livewire\Investments\History as InvestmentsHistory;
use App\Livewire\Investments\Index as InvestmentsIndex;
use App\Livewire\Investments\Portfolio;
use App\Livewire\Investments\StocksShow as InvestmentsStocksShow;
use App\Livewire\Investments\Welcome as InvestmentsWelcome;
use App\Livewire\Kyc\Verify as KycVerify;
use App\Livewire\LiveChat;
use App\Livewire\LiveChatWelcome;
use App\Livewire\ManageRolesAndPermission;
use App\Livewire\Menu;
use App\Livewire\Metals\Show as MetalsShow;
use App\Livewire\MultipleTransferReceipt;
use App\Livewire\Onboarding;
use App\Livewire\OTP\Validate as OTPValidate;
use App\Livewire\OTP\Verify as OTPVerify;
use App\Livewire\Pages\ContactSupport;
use App\Livewire\PrivacyPolicy;
use App\Livewire\Referrals;
use App\Livewire\ResetPassword;
use App\Livewire\Savings\GroupSavings;
use App\Livewire\Savings\GroupSavings\Create as GroupSavingsCreate;
use App\Livewire\Savings\GroupSavings\Invite;
use App\Livewire\Savings\GroupSavings\Show as GroupSavingsShow;
use App\Livewire\Savings\Index as SavingsIndex;
use App\Livewire\Savings\InterestSavings;
use App\Livewire\Savings\InterestSavings\Create;
use App\Livewire\Savings\InterestSavings\Show;
use App\Livewire\SellFx\Index as SellFxIndex;
use App\Livewire\SellFx\Show as SellFxShow;
use App\Livewire\SendToAfrica\Index as SendToAfricaIndex;
use App\Livewire\SendToAfrica\Welcome as SendToAfricaWelcome;
use App\Livewire\Stocks\Show as StocksShow;
use App\Livewire\SwiftPay\Bank;
use App\Livewire\TermsAndCondition;
use App\Livewire\TransactionHistory;
use App\Livewire\TransactionReceipt;
use App\Livewire\Transfer\Bank\Beneficiaries as BankBeneficiaries;
use App\Livewire\Transfer\Index as TransferIndex;
use App\Livewire\Transfer\Multiple;
use App\Livewire\Transfer\Receipt;
use App\Livewire\Transfer\SwiftPay;
use App\Livewire\Transfer\SwiftPay\Beneficiaries;
use App\Livewire\Transfer\SwiftPay\User;
use App\Livewire\TransferSwiftPay\Receipt as TransferSwiftPayReceipt;
use App\Livewire\Users\ChangePassword;
use App\Livewire\Users\ChangeFitPayTag;
use App\Livewire\Users\ChangeTansferPin;
use App\Livewire\Users\CreateAccountNumber;
use App\Livewire\Users\EditProfile;
use App\Livewire\Users\Profile;
use App\Livewire\Users\Verification;
use App\Livewire\VerifyEmail;
use App\Livewire\Welcome;
use App\Models\AjoContribution;
use App\Models\AjoContributionMember;
use App\Models\AjoContributionRound;
use App\Models\ApiKey;
use App\Models\Cable as ModelsCable;
use App\Models\CablePackage;
use App\Models\CryptoCurrency;
use App\Models\DataPlan;
use App\Models\ElectricProvider;
use App\Models\ExchangeRate;
use App\Models\Holdings;
use App\Models\Investment;
use App\Models\MetalRate;
use App\Models\Network;
use App\Models\ReloadlyCountry;
use App\Models\StockPrice;
use App\Models\User as ModelsUser;
use App\Models\UserTransaction;
use App\Notifications\NotifyUser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', Welcome::class)->name('welcome');
Route::get('/about-us', AboutUs::class)->name('about-us');
Route::get('/contact-us', ContactUs::class)->name('contact-us');
Route::get('/terms-and-conditions', TermsAndCondition::class)->name('terms-and-conditions');
Route::get('/privacy-policy', PrivacyPolicy::class)->name('privacy-policy');

Route::get('fund-wallet/success-page', FundWalletSuccess::class)->name('fund-wallet.success-page');
Route::get('fund-wallet/failed', Failed::class)->name('fund-wallet.failed');
Route::get('forgot-password', ForgotPassword::class)->name('forgot_password');
Route::get('reset-password/{token}', ResetPassword::class)->name('password.reset');

Route::get('coming-soon', ComingSoon::class)->name('coming-soon');

// Route::get('/give-admin', function () {
//     auth()->user()->syncRoles('admin');

//     return 'done';
// });
Route::get('/test', function () {
    // $hashed_password = Hash::make('password');

    // dd($hashed_password);

    return 'done';
});

Route::get('/test-redis', function () {
    Cache::put('test_key', 'Hello Redis!', now()->addMinutes(5));

    return Cache::get('test_key');
});

Route::get('/preview-email', function () {
    $user = ModelsUser::first(); // Or however you want to get a user
    $notification = new NotifyUser('test something', "Test Subject");


    return $notification->toMail($user)->render();
});

// Route::get('/symlink', function () {
//     // dd($_SERVER['DOCUMENT_ROOT']);
//     $target = $_SERVER['DOCUMENT_ROOT'] . '/storage/app/public';
//     $link = $_SERVER['DOCUMENT_ROOT'] . '/public/storage';
//     symlink($target, $link);
//     echo "Done";
Route::get('/storage/{path}', function ($path) {
    if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
        abort(404);
    }
    return \Illuminate\Support\Facades\Storage::disk('public')->response($path);
})->where('path', '.*')->name('storage.local');

Route::get('/clear-cache', function () {
    $out = [];
    if (function_exists('opcache_reset')) {
        @opcache_reset();
        $out[] = "✓ OPcache reset.";
    }
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    $out[] = "✓ Laravel cache, routes, config, and views cleared.";

    try {
        $ps = new \App\Services\PaystackService();
        $key = config('paystack.secret_key') ?? env('PAYSTACK_SECRET_KEY', '');
        $keyPrefix = substr($key, 0, 8);
        $out[] = "Paystack key mode: " . ($keyPrefix === 'sk_live_' ? 'LIVE MODE (sk_live_***)' : 'TEST MODE (' . $keyPrefix . '***)');

        $res = $ps->initializeTransaction(100.0, 'test@payzenter.com', 'test_ping_' . time(), url('/'));
        $out[] = "Paystack initialize response: " . json_encode($res);
    } catch (\Throwable $e) {
        $out[] = "Paystack exception: " . $e->getMessage();
    }

    return response(implode("\n", $out), 200)->header('Content-Type', 'text/plain');
});

Route::get('/fix-merchant-balance', function () {
    $tx = \App\Models\GatewayTransaction::where('reference', 'like', 'VA_TX_%')
        ->where('status', 'success')
        ->latest('id')
        ->first();

    if (!$tx) {
        return response('No recent VA transaction found.', 404);
    }

    $merchant = $tx->merchant;
    if (!$merchant) {
        return response('Merchant not found.', 404);
    }

    $oldBalance = (float) $merchant->wallet_balance;
    // Overcredited amount in excess of real Naira value: ($tx->amount - ($tx->amount / 100))
    // e.g. 10,000 - 100 = 9,900
    $overcredit = $tx->amount - ($tx->amount / 100);
    $merchant->wallet_balance = max(0, $oldBalance - $overcredit);
    $merchant->save();

    // Also update the UserTransaction record amount to Naira
    $userTx = \App\Models\UserTransaction::where('reference', $tx->reference)->latest('id')->first();
    if ($userTx && (float) $userTx->amount == (float) $tx->amount) {
        $userTx->amount = $tx->amount / 100;
        $userTx->save();
    }

    return response("✓ Successfully corrected merchant '{$merchant->name}' balance from ₦" . number_format($oldBalance, 2) . " to ₦" . number_format($merchant->wallet_balance, 2) . "!", 200)
        ->header('Content-Type', 'text/plain');
});

Route::post('/webhook/flutterwave', [WebhookController::class, 'handleFlutterwave'])->name('webhook');
Route::post('/webhook/fund-walllet', [TransferWebhookController::class, 'handleFundWallet'])->name('webhook.fund-wallet');
Route::post('/webhook/crypto', [CryptoWebhookController::class, 'handleWebhook'])->name('crypto-webhook');
Route::post('/webhook/palmpay', [\App\Http\Controllers\PalmPayWebhookController::class, 'handle'])->name('webhook.palmpay');
Route::post('/webhook/udara360', [\App\Http\Controllers\Udara360WebhookController::class, 'handle'])->name('webhook.udara360');
Route::post('/webhook/paystack', [WebhookController::class, 'handlePaystack'])->name('webhook.paystack');

Route::get('/gateway/flutterwave/callback/{reference}', [\App\Http\Controllers\Api\Gateway\PaymentsController::class, 'flutterwaveCallback'])->name('gateway.flutterwave.callback');

Route::get('/pay/{reference}', \App\Livewire\Gateway\PaymentLinkCheckout::class)->name('gateway.pay');

Route::get('/visit-live-chat', [UtilityController::class, 'liveChat']);
Route::get('/visit-fund-wallet', [UtilityController::class, 'fundWallet']);

Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404, 'File not found');
    }
    $mime = mime_content_type($filePath) ?: 'application/octet-stream';
    return response()->file($filePath, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*')->name('storage.file');


Route::middleware(['auth', 'is_kyc_verified', 'check.account.status',  'is_login_verified'])->group(function () {
    // Route::get('/menu', Menu::class)->name('menu');

    Route::get('/transaction-history', TransactionHistory::class)->name('transaction-history');
    Route::get('/transaction-history/receipt/{hash_id}', TransactionReceipt::class)->name('transaction-receipt');


    Route::get('/verify-login', VerifyEmail::class)->name('verify-login')->withoutMiddleware('is_login_verified');

    // bills
    Route::get('bills', Index::class)->name('bills.index')->middleware('if_owing_ajo_contribution');
    Route::get('bills/airtime-and-data', AirtimeData::class)->name('bills.airtime-and-data');
    Route::get('bills/cable', Cable::class)->name('bills.cable');
    Route::get('bills/electricity', Electricity::class)->name('bills.electricity');
    Route::get('bills/betting', Betting::class)->name('bills.betting');

    // profile
    Route::get('/profile', Profile::class)->name('profile');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['otp_verification'])->group(function () {
        Route::get('/profile/edit', EditProfile::class)->name('profile.edit');
        Route::get('/profile/change-password', ChangePassword::class)->name('profile.change-password');
        Route::get('/profile/change-transfer-pin', ChangeTansferPin::class)->name('profile.change-transfer-pin');
    });

    // fund wallet
    Route::get('/fund-wallet', FundWalletIndex::class)->name('user.fund-wallet');
    Route::get('/fund-wallet/paypal/process-payment', [PayPalController::class, 'processTransaction'])->name('user.paypal.proccess-transaction');
    Route::post('/user/add-money', [PaymentController::class, 'redirectToGateway'])->name('user.add-money');
    Route::get('/user/add-money/callback', [PaymentController::class, 'handleGatewayCallback'])->name('user.add-money.callback');
    Route::post('/user/fund-wallet/paystack-card', [PaymentController::class, 'redirectToPaystackCard'])->name('user.paystack.card');
    Route::get('/user/fund-wallet/paystack/callback', [PaymentController::class, 'handlePaystackCallback'])->name('user.paystack.callback');

    // transfer
    Route::get('/transfer', TransferIndex::class)->name('transfer.index')->middleware('if_owing_ajo_contribution');
    Route::get('/transfer/beneficiaries/bank', BankBeneficiaries::class)->name('transfer.bank.beneficiaries');
    Route::get('/transfer/bank', Bank::class)->name('transfer.bank');
    Route::get('/transfer/receipt/{hash_id}', Receipt::class)->name('transfer.receipt');
    // SwiftPay and Multiple transfers removed for gateway-only experience
    Route::get('/transfer/multiple/receipt/{id}', MultipleTransferReceipt::class)->name('transfer.multiple.receipt');

    // Beneficiaries
    Route::get('/beneficiaries', BeneficiariesIndex::class)->name('beneficiaries.index');

    Route::get('/savings', SavingsIndex::class)->name('savings.index');
    Route::get('/savings/interest-savings', InterestSavings::class)->name('savings.interest-savings');
    Route::get('/savings/interest-savings/welcome', InterestSavingsWelcome::class)->name('savings.interest-savings.welcome');
    Route::get('/savings/interest-savings/create', Create::class)->name('savings.interest-savings.create');
    Route::get('/savings/interest-savings/history', InterestSavingsHistory::class)->name('savings.interest-savings.history');
    Route::get('/savings/interest-savings/{hashId}', Show::class)->name('savings.interest-savings.show');

    Route::get('/savings/group-savings', GroupSavings::class)->name('savings.group-savings');
    Route::get('/savings/group-savings/welcome', GroupSavingsWelcome::class)->name('savings.group-savings.welcome');
    Route::get('/savings/group-savings/create', GroupSavingsCreate::class)->name('savings.group-savings.create');
    Route::get('/savings/group-savings/history', GroupSavingsHistory::class)->name('savings.group-savings.history');
    Route::get('/savings/group-savings/{hashId}', GroupSavingsShow::class)->name('savings.group-savings.show');
    Route::get('/savings/group-savings/invite/{hashId}', Invite::class)->name('savings.group-savings.invite');
    Route::get('/savings/group-savings/success/{hashId}', GroupSavingsSuccess::class)->name('savings.group-savings.success');

    Route::get('cards', CardsIndex::class)->name('cards.index');

    // holdings
    Route::get('holdings', HoldingsIndex::class)->name('holdings.index');
    Route::get('holdings/welcome', HoldingsWelcome::class)->name('holdings.welcome');
    Route::get('holdings/history', History::class)->name('holdings.history');
    Route::get('holdings/create', HoldingsCreate::class)->name('holdings.create');
    Route::get('holdings/assets', Assets::class)->name('holdings.assets');
    Route::get('holdings/assets/{assetUID}', AssetsShow::class)->name('holdings.assets.show');

    Route::get('stocks/{symbol}', StocksShow::class)->name('stocks.show')->lazy();
    Route::get('fiats/{fiat}', FiatsShow::class)->name('fiats.show');
    Route::get('metals/{symbol}', MetalsShow::class)->name('metals.show');

    // investments
    Route::get('/investments', InvestmentsIndex::class)->name('investments.index');
    Route::get('/investments/welcome', InvestmentsWelcome::class)->name('investments.welcome');
    Route::get('/investments/portfolio', Portfolio::class)->name('investments.portfolio');
    Route::get('/investments/create', InvestmentsCreate::class)->name('investments.create');
    Route::get('/investments/history', InvestmentsHistory::class)->name('investments.history');


    Route::get('/investments/crypto/{hash_id}', CryptoShow::class)->name('investments.crypto.show');
    Route::get('/investments/stock/{hash_id}', InvestmentsStocksShow::class)->name('investments.stocks.show')->lazy();

    // Ajo Savings
    Route::get('ajo-savings', AjoSavingsIndex::class)->name('ajo-savings.index');
    Route::get('ajo-savings/welcome', AjoSavingsWelcome::class)->name('ajo-savings.welcome');
    Route::get('ajo-savings/create', AjoSavingsCreate::class)->name('ajo-savings.create');
    Route::get('ajo-savings/history', AjoSavingsHistory::class)->name('ajo-savings.history');
    Route::get('ajo-savings/{hash_id}', AjoSavingsShow::class)->name('ajo-savings.show');
    Route::get('ajo-savings/{hash_id}/transactions', AjoSavingsTransactions::class)->name('ajo-savings.transactions');

    Route::get('/feedback/create', FeedbackCreate::class)->name('feedback.create');

    // international transfer
    Route::get('/international-transfer', InternationaltrandferIndex::class)->name('international-transfer.index');
    Route::get('/international-transfer/welcome', InternationaltrandferWelcome::class)->name('international-transfer.welcome');
    Route::get('/international-transfer/send-abroad', SendAbroad::class)->name('international-transfer.send-abroad');
    // Route::get('/international-transfer/send-local', Send::class)->name('international-transfer.send-local');


    // send to africa
    Route::get('/send-to-africa', SendToAfricaIndex::class)->name('send-to-africa.index');
    Route::get('/send-to-africa/welcome', SendToAfricaWelcome::class)->name('send-to-africa.welcome');

    // Ajo Contributions
    Route::get('ajo-contributions', AjoContributionsIndex::class)->name('ajo-contributions.index');
    Route::get('ajo-contributions/welcome', AjoContributionsWelcome::class)->name('ajo-contributions.welcome');
    Route::get('ajo-contributions/create', AjoContributionsCreate::class)->name('ajo-contributions.create');
    Route::get('ajo-contributions/create/success/{hashId}', Success::class)->name('ajo-contributions.success');
    Route::get('ajo-contributions/join', Join::class)->name('ajo-contributions.join');
    Route::get('ajo-contributions/history', AjoContributionsHistory::class)->name('ajo-contributions.history');
    Route::get('ajo-contributions/{hashId}', AjoContributionsShow::class)->name('ajo-contributions.show');
    Route::get('ajo-contributions/invite/{hashId}', AjoContributionsInvite::class)->name('ajo-contributions.invite');

    // sell crypto
    Route::get('bureau-da-change/sell-crypto', BuDaChangeIndex::class)->name('bureau-da-change.sell-crypto.index');
    Route::get('bureau-da-change/sell-welcome', BuDaChangeWelcome::class)->name('bureau-da-change.sell-welcome')->middleware('if_owing_ajo_contribution');
    Route::get('bureau-da-change/sell-crypto/{crypto}', SellCrypto::class)->name('bureau-da-change.sell-crypto.sell')->lazy();


    // buy crypto
    Route::get('bureau-da-change/buy', BuyWelcome::class)->name('bureau-da-change.buy-welcome')->middleware('if_owing_ajo_contribution');
    Route::get('bureau-da-change/buy-crypto', BuyCryptoIndex::class)->name('bureau-da-change.buy-crypto');
    Route::get('bureau-da-change/buy-crypto/{crypto}', BuyNow::class)->name('bureau-da-change.buy-crypto.buy');

    Route::withoutMiddleware(['is_kyc_verified', 'is_login_verified'],)->group(function () {
        Route::get('home', Home::class)->name('home');
        // onboarding
        Route::get('/verification', Verification::class)->name('verification');
        Route::get('/onboarding', Onboarding::class)->name('onboarding');

        // Route::get('create-account-number', CreateAccountNumber::class)->name('create-account-number');

        Route::get('/profile/change-fitpay-tag', ChangeFitPayTag::class)->name('profile.change-fitpay-tag');


        // bvn verification
        // Route::get('bvn/verify', Verify::class)->name('bvn.verify');
        Route::get('bvn/validate', Validate::class)->name('bvn.validate');

        Route::get('kyc/verify', KycVerify::class)->name('kyc.verify');
    });

    Route::get('/gateway/transactions', \App\Livewire\Gateway\Transactions::class)->name('gateway.transactions');
    Route::get('/gateway/virtual-accounts', \App\Livewire\Gateway\VirtualAccounts::class)->name('gateway.virtual-accounts');
    Route::get('/gateway/webhooks', \App\Livewire\Gateway\WebhookEvents::class)->name('gateway.webhooks');
    Route::get('/gateway/payment-links', \App\Livewire\Gateway\PaymentLinks::class)->name('gateway.payment-links');
    Route::get('/gateway/api-keys', \App\Livewire\Gateway\ApiKeys::class)->name('gateway.api-keys');
    Route::get('/gateway/preferences', \App\Livewire\Gateway\Preferences::class)->name('gateway.preferences');
    Route::get('/gateway/docs', \App\Livewire\Gateway\Documentation::class)->name('gateway.docs');

    // buy fx
    Route::get('bureau-da-change/buy-fx', BuyFxIndex::class)->name('bureau-da-change.buy-fx');
    Route::get('bureau-da-change/buy-fx/{currencyCode}', BuyFxShow::class)->name('bureau-da-change.buy-fx.show');

    Route::get('bureau-da-change/sell-fx', SellFxIndex::class)->name('bureau-da-change.sell-fx');
    Route::get('bureau-da-change/sell-fx/{currencyCode}', SellFxShow::class)->name('bureau-da-change.sell-fx.show');


    Route::get('gift-cards/countries', Countries::class)->name('gift-cards.countries');
    Route::get('gift-cards/products/{countryCode}', Products::class)->name('gift-cards.products')->lazy();
    Route::get('gift-cards/buy/{productID}', Buy::class)->name('gift-cards.buy')->lazy();


    Route::get('referrals', Referrals::class)->name('referrals');

    Route::get('live-chat/welcome', LiveChatWelcome::class)->name('live-chat-welcome')->withoutMiddleware('is_login_verified');
    Route::get('live-chat/{hashed_topic_id}', LiveChat::class)->name('live-chat')->withoutMiddleware('is_login_verified');

    Route::get('bank-transfer', BankTransfer::class)->name('bank-transfer');
    Route::get('currency-rates', CurrencyRates::class)->name('currency-rates');

    Route::get('otp/verify', OTPVerify::class)->name('otp.verify');
    Route::get('otp/validate', OTPValidate::class)->name('otp.validate');

    Route::get('add-card-details', AddCardDetails::class)->name('add-card-details');

    Route::get('/account-levels', AccountLevels::class)->name('account-levels');

    Route::get('/contact-support', ContactSupport::class)->name('contact-support');
});

// Admin login
Route::get('powerhouse/login', Login::class)->name('admin.login');

Route::get('powerhouse', function () {
    return redirect()->route('admin.dashboard');
});

Route::group(['prefix' => 'powerhouse', 'middleware' => ['auth']], function () {
    Route::get('/dashboard', Dashboard::class)->name('admin.dashboard');
    Route::get('/emails', \App\Livewire\Admin\EmailCenter::class)->name('admin.emails');
    Route::get('/users', Users::class)->name('admin.users.index')->middleware('permission:view_users');
    Route::get('/users/{hash_id}', UsersShow::class)->name('admin.users.show')->middleware('permission:view_users');
    Route::get('/transactions', Transactions::class)->name('admin.transactions');
    Route::get('/payment-transactions', AllPaymentTransactions::class)->name('admin.payment-transactions');
    Route::get('/electricity-providers', AllElectricProviders::class)->name('admin.electricity-providers');
    Route::get('/network-providers', AllNetworkProviders::class)->name('admin.network-providers');
    Route::get('/data-plans', AllDataPlans::class)->name('admin.data-plans');
    Route::get('/cable-packages', AllCablePackages::class)->name('admin.cable-packages');
    Route::get('/all-feedbacks', AllFeedbacks::class)->name('admin.all-feedbacks');
    Route::get('/exchange-rates', ExchangeRateIndex::class)->name('admin.exchange-rates');
    Route::get('/international-transfers', AllInternationalTransfers::class)->name('admin.international-transfers');
    Route::get('crypto-currencies', AllCryptoCurrency::class)->name('admin.crypto-currency');
    Route::get('/fx-orders', FxOrder::class)->name('admin.fx-orders');
    Route::get('/settings', Settings::class)->name('admin.settings');
    Route::get('/api-keys', ApiKeys::class)->name('admin.api-keys');
    Route::get('/manage-roles-and-permissions', ManageRolesAndPermission::class)->name('admin.manage-roles-and-permissions');

    Route::get('website-content', WebsiteContent::class)->name('admin.website-content');
    Route::get('kyc-verification', PendingKyc::class)->name('admin.kyc-verification');
    Route::get('error-logs', ErrorLogs::class)->name('admin.error-logs');

    Route::get('live-support', LiveSupportIndex::class)->name('admin.live-support')->middleware('permission:view_live_support');
    Route::get('/settlement-accounts', \App\Livewire\Admin\SettlementAccounts::class)->name('admin.settlement-accounts');

    Route::get('/edit-profile', AdminEditProfile::class)->name('admin.edit-profile');
});

Route::get('/add-rates', function () {
    $res =   Http::withHeaders([
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ])->get(env("EXCHANGE_RATE_API_URL") . "/latest/USD");

    $data = $res->json();

    $rates = $data['conversion_rates'];

    $baseRate = $rates["NGN"];

    $currencies = ExchangeRate::all();

    foreach ($currencies as $currency) {
        $currency->rate = (1 / $rates[$currency->code]) * $baseRate;
        $currency->save();
    }

    return 'done';
});


Route::get('/add-beneficiaries', function () {
    $beneficiaries = [
        [
            'acct_name' => 'Oluwaseun Jones',
            'bank_name' => 'GTBank',
            'acct_number' => '0123456789',
            'type' => 'bank',
            'user_id' => 3
        ],

        [
            'acct_name' => 'Chris Jones',
            'bank_name' => 'UBA',
            'acct_number' => '0123456789',
            'type' => 'bank',
            'user_id' => 3
        ],

        [
            'acct_name' => 'Simon smith',
            'bank_name' => 'First Bank',
            'acct_number' => '0123456789',
            'type' => 'bank',
            'user_id' => 3
        ],

        [
            'acct_name' => 'Joy Sunday',
            'bank_name' => 'Access Bank',
            'acct_number' => '0123456789',
            'type' => 'bank',
            'user_id' => 3
        ],

        [
            'swiftpay_username' => 'crhistalk',
            'type' => 'swiftpay',
            'user_id' => 3
        ],
        [
            'swiftpay_username' => 'tawio_money',
            'type' => 'swiftpay',
            'user_id' => 3
        ],
        [
            'swiftpay_username' => 'ayo_money',
            'type' => 'swiftpay',
            'user_id' => 3
        ],



    ];

    foreach ($beneficiaries as $beneficiary) {
        \App\Models\Beneficiary::create($beneficiary);
    }


    return 'done';
});

Route::get('/add-reloadly-countries', function () {
    $res =   Http::withHeaders([
        'Accept' => 'application/com.reloadly.giftcards-v1+json',
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . env("RELOADLY_ACCESS_TOKEN")
    ])->get(env("RELOADLY_API_URL") . "/countries");

    $data = $res->json();

    dd($data);

    foreach ($data as $country) {
        ReloadlyCountry::create([
            'iso_name' => $country['isoName'],
            'name' => $country['name'],
            'currency_code' => $country['currencyCode'],
            'currency_name' => $country['currencyName'],
            'flag_url' => $country['flagUrl']
        ]);
    }

    return 'done';
});


Route::get('/add-metals', function () {
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

            $metals = $data['metals'];

            foreach ($metals as $key => $value) {
                MetalRate::updateOrCreate(
                    ['metal' => $key],
                    ['price' => $value]
                );
            }
        } else {
            throw new \Exception($data['message'] ?? 'An error occurred');
        }
    } catch (\Throwable $th) {
        throw new \Exception($th->getMessage());
    }

    return 'done';
});

Route::get('/add-stocks', function () {

    try {
        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-RapidAPI-Key' => ApiKey::getApiKey(ApiKey::RAPID_API_KEY),
            'X-RapidAPI-Host' => 'real-time-finance-data.p.rapidapi.com'
        ])->get(env("STOCKS_API_URL") . "/market-trends", [
            'trend_type' => 'MOST_ACTIVE',
        ]);

        $data = $res->json();

        if ($res->successful()) {

            $stocks = $data['data']['trends'];

            foreach ($stocks as $stock) {
                \App\Models\StockPrice::updateOrCreate(
                    ['symbol' => $stock['symbol']],
                    [
                        'company' => $stock['name'],
                        'change' => $stock['change'],
                        'change_percent' => $stock['change_percent'],
                        'price' => $stock['price'],
                    ]
                );
            }
        } else {

            throw new \Exception($data['message'] ?? 'An error occurred');
        }
    } catch (\Throwable $th) {
        throw new \Exception($th->getMessage());
    }

    return 'done';
});

Route::get('/add-crypto', function () {

    try {
        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-RapidAPI-Key' => ApiKey::getApiKey(ApiKey::RAPID_API_KEY),
            'X-RapidAPI-Host' => env('COIN_RANKING_API_HOST')
        ])->get(env("COIN_RANKING_API_URL") . "/coins", [
            'referenceCurrencyUuid' => 'yhjMzLPhuIDl',
            'timePeriod' => '24h',
            'tiers[0]' => 1,
            'limit' => 20,
        ]);

        $data = $res->json();

        if ($res->successful()) {

            $coins = $data['data']['coins'];


            foreach ($coins as $coin) {
                CryptoCurrency::updateOrCreate(
                    ['uuid' => $coin['uuid']],
                    [
                        'name' => $coin['name'],
                        'symbol' => $coin['symbol'],
                        'icon' => $coin['iconUrl'],
                        'price_in_usd' => $coin['price'],
                        'changes' => array_slice($coin['sparkline'], 0, -1),
                        'status' => CryptoCurrency::STATUS_OPEN,
                        'change_percentage' => $coin['change'],
                    ]
                );
            }
        } else {
            throw new \Exception($data['message']);
        }
    } catch (\Throwable $th) {
        throw new \Exception($th->getMessage());
    }

    return 'done';
});

Route::get('/add-data-plans', function () {

    $cables = ModelsCable::all();

    foreach ($cables as $cable) {
        try {
            $url  = env("FLW_API_URL") . "/billers/" . $cable->code . "/items";
            $res = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env('FLW_SECRET_KEY')
            ])->get($url, [
                'country' => 'NG',
            ]);

            $data = $res->json();

            if ($res->successful()) {

                $packages = $data['data'];

                foreach ($packages as $package) {
                    CablePackage::updateOrCreate(
                        ['package_code' => $package['item_code']],
                        [
                            'cable_id' => $cable->id,
                            'name' => $package['biller_name'],
                            'amount' => $package['amount'],
                            'price' => $package['amount'],
                        ]
                    );
                }
            } else {
                throw new \Exception($data['message']);
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }







    return 'done';
});




require __DIR__ . '/auth.php';
