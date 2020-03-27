<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Carbon\Carbon;

class Notificacion extends Notification
{
    use Queueable;
    private $details;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        //
        $this->details = $details;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $subject = sprintf('%s: tienes un nuevo mensaje de %s!', config('app.name'), $this->fromUser->name);
        $greeting = sprintf('Hola %s!', $notifiable->name);
        return (new MailMessage)
                    ->subject($subject)
                    ->greeting($greeting)
                    ->line('The introduction to the notification.')
                    ->action('Notificación', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * obtener un arreglo de la representación de la notificación
     * @param mixed $notifiable
     * @return array
     */

    public function toDatabase($notifiable)
    {
        return[
            'thread' => $this->thread,
            'user' => auth()->user()
        ];
    }

    /**
     * BroadCast
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'thread' => $this->thread,
            'user'=>auth()->user()
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
