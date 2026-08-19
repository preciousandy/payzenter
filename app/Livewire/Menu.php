<?php

namespace App\Livewire;

use App\Models\SocialLink;
use Livewire\Component;

use App\Utils\Roles;

class Menu extends Component
{
    public $whatsappLink;

    public function mount()
    {
        $whatsapp = SocialLink::where('name', 'Whatsapp')->first();
        $this->whatsappLink = $whatsapp?->link;
    }

    public function render()
    {
        return view('livewire.menu');
    }

    public function logout()
    {
        $user = auth()->user();
        $referer = request()->headers->get('referer');
        $refererPath = $referer ? parse_url($referer, PHP_URL_PATH) : '';
        $isAdminPath = str_contains($refererPath, '/admin');

        $isAdmin = ($user && ($user->hasRole('admin') || $user->hasRole(Roles::ADMIN))) || $isAdminPath;

        auth()->logout();

        if ($isAdmin) {
            return redirect()->route('admin.login');
        }

        return redirect()->route('login');
    }
}
