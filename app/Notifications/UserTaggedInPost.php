<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Post;

class UserTaggedInPost extends Notification
{
    use Queueable;

    protected $post;

    /**
     * Create a new notification instance.
     */
     // ✅ Accept the Post in constructor and assign it
    public function __construct(Post $post)
    {
        $this->post = $post;
        //$this->tagger = $tagger;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    

    public function via($notifiable)
    {
        return ['database']; // Or add 'mail' if you want to email them too
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'You were tagged in a post!',
            'post_id' => $this->post->id,
            'tagger_id' => auth()->id(),
        ];
    }
    

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => "{$this->post->user->name} tagged you in a post titled \"{$this->post->title}\".",
        'post_id' => $this->post->id,
        'post_title' => $this->post->title,
        'tagger_name' => $this->post->user->name,
        ];
    }
}
