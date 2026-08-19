<?php

namespace App\Jobs;

use ExpoSDK\Expo;
use ExpoSDK\ExpoMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendExpoPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $tokens;
    protected $title;
    protected $body;
    protected $data;

    public function __construct(array $tokens, string $title, string $body, array $data = [])
    {
        $this->tokens = $tokens;
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
    }

    public function handle()
    {
        if (empty($this->tokens)) {
            return;
        }

        try {
            $expo = new Expo();

            $message = (new ExpoMessage([
                'title' => $this->title,
                'body' => $this->body,
                'data' => $this->data,
            ]));

            $expo->send($message)->to($this->tokens)
                ->push();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Expo Push Notification Error: ' . $e->getMessage());
        }
    }
}
