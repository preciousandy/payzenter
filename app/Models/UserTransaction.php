<?php

namespace App\Models;

use App\Jobs\SendExpoPushNotification;
use App\Notifications\NotifyUser;
use App\Traits\HashIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTransaction extends Model
{
    use HasFactory;
    use HashIdTrait;

    protected $guarded = [];


    const TYPE_DEBIT = 'debit';
    const TYPE_CREDIT = 'credit';

    const SOURCE_WALLET_FUNDED = 'wallet_funded';
    const SOURCE_AIRTIME = 'airtime';
    const SOURCE_DATA = 'data';
    const SOURCE_CABLE = 'cable';
    const SOURCE_ELECTRICITY = 'electricity';
    const SOURCE_FOREIGN_TO_LOCAL_TRANSFER = 'foreign_to_local_transfer';
    const SOURCE_LOCAL_TO_FOREIGN_TRANSFER = 'local_to_foreign_transfer';
    const SOURCE_HOLDINGS = 'holdings';
    const SOURCE_INVESTMENTS = 'investments';
    const SOURCE_BUREAU_DA_CHANGE = 'bureau_de_change';
    const SOURCE_BUY_CRYPTO = 'buy-crypto';
    const SOURCE_SELL_CRYPTO = 'sell_crypto';
    const SOURCE_GIFT_CARD = 'gift_card';
    const SOURCE_BANK_TRANSFER = 'bank_transfer';
    const SOURCE_SWIFTPAY_TRANSFER = 'swiftpay_transfer';
    const SOURCE_SAVE_WITH_INTEREST = 'save_with_interest';
    const SOURCE_GROUP_SAVINGS = 'group_savings';
    const SOURCE_AJO_SAVINGS = 'ajo_savings';
    const SOURCE_AJO_CONTRIBUTION = 'ajo_contribution';
    const SOURCE_INTERNATIONAL_TRANSFER = 'international_transfer';
    const SOURCE_MULTIPLE_BANK_TRANSFER = 'multiple_bank_transfer';
    const SOURCE_MULTIPLE_SWIFTPAY_TRANSFER = 'multiple_swiftpay_transfer';
    const SOURCE_BETTING = 'betting';



    // statuses
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESSFUL = 'successful';
    const STATUS_FAILED = 'failed';


    // increment transaction volume for user

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->user->increment('transaction_volume', $model->amount);

            if (!$model->reference) {
                if ($model->type === self::TYPE_DEBIT) {
                    $model->reference = generateOutgoingReference();
                } else {
                    $model->reference = generateTransactionReference($model->source ?? '');
                }
            }

            $model->user->notify(new NotifyUser($model->message, $model->source));

            $userTokens = $model->user->notificationTokens->pluck('token')->toArray();

            if (!empty($userTokens)) {
                SendExpoPushNotification::dispatch(
                    $userTokens,
                    ucfirst(str_replace('_', ' ', $model->source ?? 'Notification')),
                    $model->message,
                    []
                );
            }
        });
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
