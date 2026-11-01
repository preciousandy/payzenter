<div class="p-4 max-w-6xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="text-2xl font-bold text-gray-900">Developer API</div>
        <div class="text-sm text-gray-500 mt-1">Integrate your app to collect payments via {{ config('app.name') }}</div>

        <div class="mt-6 space-y-6 text-sm text-gray-700">
            <div class="border border-gray-200 rounded-xl p-4">
                <div class="font-semibold text-gray-900">Base URL</div>
                <div class="mt-2 bg-gray-50 border border-gray-200 rounded-xl p-3 font-mono text-xs overflow-x-auto">
                    {{ rtrim(config('app.url'), '/') }}
                </div>
            </div>

            <div class="border border-gray-200 rounded-xl p-4">
                <div class="font-semibold text-gray-900">Authentication</div>
                <div class="mt-2">Use your Merchant API Key on every request:</div>
                <div class="mt-2 bg-gray-50 border border-gray-200 rounded-xl p-3 font-mono text-xs overflow-x-auto">
                    Authorization: Bearer YOUR_MERCHANT_API_KEY
                </div>
                <div class="mt-2">You can also use:</div>
                <div class="mt-2 bg-gray-50 border border-gray-200 rounded-xl p-3 font-mono text-xs overflow-x-auto">
                    X-API-KEY: YOUR_MERCHANT_API_KEY
                </div>
                <div class="mt-3 text-xs text-gray-500">
                    Create your Merchant API Key from your dashboard: Gateway → API Keys.
                </div>
            </div>

            <div class="border border-gray-200 rounded-xl p-4">
                <div class="font-semibold text-gray-900 text-lg mb-2">Integrated Checkout Experience</div>
                <div class="mt-2 leading-relaxed">
                    Our checkout process is designed to maximize conversion with a seamless multi-step interface. When your customer visits the <span class="font-semibold">checkout_url</span>, they will experience:
                    <ul class="list-disc ml-5 mt-2 space-y-1">
                        <li><span class="font-semibold">Dynamic Selection</span>: Choose between Card, Bank Transfer, or PalmPay.</li>
                        <li><span class="font-semibold">30-Minute Window</span>: Bank transfers are held for 30 minutes with a live countdown timer for immediate confirmation.</li>
                        <li><span class="font-semibold">Real-time Updates</span>: The page automatically confirms payment the moment it's received—no refresh required.</li>
                    </ul>
                </div>
            </div>

            <div class="border border-gray-200 rounded-xl p-4">
                <div class="font-semibold text-gray-900">Create Payment</div>
                <div class="mt-2 font-mono text-xs bg-gray-50 border border-gray-200 rounded-xl p-3 overflow-x-auto">
                    POST https://payzenter.com/api/gateway/payments/initiate
                </div>
                <div class="mt-3">Body fields:</div>
                <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div><span class="font-semibold">amount</span> (number, min 100)</div>
                    <div><span class="font-semibold">currency</span> (string, default NGN)</div>
                    <div><span class="font-semibold">method</span> (card | transfer | ussd)</div>
                    <div><span class="font-semibold">customer_email</span> (email)</div>
                    <div><span class="font-semibold">customer_phone</span> (string, optional)</div>
                    <div><span class="font-semibold">customer_name</span> (string, optional)</div>
                    <div><span class="font-semibold">return_url</span> (url)</div>
                    <div><span class="font-semibold">merchant_webhook_url</span> (url, optional override)</div>
                    <div><span class="font-semibold">reference</span> (string, optional)</div>
                </div>
                <div class="mt-3 text-xs text-gray-500">
                    Amount sent in the request is in major units (e.g. NGN). Amount stored/returned may be in minor units depending on provider (kobo).
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    The payment method selects the checkout flow. Provider routing is handled internally and the hosted checkout may still present PalmPay as one of the available payment options where supported.
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    If <span class="font-semibold">merchant_webhook_url</span> is omitted, the saved default webhook URL from your dashboard is used.
                </div>

                <div class="mt-4 font-semibold text-gray-900">Example (cURL)</div>
                <pre class="mt-2 bg-gray-50 border border-gray-200 rounded-xl p-3 text-xs overflow-x-auto"><code>curl -X POST "{{ rtrim(config('app.url'), '/') }}/api/gateway/payments/initiate" \
  -H "Authorization: Bearer YOUR_MERCHANT_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 2000,
    "currency": "NGN",
    "method": "card",
    "customer_email": "customer@example.com",
    "customer_phone": "08000000000",
    "customer_name": "John Doe",
    "return_url": "https://yourapp.com/paid",
    "merchant_webhook_url": "https://yourapp.com/webhook/payment-gateway-ng"
  }'</code></pre>

                <div class="mt-3">Response contains <span class="font-semibold">provider.checkout_url</span>. Redirect your customer to that URL to complete payment.</div>

                <div class="mt-4 font-semibold text-gray-900">Response (example)</div>
                <pre class="mt-2 bg-gray-50 border border-gray-200 rounded-xl p-3 text-xs overflow-x-auto"><code>{
  "status": "success",
  "message": "Payment initialized",
  "data": {
    "reference": "gtw_xxxxxxxxxxxxxxxxxxxxxxxx",
    "amount": 200000,
    "currency": "NGN",
    "method": "card",
    "status": "unpaid",
    "provider": {
      "name": "{{ $enablePalmPay ? 'palmpay' : ($enableFlutterwave ? 'flutterwave' : 'standard') }}",
      "reference": "gtw_xxxxxxxxxxxxxxxxxxxxxxxx",
      "transaction_id": null,
      "checkout_url": "{{ rtrim(config('app.url'), '/') }}/gateway/pay/gtw_xxxxxxxxxxxxxxxxxxxxxxxx"
    }
  }
}</code></pre>
            </div>

            <div class="border border-gray-200 rounded-xl p-4">
                <div class="font-semibold text-gray-900">Verify Transaction</div>
                <div class="mt-2 font-mono text-xs bg-gray-50 border border-gray-200 rounded-xl p-3 overflow-x-auto">
                    GET {{ rtrim(config('app.url'), '/') }}/api/gateway/payments/verify/{reference}
                </div>
            </div>

            <div class="border border-gray-200 rounded-xl p-4">
                <div class="font-semibold text-gray-900">Refresh Payment Status</div>
                <div class="mt-2 font-mono text-xs bg-gray-50 border border-gray-200 rounded-xl p-3 overflow-x-auto">
                    POST {{ rtrim(config('app.url'), '/') }}/api/gateway/payments/{reference}/refresh
                </div>
                @if($enableFlutterwave)
                <div class="mt-3 text-xs text-gray-500">
                    For Flutterwave, refresh requires <span class="font-semibold">provider_transaction_id</span> which is set after the customer completes payment and is redirected back via the callback flow.
                </div>
                @endif
            </div>

            <div class="border border-gray-200 rounded-xl p-4">
                <div class="font-semibold text-gray-900">Create Virtual Account</div>
                <div class="mt-2">Generate a dedicated virtual account for your customers to pay into. Each customer is identified by their phone number.</div>
                <div class="mt-2 text-xs text-amber-700 bg-amber-50 p-2 rounded-lg border border-amber-100">
                    <span class="font-bold italic">Note</span>: The account name will be automatically prefixed with the company abbreviation (e.g., "{{ getCompanyAbbreviation() }} {{ config('app.name') }}") to ensure brand recognition at the bank.
                </div>
                @php
                    $docProviders = [];
                    if ($enablePalmPay) $docProviders[] = 'palmpay';
                    if ($enableUdara360) $docProviders[] = 'udara360';
                    if ($enableFlutterwave) $docProviders[] = 'flutterwave';
                @endphp
                <div class="mt-2 text-xs text-blue-700 bg-blue-50 p-2 rounded-lg border border-blue-100">
                    <span class="font-bold italic">Provider Selection</span>: Omitting the provider uses your system default provider. Available active providers: <span class="font-semibold">{{ implode(', ', $docProviders) }}</span>.
                </div>
                <div class="mt-4 font-mono text-xs bg-gray-50 border border-gray-200 rounded-xl p-3 overflow-x-auto">
                    POST {{ rtrim(config('app.url'), '/') }}/api/gateway/virtual-accounts
                </div>
                <div class="mt-3">Body fields:</div>
                <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                    <div><span class="font-semibold">name</span> (string, required - customer name)</div>
                    <div><span class="font-semibold">phone</span> (string, required - customer phone)</div>
                    <div><span class="font-semibold">email</span> (string, optional - customer email)</div>
                    <div><span class="font-semibold">provider</span> (string, optional - {!! implode(' | ', array_map(fn($p) => "<code class=\"bg-gray-100 px-1 py-0.5 rounded font-mono\">$p</code>", $docProviders)) !!})</div>
                </div>

                <div class="mt-4 font-semibold text-gray-900">Example (cURL - PalmPay)</div>
                <pre class="mt-2 bg-gray-50 border border-gray-200 rounded-xl p-3 text-xs overflow-x-auto"><code>curl -X POST "{{ rtrim(config('app.url'), '/') }}/api/gateway/virtual-accounts" \
  -H "Authorization: Bearer YOUR_MERCHANT_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "phone": "08012345678",
    "email": "john@example.com",
    "provider": "palmpay"
  }'</code></pre>

                <div class="mt-3 font-semibold text-gray-900">Sample Response (200 OK)</div>
                <pre class="mt-1 bg-gray-900 text-gray-100 rounded-xl p-3 text-xs overflow-x-auto"><code>{
  "status": "success",
  "message": "Virtual account created successfully",
  "data": {
    "account_number": "6649335104",
    "bank_name": "PalmPay",
    "bank_code": "000000",
    "account_name": "{{ getCompanyAbbreviation() }} John Doe",
    "provider": "palmpay",
    "phone": "08012345678"
  }
}</code></pre>
            </div>

            <div class="border border-gray-200 rounded-xl p-4">
                <div class="font-semibold text-gray-900">Get Virtual Account Details</div>
                <div class="mt-2 font-mono text-xs bg-gray-50 border border-gray-200 rounded-xl p-3 overflow-x-auto">
                    GET {{ rtrim(config('app.url'), '/') }}/api/gateway/virtual-accounts/show?phone=08012345678&provider=palmpay
                </div>
                <div class="mt-3 text-xs text-gray-600">
                    Query parameters:
                    <ul class="list-disc list-inside mt-1 space-y-0.5">
                        <li><span class="font-semibold">phone</span> (string, optional) - Customer's phone number.</li>
                        <li><span class="font-semibold">provider</span> (string, optional) - Filter by provider ({!! implode(' or ', array_map(fn($p) => "<code class=\"bg-gray-100 px-1 py-0.5 rounded\">$p</code>", $docProviders)) !!}).</li>
                    </ul>
                </div>
            </div>

            <div class="border border-gray-200 rounded-xl p-4">
                <div class="font-semibold text-gray-900">List All Virtual Accounts</div>
                <div class="mt-2 font-mono text-xs bg-gray-50 border border-gray-200 rounded-xl p-3 overflow-x-auto">
                    GET {{ rtrim(config('app.url'), '/') }}/api/gateway/virtual-accounts?provider=palmpay
                </div>
                <div class="mt-3 text-xs text-gray-600">
                    Returns a paginated list of all virtual accounts created under your merchant account. Filter optionally using <span class="font-semibold">provider={{ implode('</span> or <span class="font-semibold">provider=', $docProviders) }}</span>.
                </div>
            </div>

            <div class="border border-gray-200 rounded-xl p-4">
                <div class="font-semibold text-gray-900">Webhooks</div>
                <div class="mt-2">
                    Save your default webhook URL from the Webhook Events page. You can still override it per payment request with <span class="font-semibold">merchant_webhook_url</span>.
                </div>
                <div class="mt-2">
                    Webhook delivery is best-effort. You can view and repush webhook deliveries from the Webhook Events page.
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    Event types currently sent: <span class="font-semibold">payment.success</span> and <span class="font-semibold">virtual_account.funding.success</span>.
                </div>

                <div class="mt-4 font-semibold text-gray-900">Payment Webhook Payload</div>
                <pre class="mt-2 bg-gray-50 border border-gray-200 rounded-xl p-3 text-xs overflow-x-auto"><code>{
  "event": "payment.success",
  "type": "payment",
  "reference": "gtw_xxxxxxxxxxxxxxxxxxxxxxxx",
  "status": "success",
  "paid_at": "2026-05-01T12:34:56.000000Z",
  "amount": 200000,
  "currency": "NGN",
  "method": "transfer",
  "provider": "palmpay",
  "provider_reference": "gtw_xxxxxxxxxxxxxxxxxxxxxxxx",
  "provider_transaction_id": "1234567890"
}</code></pre>

                <div class="mt-4 font-semibold text-gray-900">Virtual Account Funding Payload</div>
                <pre class="mt-2 bg-gray-50 border border-gray-200 rounded-xl p-3 text-xs overflow-x-auto"><code>{
  "event": "virtual_account.funding.success",
  "type": "virtual_account_funding",
  "reference": "fund_xxxxxxxxxxxxx",
  "status": "success",
  "amount": 500000,
  "currency": "NGN",
  "funded_at": "2026-06-26T12:34:56.000000Z",
  "provider": "palmpay",
  "provider_reference": "fund_xxxxxxxxxxxxx",
  "virtual_account": {
    "account_number": "1234567890",
    "account_name": "{{ getCompanyAbbreviation() }} John Doe",
    "bank_name": "PalmPay",
    "bank_code": "000000",
    "customer_phone": "08012345678",
    "customer_email": "john@example.com",
    "merchant_ref": "08012345678",
    "account_reference": "08012345678"
  },
  "source": {
    "account_name": "John Doe",
    "account_number": null,
    "bank_name": null
  }
}</code></pre>

                <div class="mt-3 text-xs text-gray-500">
                    Webhooks are not currently signed. Recommended: treat webhooks as notifications and confirm final status by calling
                    <span class="font-semibold">GET /api/gateway/payments/{reference}</span>.
                </div>
            </div>

            <div class="border border-gray-200 rounded-xl p-4">
                <div class="font-semibold text-gray-900">Common Errors</div>
                <div class="mt-3 space-y-2 text-xs text-gray-600">
                    <div><span class="font-semibold">401</span> Missing API key / Invalid API key</div>
                    <div><span class="font-semibold">422</span> Validation error (missing fields, invalid method, min amount)</div>
                    <div><span class="font-semibold">400</span> Provider error (Flutterwave/PalmPay not configured or request failed)</div>
                    <div><span class="font-semibold">PalmPay sign error</span> Usually means the PalmPay app ID, merchant private key, uploaded public key, or target environment do not match.</div>
                </div>
            </div>
        </div>
    </div>

    <x-slot:headerTitle>
        Developer API
    </x-slot:headerTitle>
</div>
