<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Benwilkins\FCM\FcmMessage;


class itemNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['fcm'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    public function toFcm($notifiable)
    {
        $status = fcm()->to([$notifiable->device_token])
            ->data([
                'title' => trans('general.name'),
                'body' => $this->getData()['message'],
                'type' => $this->getData()['type'],
                'object' => $this->getData()['object'],
                'object_id' => $this->getData()['id']
            ])
            ->notification([
                'title' => trans('general.name'),
                'body' => $this->getData()['message'],
                'vibrate' => "1",
                'sound' => "default",
                "priority" => "high"
            ])
            ->send();
        return $status;
    }

    public function routeNotificationForFcm($notification)
    {
            return $this->device_token;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $data = $this->getData();
        if(!$notifiable->isMuteNotification()) {
            $this->toFcm($notifiable);
        }

        return $data;
    }

    abstract public function getData();

}
