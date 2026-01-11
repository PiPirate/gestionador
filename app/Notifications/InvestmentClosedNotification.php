<?php

namespace App\Notifications;

use App\Models\Investment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InvestmentClosedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Investment $investment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $investorName = $this->investment->investor?->name ?? 'Inversor';

        return [
            'title' => 'Inversión cerrada automáticamente',
            'body' => "La inversión {$this->investment->code} de {$investorName} fue cerrada por fecha.",
            'investment_id' => $this->investment->id,
            'status' => $this->investment->status,
        ];
    }
}
