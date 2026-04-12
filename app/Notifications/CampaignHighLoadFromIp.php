<?php

namespace App\Notifications;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class CampaignHighLoadFromIp extends Notification implements ShouldQueue
{
    use Queueable;

    public $ipAddress;
    public Campaign $campaign;
    public $chat_id;
    /**
     * Create a new notification instance.
     */
    public function __construct($ipAddress, Campaign $campaign)
    {
        $this->ipAddress = $ipAddress;
        $this->campaign = $campaign;
        $this->chat_id = config('services.telegram.chat_id');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['telegram'];
    }

    /**
     * The Telegram representation of the notification.
     */
    public function toTelegram(object $notifiable)
    {
        $message = "Experiencing a high load of signatures from IP address: {$this->ipAddress} for campaign: {$this->campaign->title} (ID: {$this->campaign->id}). Please investigate.";
        return TelegramMessage::create()
            ->content($message);
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
