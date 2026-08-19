<?php

use App\Http\Controllers\Api\AirtimeController;
use App\Http\Controllers\Api\AjoContributionController;
use App\Http\Controllers\Api\AjosavingsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BankTransferController;
use App\Http\Controllers\Api\BettingController;
use App\Http\Controllers\Api\BettingControlller;
use App\Http\Controllers\Api\CableController;
use App\Http\Controllers\Api\CryptoExchangeController;
use App\Http\Controllers\Api\ElectricityController;
use App\Http\Controllers\Api\GiftCardsController;
use App\Http\Controllers\Api\GroupSavingsController;
use App\Http\Controllers\Api\HoldingsController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\InterestSavingsController;
use App\Http\Controllers\Api\InternationationalTransferController;
use App\Http\Controllers\Api\InvestmentsController;
use App\Http\Controllers\Api\KycController;
use App\Http\Controllers\Api\MobileDataController;
use App\Http\Controllers\Api\MultipleTransferController;
use App\Http\Controllers\Api\OnboardingController;
use App\Http\Controllers\Api\PushNotificationController;
use App\Http\Controllers\Api\ReferralController;
use App\Http\Controllers\Api\SavingsController;
use App\Http\Controllers\Api\SwiftPayTransferController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UtilityController;
use App\Http\Controllers\Api\Gateway\MerchantApiKeyController;
use App\Http\Controllers\Api\Gateway\PaymentsController;
use App\Http\Controllers\Api\Gateway\VirtualAccountController;
use App\Http\Controllers\BureauDeChangeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:10,1');

