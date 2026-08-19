<?php

namespace Database\Seeders;

use App\Helpers\PermissionHelper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            PermissionHelper::VIEW_DASHBOARD,
            PermissionHelper::VIEW_USERS,
            PermissionHelper::MODIFY_USERS,
            PermissionHelper::VIEW_USER_TRANSACTIONS,
            PermissionHelper::VIEW_USER_PAYMENTS,
            PermissionHelper::VIEW_NETWORK_PROVIDERS,
            PermissionHelper::VIEW_DATA_PLANS,
            PermissionHelper::MODIFY_DATA_PLANS,
            PermissionHelper::VIEW_CABLE_PACKAGES,
            PermissionHelper::MODIFY_CABLE_PACKAGES,
            PermissionHelper::VIEW_USERS_FEEDBACK,
            PermissionHelper::VIEW_EXCHANGE_RATES,
            PermissionHelper::VIEW_INTERNATIONAL_TRANSFERS,
            PermissionHelper::MODIFY_INTERNATIONAL_TRANSFERS,
            PermissionHelper::VIEW_CRYPTO_CURRENCIES,
            PermissionHelper::MANAGE_CRYPTO_BUY_ORDERS,
            PermissionHelper::MANAGE_CRYPTO_SELL_ORDERS,
            PermissionHelper::VIEW_FX_ORDERS,
            PermissionHelper::MANAGE_FX_BUY_ORDERS,
            PermissionHelper::MANAGE_FX_SELL_ORDERS,
            PermissionHelper::VIEW_SETTINGS,
            PermissionHelper::MANAGE_SETTINGS,
            PermissionHelper::VIEW_ROLES_AND_PERMISSIONS,
            PermissionHelper::MODIFY_ROLES_AND_PERMISSIONS,
            PermissionHelper::MANAGE_WEBSITE_CONTENT,
            PermissionHelper::MANAGE_KYC,
            PermissionHelper::MANAGE_LIVE_SUPPORT,
            PermissionHelper::MODIFY_LIVE_SPPORT_TOPIC,
            PermissionHelper::MANAGE_ERROR_LOGS,
        ];


        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission]);
        }
    }
}
