<?php

namespace App\Livewire;

use App\Models\WelcomePageContent;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditWelcomePageContent extends Component
{
    use LivewireAlert;

    public $welcomeContent;

    public $columns;

    protected $rules = [
        'welcomeContent.welcome_heading' => 'nullable|string',
        'welcomeContent.welcome_sub_heading' => 'nullable|string',
        'welcomeContent.features_section_heading' => 'nullable|string',
        'welcomeContent.crypto_card_heading' => 'nullable|string',
        'welcomeContent.crypto_card_text' => 'nullable|string',
        'welcomeContent.holdings_card_heading' => 'nullable|string',
        'welcomeContent.holdings_card_text' => 'nullable|string',
        'welcomeContent.transfer_card_heading' => 'nullable|string',
        'welcomeContent.transfer_card_text' => 'nullable|string',
        'welcomeContent.savings_card_heading' => 'nullable|string',
        'welcomeContent.savings_card_save_with_interest' => 'nullable|string',
        'welcomeContent.savings_card_group_savings' => 'nullable|string',
        'welcomeContent.ajo_savings_card_heading' => 'nullable|string',
        'welcomeContent.ajo_savings_card_text' => 'nullable|string',
        'welcomeContent.bills_card_heading' => 'nullable|string',
        'welcomeContent.bills_card_text' => 'nullable|string',
        'welcomeContent.gift_cards_card_heading' => 'nullable|string',
        'welcomeContent.gift_cards_card_text' => 'nullable|string',
        'welcomeContent.about_us_section_heading' => 'nullable|string',
        'welcomeContent.about_us_section_sub_heading' => 'nullable|string',
        'welcomeContent.mission_statement' => 'nullable|string',
        'welcomeContent.vision_statement' => 'nullable|string',
        'welcomeContent.testimonial_section_heading' => 'nullable|string',
        'welcomeContent.contact_us_section_heading' => 'nullable|string',
        'welcomeContent.contact_us_section_sub_heading' => 'nullable|string',
        'welcomeContent.faq_section_sub_heading' => 'nullable|string',
        'welcomeContent.call_to_action_section_heading' => 'nullable|string',
        'welcomeContent.call_to_action_section_sub_heading' => 'nullable|string',
        'welcomeContent.footer_section_sub_heading' => 'nullable|string',
    ];

    public function mount()
    {
        $this->welcomeContent = WelcomePageContent::firstOrNew([]);

        $this->columns = Schema::getColumnListing('welcome_page_contents');

        // sort columns

        $this->columns = Arr::sort($this->columns);

        // dd($this->welcomeContent, $this->columns);
    }

    public function update()
    {

        $this->validate();

        $this->welcomeContent->save();

        $this->alert('success', 'Content Updated Successfully');
    }

    public function render()
    {
        return view('livewire.edit-welcome-page-content');
    }
}
