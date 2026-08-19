<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                "question" => "What can I do with this FitPay?",
                "answer" => "This fintech app allows you to manage your finances conveniently in one place. You can view your account balances, transfer money between accounts, pay bills, and even invest your money."
            ],
            [
                "question" => "Is my money safe with this FitPay?",
                "answer" => "Yes, your money is safe with this app. We use state-of-the-art security measures to protect your information and transactions."
            ],
            [
                "question" => "What fees are associated with using the FitPay?",
                "answer" => "We offer a variety of plans with different fee structures. You can find more information about our fees on our website."
            ],
            [
                "question" => "How do I get started with the FitPay?",
                "answer" => "Getting started is easy! Simply download the app from the App Store or Google Play and follow the on-screen instructions to create an account."
            ],
            [
                "question" => "I'm having trouble with the FitPay. What should I do?",
                "answer" => "If you're having trouble with the app, you can contact our customer support team for assistance. We're available 24/7 to help you."
            ]
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
