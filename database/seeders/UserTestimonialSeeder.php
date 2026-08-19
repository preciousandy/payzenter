<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $FitPayTestimonials = [
            [
                "text" => "FitPay has completely changed the way I manage my finances. It's so easy to use and keeps me organized. I highly recommend it to anyone looking for a convenient and secure financial app.",
                "user" => "Sarah Jones",
                "profession" => "Entrepreneur",
            ],
            [
                "text" => "As a freelancer, keeping track of my income and expenses can be a nightmare. But FitPay has made it so much easier! I love the budgeting tools and the ability to send invoices directly through the app.",
                "user" => "David Lee",
                "profession" => "Web Developer",
            ],
            [
                "text" => "I was hesitant to switch to a new finance app, but I'm so glad I did! FitPay is user-friendly and has all the features I need. Plus, the customer service is top-notch.",
                "user" => "Emily Garcia",
                "profession" => "Marketing Manager",
            ],
            [
                "text" => "FitPay has saved me so much time! I can now transfer money between accounts, pay bills, and invest all in one place. It's a lifesaver!",
                "user" => "Michael Brown",
                "profession" => "Software Engineer",
            ],
            [
                "text" => "I'm finally able to meet my financial goals thanks to FitPay. The budgeting tools and spending insights have been incredibly helpful.",
                "user" => "Amanda Hernandez",
                "profession" => "Teacher",
            ],
            [
                "text" => "FitPay is the perfect app for busy professionals like me. It's fast, secure, and lets me manage my finances on the go. I can't recommend it enough.",
                "user" => "William Miller",
                "profession" => "Lawyer",
            ],
            [
                "text" => "I was initially skeptical about using a fintech app, but FitPay has won me over. The security features are impressive, and I feel confident using the app for all my financial transactions.",
                "user" => "Jennifer Davis",
                "profession" => "Accountant",
            ],
            [
                "text" => "FitPay has made a huge difference in my financial literacy. The educational resources within the app have been invaluable in helping me understand my finances better.",
                "user"  => "Robert Thompson",
                "profession" => "Student",
            ],
            [
                "text" => "I love the sleek and user-friendly design of FitPay. It's so easy to navigate and find what I'm looking for. Plus, the customer support team is always responsive and helpful.",
                "user" => "Elizabeth Choi",
                "profession" => "Graphic Designer",
            ],
            [
                "text" => "FitPay is the best financial decision I've made in a long time. It's helped me take control of my finances and achieve my financial goals.",
                "user" => "Daniel Walker",
                "profession" => "Business Owner",
            ],
        ];


        foreach ($FitPayTestimonials as $testimonial) {
            \App\Models\UserTestimonial::create([
                "message" => $testimonial["text"],
                "name" => $testimonial["user"],
                "profession" => $testimonial["profession"],
            ]);
        }
    }
}
