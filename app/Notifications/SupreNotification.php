<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\supre;

class SupreNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(supre $supre)
    {
        $this->supre =  $supre;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
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

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $url = '/supre/validacion/' . $this->supre->id;
        return [
            'titulo' => 'Una nueva solicitud de suficicencia '. $this->supre->no_memo .' presupuestal ha sido agregada de la unidad '. $this->supre->unidad_capacitacion,
            'supre_id' => $this->supre->id,
            'supre_memo' => $this->supre->no_memo,
            'supre_unidad' => $this->supre->unidad_capacitacion,
            'url' => $url,
        ];
    }
}