Route::middleware(['auth:sanctum',])->group(function () {

    // onboarding
    Route::post('/onboarding/resend-otp', [OnboardingController::class, 'resendOTP']);
    Route::post('/onboarding/verify-otp', [OnboardingController::class, 'verifyOTP']);
    Route::post('/onboarding/create-pin', [OnboardingController::class, 'createPin']);
    Route::post('/onboarding/create-account-number', [OnboardingController::class, 'createAccountNumber']);

    Route::post('logout', [AuthController::class, 'logout']);


    // kyc
    Route::post('/kyc/submit-step-1', [KycController::class, 'submitKycStep1']);
    Route::post('/kyc/submit-step-2', [KycController::class, 'submitKycStep2']);
    Route::post('/kyc/submit-step-3', [KycController::class, 'submitKycStep3']);

    Route::get('/home', [HomeController::class, 'index']);

    Route::get('user', [UserController::class, 'user']);
    Route::post('user/update-profile', [UserController::class, 'updateProfile']);
    Route::post('user/update-profile-photo', [UserController::class, 'updateProfilePhoto']);
    Route::post('user/change-password', [UserController::class, 'changePassword']);
    Route::post('user/change-transaction-pin', [UserController::class, 'changeTransactionPin']);
    Route::post('user/change-swiftpay-tag', [UserController::class, 'changeSwiftPayTag']);
    Route::post('user/delete-account', [UserController::class, 'deleteAccount']);

    Route::get('transaction-history', [UserController::class, 'transactionHistory']);
    Route::get('notifications', [UserController::class, 'getNotifications']);
    Route::post('notifications/mark-as-read/{notification}', [UserController::class, 'markNotificationsAsRead']);
    Route::post('notifications/mark-all-as-read', [UserController::class, 'markAllNotificationsAsRead']);
    Route::get('verify-pin', [UserController::class, 'verifyTransactionPin']);
    Route::get('get-pin', [UserController::class, 'getUserPin']);
    Route::get('/bank-details', [UserController::class, 'getUserBankDetails']);

    // bank transfer
    Route::get('bank-transfer', [BankTransferController::class, 'index']);
    Route::get('bank-transfer/verify-customer', [BankTransferController::class, 'verifyCustomer']);
    Route::get('bank-transfer/beneficiaries', [BankTransferController::class, 'getBeneficiairies']);
    Route::post('bank-transfer/beneficiaries/toggle-favorite', [BankTransferController::class, 'toggleFavoriteBeneficiary']);
    Route::post('bank-transfer/transfer-money', [BankTransferController::class, 'transferMoney']);

    // SwiftPay and multiple transfers removed for gateway-only experience

    // savings
    Route::get('savings', [SavingsController::class, 'index']);

    // interest savings
    Route::get('interest-savings', [InterestSavingsController::class, 'index']);
    Route::get('interest-savings/create', [InterestSavingsController::class, 'create']);
    Route::post('interest-savings/store', [InterestSavingsController::class, 'store']);
    Route::get('interest-savings/history', [InterestSavingsController::class, 'history']);
    Route::get('interest-savings/show/{id}', [InterestSavingsController::class, 'show']);
    Route::post('interest-savings/update/{id}', [InterestSavingsController::class, 'update']);
    Route::post('interest-savings/add-money', [InterestSavingsController::class, 'addMoney']);
    Route::post('interest-savings/withdraw', [InterestSavingsController::class, 'withdraw']);
    Route::post('interest-savings/end', [InterestSavingsController::class, 'endSavings']);

    // Group savings
    Route::get('group-savings', [GroupSavingsController::class, 'index']);
    Route::get('group-savings/history', [GroupSavingsController::class, 'history']);
    Route::get('group-savings/create', [GroupSavingsController::class, 'create']);
    Route::post('group-savings/store', [GroupSavingsController::class, 'store']);
    Route::get('group-savings/show/{id}', [GroupSavingsController::class, 'show']);
    Route::get('group-savings/invite/{invite_code}', [GroupSavingsController::class, 'invitation']);
    Route::post('group-savings/invite/respond', [GroupSavingsController::class, 'respondToInvitation']);
    Route::post('group-savings/add-money', [GroupSavingsController::class, 'addMoney']);
    Route::post('group-savings/withdraw', [GroupSavingsController::class, 'withdrawMoney']);
    Route::post('group-savings/update', [GroupSavingsController::class, 'updateGroupSavings']);
    Route::post('group-savings/make-member-admin', [GroupSavingsController::class, 'makeMemberAdmin']);
    Route::post('group-savings/remove-member', [GroupSavingsController::class, 'removeMember']);
    Route::post('group-savings/end', [GroupSavingsController::class, 'endGroupSavings']);

    // buy cable
    Route::group(['prefix' => 'bills'], function () {
        Route::get('airtime-data', [AirtimeController::class, 'airtimeDataPage']);
        Route::post('buy-airtime', [AirtimeController::class, 'buyAirtime']);
        Route::post('buy-data', [MobileDataController::class, 'buyData']);
        Route::get('airtime-data/history', [AirtimeController::class, 'airtimeDataHistory']);


        Route::get('cable', [CableController::class, 'index']);
        Route::get('cable/verify-customer', [CableController::class, 'verifyCustomer']);
        Route::get('cable/history', [CableController::class, 'history']);
        Route::post('cable/buy', [CableController::class, 'buyCable']);


        Route::get('electricity', [ElectricityController::class, 'index']);
        Route::get('electricity/verify-customer', [ElectricityController::class, 'verifyCustomer']);
        Route::get('electricity/history', [ElectricityController::class, 'history']);
        Route::post('electricity/buy', [ElectricityController::class, 'buyElectricity']);

        Route::get('betting', [BettingController::class, 'bettingPage']);
        Route::get('betting/history', [BettingController::class, 'history']);
        Route::get('betting/verify-customer', [BettingController::class, 'verifyCustomer']);
        Route::post('betting/fund-account', [BettingController::class, 'fundAccount']);
    });

    // holdings
    Route::get('/holdings', [HoldingsController::class, 'index']);
    Route::get('/holdings/assets', [HoldingsController::class, 'assets']);
    Route::get('/holdings/create', [HoldingsController::class, 'create']);
    Route::post('/holdings/store', [HoldingsController::class, 'store']);
    Route::get('/holdings/history', [HoldingsController::class, 'history']);
    Route::get('/holdings/fiats/show/{code}', [HoldingsController::class, 'showFiat']);
    Route::get('/holdings/metals/show/{symbol}', [HoldingsController::class, 'showMetal']);

    // investments
    Route::get('/investments', [InvestmentsController::class, 'index']);
    Route::get('/investments/create', [InvestmentsController::class, 'create']);
    Route::post('/investments/store', [InvestmentsController::class, 'store']);
    Route::get('/investments/assets', [InvestmentsController::class, 'assets']);
    Route::get('/investments/assets/stock-details', [InvestmentsController::class, 'stockDetails']);
    Route::get('/investments/assets/crypto-details', [InvestmentsController::class, 'cryptoDetails']);
    Route::get('/investments/crypto-investment', [InvestmentsController::class, 'cryptoInvestment']);
    Route::get('/investments/stock-investment', [InvestmentsController::class, 'stockInvestment']);
    Route::get('/investments/history', [InvestmentsController::class, 'history']);

    // ajo savings
    Route::get('/ajo-savings', [AjosavingsController::class, 'index']);
    Route::get('/ajo-savings/create', [AjosavingsController::class, 'create']);
    Route::post('/ajo-savings/store', [AjosavingsController::class, 'store']);
    Route::get('/ajo-savings/show', [AjosavingsController::class, 'show']);
    Route::get('/ajo-savings/transactions', [AjosavingsController::class, 'transactions']);
    Route::get('/ajo-savings/history', [AjosavingsController::class, 'history']);
    Route::post('/ajo-savings/end', [AjosavingsController::class, 'endAjoSavings']);
    Route::post('/ajo-savings/payback', [AjosavingsController::class, 'payback']);



    Route::withoutMiddleware(['is_owing_ajo_contribution_api'],)->group(function () {
        // ajo contributions
        Route::get('/ajo-contributions', [AjoContributionController::class, 'index']);
        Route::post('/ajo-contributions/store', [AjoContributionController::class, 'store']);
        Route::get('/ajo-contributions/show', [AjoContributionController::class, 'show']);
        Route::post('/ajo-contributions/deposit-money', [AjoContributionController::class, 'depositMoney']);
        Route::post('/ajo-contributions/update', [AjoContributionController::class, 'update']);
        Route::post('/ajo-contributions/toggle-auto-save', [AjoContributionController::class, 'toggleAutoSave']);
        Route::post('/ajo-contributions/end', [AjoContributionController::class, 'endAjoContribution']);

        Route::get('/ajo-contributions/history', [AjoContributionController::class, 'history']);
        Route::post('/ajo-contributions/withdraw', [AjoContributionController::class, 'withdraw']);
    });
    Route::post('/ajo-contributions/report-member', [AjoContributionController::class, 'reportMember']);
    Route::get('/ajo-contributions/join', [AjoContributionController::class, 'join']);
    Route::post('/ajo-contributions/join/accept', [AjoContributionController::class, 'acceptInvitation']);

    // bureau de change
    Route::get('/bureau-da-change/buy-page', [BureauDeChangeController::class, 'buyPage']);
    Route::get('/bureau-da-change/buy-currency-page', [BureauDeChangeController::class, 'buyCurrencyPage']);
    Route::post('/bureau-da-change/submit-buy-fx-order', [BureauDeChangeController::class, 'submitBuyFxOrder']);

    Route::get('/bureau-da-change/sell-page', [BureauDeChangeController::class, 'sellPage']);
    Route::get('/bureau-da-change/sell-currency-page', [BureauDeChangeController::class, 'sellCurrencyPage']);
    Route::post('/bureau-da-change/submit-sell-fx-order', [BureauDeChangeController::class, 'submitSellFxOrder']);

    // crypto exchange
    Route::get('/crypto-exchange/buy-page', [CryptoExchangeController::class, 'buyPage']);
    Route::get('/crypto-exchange/buy-crypto-page', [CryptoExchangeController::class, 'buyCryptoPage']);
    Route::post('/crypto-exchange/generate-payment-address', [CryptoExchangeController::class, 'generatePaymentAddress']);
    Route::get('/crypto-exchange/create-buy-order', [CryptoExchangeController::class, 'createBuyOrder']);
    Route::post('/crypto-exchange/submit-buy-order', [CryptoExchangeController::class, 'submitBuyOrder']);

    Route::get('/crypto-exchange/sell-page', [CryptoExchangeController::class, 'sellPage']);
    Route::get('/crypto-exchange/sell-crypto-page', [CryptoExchangeController::class, 'sellCryptoPage']);
    Route::post('/crypto-exchange/submit-sell-order', [CryptoExchangeController::class, 'submitSellOrder']);

    // gift cards
    Route::get('/gift-cards/countries', [GiftCardsController::class, 'getCountries']);
    Route::get('/gift-cards/products', [GiftCardsController::class, 'getProducts']);
    Route::get('/gift-cards/products/show', [GiftCardsController::class, 'showProduct']);
    Route::get('/gift-cards/products/check-price', [GiftCardsController::class, 'checkBuyPrice']);
    Route::post('/gift-cards/products/buy-gift-card', [GiftCardsController::class, 'buyGiftCard']);

    // international money transfer
    Route::get('/send-abroad-page', [InternationationalTransferController::class, 'sendAbroadPage']);
    Route::get('/transfer-abroad-page', [InternationationalTransferController::class, 'transferAbroadPage']);
    Route::post('/transfer-money-abroad', [InternationationalTransferController::class, 'transferMoneyAbroad']);

    Route::get('/send-to-africa', [InternationationalTransferController::class, 'sentToAfricaPage']);


    // utilities
    Route::get('/currency-conversion', [UtilityController::class, 'currencyConversion']);
    Route::get('/currency-rates', [UtilityController::class, 'getRates']);
    Route::get('/account-levels', [UtilityController::class, 'accountLevels']);

    Route::get('/referral-page', [ReferralController::class, 'referralPage']);
    Route::post('/store-notification-token', [PushNotificationController::class, 'handleStoreToken']);

    Route::prefix('merchant')->group(function () {
        Route::get('api-keys', [MerchantApiKeyController::class, 'index']);
        Route::post('api-keys', [MerchantApiKeyController::class, 'store']);
        Route::post('api-keys/{merchantApiKey}/revoke', [MerchantApiKeyController::class, 'revoke']);
    });
});

Route::prefix('gateway')->middleware(['merchant_api_key'])->group(function () {
    Route::post('payments/initiate', [PaymentsController::class, 'initiate']);
    Route::get('payments/verify/{reference}', [PaymentsController::class, 'show']);
    Route::post('payments/{reference}/refresh', [PaymentsController::class, 'refreshStatus']);
    Route::post('payments/{reference}/charge-card', [PaymentsController::class, 'chargeCard']);
    Route::post('payments/{reference}/validate-charge', [PaymentsController::class, 'validateCardCharge']);

    // Virtual Accounts
    Route::post('virtual-accounts', [VirtualAccountController::class, 'create']);
    Route::get('virtual-accounts', [VirtualAccountController::class, 'index']);
    Route::get('virtual-accounts/show', [VirtualAccountController::class, 'show']);
});
