<?php

namespace App\Livewire;

use App\Models\Page;
use Livewire\Component;

class TermsAndCondition extends Component
{
    private function defaultTermsHtml(): string
    {
        $name = e(config('app.name'));
        $websiteUrl = e('https://payzenter.com');
        $supportEmail = e('support@payzenter.com');
        $partnerEmail = e('partner@payzenter.com');
        $location = e('Garki, Abuja, Federal Capital Territory');
        $effectiveDate = e(now()->format('F j, Y'));

        return <<<HTML
<div class="prose prose-slate max-w-none">
    <p><strong>Effective date:</strong> {$effectiveDate}</p>

    <h3>1. Acceptance of these Terms</h3>
    <p>By accessing or using {$name} (the “Service”), you agree to these Terms and Conditions. If you do not agree, do not use the Service.</p>

    <h3>2. The Service</h3>
    <p>{$name} provides a payment gateway platform that enables merchants to:</p>
    <ul>
        <li>Accept payments via card, bank transfer, and USSD (where available).</li>
        <li>Generate virtual bank accounts for collections (where available).</li>
        <li>View transaction history and reports.</li>
        <li>Receive event notifications through webhooks and manage webhook delivery.</li>
        <li>Withdraw available balances to a verified settlement bank account.</li>
    </ul>

    <h3>3. Eligibility and Merchant Verification (KYB)</h3>
    <p>To use merchant features, you must complete our onboarding and business verification (KYB). We may request documents and additional information to comply with regulatory requirements and our partners’ requirements.</p>

    <h3>4. Account Security and API Keys</h3>
    <ul>
        <li>You are responsible for maintaining the confidentiality of your login credentials and API keys.</li>
        <li>You must not share your API keys publicly or embed secret keys in client-side applications.</li>
        <li>You are responsible for all activities performed through your account and API keys.</li>
    </ul>

    <h3>5. Payments, Settlement, and Fees</h3>
    <ul>
        <li>Transaction processing may involve third-party providers. Payment status may be <em>pending</em>, <em>successful</em>, or <em>failed</em> depending on provider confirmation.</li>
        <li>Fees (if applicable) may be deducted from transactions or charged separately as displayed in the Service.</li>
        <li>Withdrawals are permitted only to your verified settlement account. We may delay or hold settlements for compliance checks, risk reviews, disputes, or suspected fraud.</li>
    </ul>

    <h3>6. Refunds, Chargebacks, and Disputes</h3>
    <p>You are responsible for providing support to your customers and handling disputes related to your goods or services. Chargebacks and disputes may lead to reversals, negative balances, or account limitations.</p>

    <h3>7. Webhooks and Notifications</h3>
    <ul>
        <li>If you configure a webhook URL, we will attempt to send payment events to your endpoint.</li>
        <li>Webhook delivery is best-effort. Network failures and downtime can occur. You are responsible for securely receiving, validating, and processing webhook events.</li>
    </ul>

    <h3>8. Prohibited Activities</h3>
    <p>You must not use the Service for unlawful activities, fraud, money laundering, or prohibited business categories. We may suspend or terminate accounts that violate these Terms or applicable laws.</p>

    <h3>9. Availability</h3>
    <p>We aim to provide reliable access to the Service, but we do not guarantee uninterrupted operation. Maintenance, provider outages, and network issues may occur.</p>

    <h3>10. Limitation of Liability</h3>
    <p>To the maximum extent permitted by law, {$name} will not be liable for indirect, incidental, special, consequential, or punitive damages, or any loss of profits, revenues, data, or goodwill, arising out of or related to your use of the Service.</p>

    <h3>11. Privacy</h3>
    <p>Your use of the Service is also subject to our Privacy Policy. Please review it for information about how we collect and use personal data.</p>

    <h3>12. Termination</h3>
    <p>We may suspend or terminate your access to the Service if we reasonably believe you have violated these Terms, created risk, or failed verification requirements.</p>

    <h3>13. Changes to these Terms</h3>
    <p>We may update these Terms from time to time. Continued use of the Service after updates means you accept the revised Terms.</p>

    <h3>14. Contact</h3>
    <p>If you have questions about these Terms, contact us at <a href="mailto:{$supportEmail}">{$supportEmail}</a>.</p>
    <p><strong>For Partnership Contact:</strong> <a href="mailto:{$partnerEmail}">{$partnerEmail}</a></p>
    <p><strong>Location:</strong> {$location}</p>
    <p><strong>Website:</strong> <a href="{$websiteUrl}">payzenter.com</a></p>
</div>
HTML;
    }


    public function render()
    {
        $page = Page::where('code', Page::TERMS_AND_CONDITIONS)->first();
        $terms = $page?->content;

        $isLegacy = false;
        if (is_string($terms)) {
            $lower = strtolower($terms);
            $isLegacy = str_contains($lower, 'ajo')
                || str_contains($lower, 'crypto')
                || str_contains($lower, 'gift card')
                || str_contains($lower, 'international transfer')
                || str_contains($lower, 'virtual card')
                || str_contains($lower, 'payzenter')
                || str_contains($lower, 'fitpay');
        }

        if (!is_string($terms) || trim($terms) === '' || $isLegacy) {
            $terms = $this->defaultTermsHtml();
        } else {
            $appName = config('app.name');
            $terms = str_replace(['Payzenter', 'payzenter', 'payzenter'], [$appName, $appName, $appName], $terms);
            $terms = str_replace(
                [ 'partner@swiftpaymfb.com', 'support@axenbot.com', 'http://127.0.0.1:8000', 'http://localhost', 'https://localhost', 'support@paymentgateway.ng', 'partner@paymentgateway.ng', 'https://paymentgateway.ng'],
                ['support@payzenter.com', 'partner@payzenter.com', 'support@payzenter.com', 'https://payzenter.com', 'https://payzenter.com', 'https://payzenter.com', 'support@payzenter.com', 'partner@payzenter.com', 'https://payzenter.com'],
                $terms
            );
        }

        return view('livewire.terms-and-condition', [
            'terms' => $terms,
        ])->extends('layouts.guest')->title('Terms and Conditions');
    }
}
