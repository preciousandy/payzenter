<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use LivewireAlert, WithFileUploads;

    public $user;
    public $photo;
    public $temporaryUrl;


    public function mount()
    {
        $this->user = auth()->user();
    }


    public function updatedPhoto()
    {
        if ($this->photo) {
            $this->temporaryUrl = '/storage/' . splitLivewireTmpPath($this->photo->getRealPath());
        }
    }

    public function handleUpload()
    {
        $this->validate([
            'photo' => 'image|max:5124', // 5MB Max
        ]);

        $url = $this->photo->store('profile-photos');

        $this->user->update([
            'profile_image' => $url
        ]);

        $this->flash('success', 'Profile Image updated successfully', [], route('profile'));
    }


    public function render()
    {
        return view('livewire.users.profile', [])->title("My Profile");
    }

    public function deleteAllRelatedData()
    {

        DB::transaction(function () {
            // Delete transactions
            $this->user->transactions()->delete();

            // Delete savings
            $this->user->savings()->delete();

            // Remove user from group savings
            $this->user->groupSavings()->detach();

            // Delete group savings donations
            $this->user->groupSavingsDonations()->delete();

            // Delete ajo savings
            $ajoSavings =   $this->user->ajoSavings;


            $ajoSavings->each(function ($ajoSaving) {
                $ajoSaving->transactions()->delete();
                $ajoSaving->delete();
            });



            // Remove user from ajo contributions
            $this->user->ajoContributions()->detach();

            // Delete beneficiaries
            $this->user->beneficiaries()->delete();

            // Delete holdings
            $this->user->holdings()->delete();

            // Delete investments
            $this->user->investments()->delete();

            // Remove referrals
            $this->user->referrals()->detach();

            // Delete KYC
            if ($this->user->kyc) {
                $this->user->kyc->delete();
            }

            // Delete live support messages
            $this->user->liveSupportMessages()->delete();

            // Delete international transfers
            $this->user->internationalTransfers()->delete();

            $this->user->swiftpayTransfers()->delete();

            $this->user->bankTransfers()->delete();

            $this->user->sellFxOrders()->delete();

            $this->user->buyFxOrders()->delete();
        });
    }

    public function deleteAccount()
    {

        // delete all user related data
        $this->deleteAllRelatedData();

        // Finally, delete the user
        $this->user->delete();

        // $this->user->update([
        //     'account_status' => User::ACCOUNT_STATUS_DELETED
        // ]);


        auth()->logout();

        $this->flash('success', 'Account deleted successfully', [], route('login'));
    }
}
