<?php

namespace App\Http\Livewire;

use Livewire\Component;

class NotificationsDropdown extends Component
{

    protected $listeners = [
        // écoute ton event et raffraîchit automatiquement le rendu
        'echo:App.Events.NotificationsUpdated,NotificationsUpdated' => '$refresh',
    ];

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.notifications-dropdown', [
            'notifications' => auth()->user()->unreadNotifications,
        ]);
    }
}
