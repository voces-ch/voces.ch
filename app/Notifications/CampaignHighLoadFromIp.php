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
    /**
     * Create a new notification instance.
     */
    public function __construct($ipAddress, Campaign $campaign)
    {
        // Remove last octet of IP address for privacy reasons
        $ipAddressParts = explode('.', $ipAddress);
        if (count($ipAddressParts) === 4) {
            $ipAddressParts[3] = '0';
            $ipAddress = implode('.', $ipAddressParts);
        }
        $this->ipAddress = $ipAddress;
        $this->campaign = $campaign;
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
