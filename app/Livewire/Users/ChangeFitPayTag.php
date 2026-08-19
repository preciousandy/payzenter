<?php

namespace App\Livewire\Users;

use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ChangeFitPayTag extends Component
{
    use LivewireAlert;

    public $tag;
    public $showSuccessModal = false;


    public function mount()
    {
        $this->tag = auth()->user()->username;
    }

    public function update()
    {
        $this->validate([
            'tag' => ['string', 'required', Rule::unique('users', 'username')->ignore(auth()->id())]
        ]);

        auth()->user()->update([
            'username' => $this->tag
        ]);

        $this->showSuccessModal = true;
    }

    public function goHome()
    {
        return $this->flash('success', config('app.name') . ' tag created successfully', [], route('home'));
    }

    public function render()
    {
        return view('livewire.users.change-fit-pay-tag')->title('Change ' . config('app.name') . ' Tag');
    }
}
