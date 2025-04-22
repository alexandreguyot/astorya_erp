<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BillGenerationCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $companyName;
    private $billNumber;

    public function __construct($companyName, $billNumber)
    {
        $this->companyName = $companyName;
        $this->billNumber = $billNumber;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "La facture {$this->billNumber} pour {$this->companyName} a été générée avec succès.",
        ];
    }
}
