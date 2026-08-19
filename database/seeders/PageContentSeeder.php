<?php

namespace Database\Seeders;

use App\Models\WelcomePageContent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $welcomePageContent =  [
            'welcome_heading' => 'Revolutionize Your Finances, Explore Endless Possibilities with Our Multi-Functional Financial Services.',
            'welcome_sub_heading' => 'Take Control of your money any day, anytime with FitPay.',
            'features_section_heading' => 'Explore Our Services That
            Sets Us Apart',
            'crypto_card_heading' => 'Buy & Sell Currency',
            'crypto_card_text' => 'Trade currencies effortlessly with real-time rates. Say goodbye to hassle, hello to convenience. Experience seamless exchange like never before.',
            'holdings_card_heading' => 'Holdings',
            'holdings_card_text' => 'Trade currencies effortlessly with real-time rates. Say goodbye to hassle, hello to convenience. Experience seamless exchange like never before.',
            'transfer_card_heading' => 'Money Tansfer',
            'transfer_card_text' => 'Trade currencies effortlessly with real-time rates. Say goodbye to hassle, hello to convenience. Experience seamless exchange like never before.',
            'savings_card_heading' => 'Ajo Savings',
            'savings_card_save_with_interest' => 'Trade currencies effortlessly with real-time rates. Say goodbye to hassle, hello to convenience. Experience seamless exchange like never before.',
            'savings_card_group_savings' => 'Trade currencies effortlessly with real-time rates. Say goodbye to hassle, hello to convenience. Experience seamless exchange like never before.',
            'gift_cards_card_heading' => 'Gift Cards',
            'gift_cards_card_text' => 'Trade currencies effortlessly with real-time rates. Say goodbye to hassle, hello to convenience. Experience seamless exchange like never before.',
            'about_us_section_heading' => 'We are an award winning finance team',
            'about_us_section_sub_heading' => 'we are dedicated to giving you the best of all financial service with a lot of unnecessary stuffs you know.',
            'mission_statement' => 'At our core, we are driven by the relentless pursuit of simplifying financial transactions for individuals and businesses worldwide. Our mission is to empower every user with accessible and innovative fintech solutions that not only streamline processes but also foster economic growth and prosperity.',
            'vision_statement' => 'As we look ahead, our vision is to establish ourselves as the preeminent global fintech platform, redefining the way people engage with their finances. We envision a world where financial management is intuitive, inclusive, and empowering, driving towards universal financial well-being and resilience.',
            'testimonial_section_heading' => 'Our Users Are Pleased With Us',
            'contact_us_section_heading' => 'Always Available at your service',
            'contact_us_section_sub_heading' => 'Our friendly team would love to hear from you.',
            'faq_section_sub_heading' => "Have a question and don't see the answers below? Feel free to contact us support@FitPay.com",
            'call_to_action_section_heading' => 'Take control of Your Finances Today',
            'call_to_action_section_sub_heading' => 'Ready to experience the power of FitPay? Get started now and transform the way you manage your funds.',
            'footer_section_sub_heading' => 'FitPay offers versatile financial services, catering to individuals and businesses alike. With a focus on efficiency and innovation, our platform enables seamless transactions and wealth management.',
            'bills_card_heading' => 'Bills Payment',
            'bills_card_text' => 'Trade currencies effortlessly with real-time rates. Say goodbye to hassle, hello to convenience. Experience seamless exchange like never before.',
            'ajo_savings_card_heading' => 'Ajo Savings',
            'ajo_savings_card_text' => 'Trade currencies effortlessly with real-time rates. Say goodbye to hassle, hello to convenience. Experience seamless exchange like never before.',
        ];


        WelcomePageContent::create($welcomePageContent);
    }
}
