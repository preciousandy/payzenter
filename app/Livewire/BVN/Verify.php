<?php

namespace App\Livewire\BVN;

use Livewire\Component;

class Verify extends Component
{
    public $first_name, $last_name, $bvn;

    public function mount()
    {
        $this->fill([
            'first_name' => auth()->user()->first_name,
            'last_name' => auth()->user()->last_name,
        ]);
    }

    public function submit()
    {
        $this->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'bvn' => 'required|digits:11',
        ]);

        $data = auth()->user()->submitBVN($this->first_name, $this->last_name, $this->bvn);

        auth()->user()->update([
            'bvn_reference' => $data['reference']
        ]);


        if (!$data['url']) {
            return  redirect()->route('bvn.verify')->with('error', 'Failed to verify BVN');
        }


        return redirect()->away($data['url']);
    }


    public function render()
    {
        return view('livewire.b-v-n.verify')->title('Verify BVN');
    }
}
