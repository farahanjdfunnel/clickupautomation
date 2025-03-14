<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $isInstall;

    public function __construct($data)
    {
        $this->data = $data;
        $this->isInstall = $data['type'] === 'INSTALL';
    }

    public function build()
    {
        $subject = $this->isInstall ? 'New App Installation' : 'App Uninstallation';
        return $this->subject($subject)
            ->markdown('emails.app-status');
    }
}
