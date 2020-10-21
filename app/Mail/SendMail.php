<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    //メッセージリスト
    protected $messageList;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($messageList)
    {
        $this->messageList = $messageList;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->text('email.notice')
                    ->subject('【システム通知】設備閾値越え')
                    ->to('satoshi.tomiyama0221@gmail.com')
                    ->with(['messageList' => $this->messageList]);
    }
}