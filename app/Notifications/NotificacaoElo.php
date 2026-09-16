<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Base das notificações da Elo (RF-07.1).
 *
 * Cada subclasse define título, mensagem e a URL de destino; esta classe
 * cuida dos canais e do formato, para que todas as notificações fiquem
 * consistentes no banco e no e-mail.
 */
abstract class NotificacaoElo extends Notification
{
    use Queueable;

    abstract public function titulo(object $notifiable): string;

    abstract public function mensagem(object $notifiable): string;

    abstract public function url(object $notifiable): string;

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => $this->titulo($notifiable),
            'mensagem' => $this->mensagem($notifiable),
            'url' => $this->url($notifiable),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Elo — '.$this->titulo($notifiable))
            ->greeting("Olá, {$notifiable->nome}!")
            ->line($this->mensagem($notifiable))
            ->action('Ver na plataforma', url($this->url($notifiable)));
    }
}
