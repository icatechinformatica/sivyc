<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\supre;

class ValSupreDelegadoNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(supre $valsupre)
    {
        $this->valsupre =  $valsupre;
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
        $url = '/supre/validacion/pdf/' . $this->valsupre->id;
        return [
            'titulo' => 'La suficicencia presupuestal '. $this->valsupre->no_memo .' ha sido validada',
            'supre_id' => $this->valsupre->id,
            'supre_memo' => $this->valsupre->no_memo,
            'supre_unidad' => $this->valsupre->unidad_capacitacion,
            'url' => $url,
        ];
    }
}
