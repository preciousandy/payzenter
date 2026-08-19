<?php

namespace App\Livewire;

use App\Models\Page;
use Livewire\Component;

class PrivacyPolicy extends Component
{
    public $policy;


    public function mount()
    {
        $policy = Page::where('code', Page::PRIVACY_POLICY)->first()?->content;

        if (!is_string($policy) || trim($policy) === '') {
            $policy = $this->defaultPrivacyHtml();
        } else {
            $policy = str_replace(
                ['support@paymentgateway.ng', 'https://paymentgateway.ng', 'PaymentGateway.ng'],
                ['support@payzenter.com', 'https://payzenter.com', 'payzenter.com'],
                $policy
            );
        }

        $this->policy = $policy;
    }

    private function defaultPrivacyHtml(): string
    {
        $name = e(config('app.name'));
        $websiteUrl = e('https://payzenter.com');
        $supportEmail = e('support@payzenter.com');
        $effectiveDate = e(now()->format('F j, Y'));

        return <<<HTML
<div class="prose prose-slate max-w-none">
    
    <p><strong>Effective date:</strong> {$effectiveDate}</p>

    <p>At {$name}, we are committed to protecting your privacy. This Privacy Policy explains how we collect, use, and share information when you use our website, mobile application, and payment gateway services (collectively, the “Service”).</p>

    <h3>1. Information We Collect</h3>
    <ul>
        <li><strong>Merchant Information:</strong> When you register as a merchant, we collect your business details, contact information, and verification documents (KYB).</li>
        <li><strong>Transaction Data:</strong> We collect details of transactions processed through our gateway, including payment amounts, currencies, and timestamps.</li>
        <li><strong>Customer Information:</strong> To facilitate payments, we may collect customer email addresses, phone numbers, and names. Payment instrument details (like card numbers) are processed securely by our partners and are not stored in full on our servers.</li>
        <li><strong>Usage and Device Information:</strong> We may collect information about how you access the Service, including IP addresses, browser types, and device identifiers.</li>
    </ul>

    <h3>2. How We Use Information</h3>
    <ul>
        <li>To provide and maintain the Service.</li>
        <li>To process payments and facilitate settlements.</li>
        <li>To verify merchant identities and prevent fraud.</li>
        <li>To communicate with you about your account and transactions.</li>
        <li>To comply with legal and regulatory requirements.</li>
    </ul>

    <h3>3. How We Share Information</h3>
    <ul>
        <li><strong>Service Providers:</strong> We share information with payment processors, banks, and verification partners to facilitate transactions.</li>
        <li><strong>Compliance and Legal:</strong> We may disclose information if required by law or to protect the rights and safety of {$name}, our users, or others.</li>
        <li><strong>Business Transfers:</strong> If we are involved in a merger, acquisition, or sale of assets, your information may be transferred as part of that transaction.</li>
    </ul>

    <h3>4. Data Security</h3>
    <p>We use administrative, technical, and physical security measures to help protect your personal information. However, no electronic transmission or storage is 100% secure.</p>

    <h3>5. Your Choices</h3>
    <p>You can update your account information through your dashboard. You may also contact us to request access to or deletion of your data, subject to legal and regulatory retention requirements.</p>

    <h3>6. Changes to this Policy</h3>
    <p>We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new policy on this page.</p>

    <h3>7. Contact Us</h3>
    <p>If you have questions about this Privacy Policy, please contact us at <a href="mailto:{$supportEmail}">{$supportEmail}</a>.</p>
    <p><strong>Website:</strong> <a href="{$websiteUrl}">payzenter.com</a></p>
</div>
HTML;
    }

    public function render()
    {
        return view('livewire.privacy-policy')->extends('layouts.guest')->title('Privacy Policy');
    }
}
