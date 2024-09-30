<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostStatusChangedNotification extends Notification
{
    use Queueable;

    protected $post;
    protected $status;
    protected $type;

    public function __construct($post, $status, $type = 'owner')
    {
        $this->post = $post;
        $this->status = $status;
        $this->type = $type;
    }
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //                 ->line('The introduction to the notification.')
    //                 ->action('Notification Action', url('/'))
    //                 ->line('Thank you for using our application!');
    // }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = '';

        if ($this->type == 'owner') {
            $message = $this->status == 0 ? 'Your post [' . $this->post->title . '] has been deactivated.' : ($this->status == 1 ? 'Your post [' . $this->post->title . '] has been made private.' : ($this->status == 2 ? 'Your post [' . $this->post->title . '] has been activated.' :
                        'Your post [' . $this->post->title . '] has been made public.'));
        } 

        return [
            'message' => $message,
            'post_id' => $this->post->id,
        ];
    }
}
