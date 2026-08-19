<?php

namespace App\Livewire;

use App\Events\LiveSupportMessage as EventsLiveSupportMessage;
use App\Models\liveSupportMessage;
use App\Models\liveSupportTopic;
use App\Services\HashIdService;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class LiveChat extends Component
{
    use LivewireAlert;

    public $messages = [];
    public $message;
    public $lastMessageId = 0;

    public $topic;


    public function mount($hashed_topic_id)
    {
        $this->topic = (new HashIdService)->getRecord($hashed_topic_id, liveSupportTopic::class);

        $this->loadUserMessages();
    }


    public function sendMessage()
    {
        if (!$this->message) {
            $this->alert('warning', 'Please enter a message');
            return;
        }

        $message =  liveSupportMessage::create([
            'topic_id' => $this->topic->id,
            'user_id' => auth()->id(),
            'agent_id' => null,
            'message' => $this->message
        ]);

        $message->readable_created_at = $message->created_at->diffForHumans();


        broadcast(new EventsLiveSupportMessage($message))->toOthers();



        $this->message = '';
    }



    public function loadUserMessages()
    {
        if (!$this->topic) {
            return;
        }

        $this->messages = liveSupportMessage::whereUserId(auth()->id())->whereTopicId($this->topic->id)
            ->orderBy('id', 'asc')
            ->get()->toArray();




        // dd($this->topic, $this->messages);
    }


    public function handleNewMessage($data)
    {
        // Security check: Only process messages for current user and topic
        if ($data['topic_id'] == $this->topic->id && $data['user_id'] == auth()->id()) {
            $this->messages[] = $data;
            $this->scrollToBottom();
        }
    }

    /**
     * Mark messages as read when user loads the chat
     */
    public function markMessagesAsRead()
    {
        // Add logic to mark messages as read if you implement read receipts
        // This could help admins know when users have seen their responses
    }

    public function scrollToBottom()
    {
        $this->dispatch('scroll-to-bottom');
    }

    public function render()
    {
        return view('livewire.live-chat')->extends('layouts.auth')->title('Live Chat');
    }
}
