<?php
// EmailRecipient - Simple notifiable model for sending emails to email addresses
// Used with Laravel notification system to send emails to recipients
// Created to work with EmailNotification for flexible email sending

namespace App\Models;

use Illuminate\Notifications\Notifiable;

class EmailRecipient
{
    use Notifiable;

    protected $email;
    protected $name;

    public function __construct($email, $name = null)
    {
        $this->email = $email;
        $this->name = $name;
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return array|string
     */
    public function routeNotificationForMail()
    {
        return $this->name ? [$this->email => $this->name] : $this->email;
    }
}
