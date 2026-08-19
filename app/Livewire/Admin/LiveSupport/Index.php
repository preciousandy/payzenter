<?php

namespace App\Livewire\Admin\LiveSupport;

use App\Events\LiveSupportMessage as EventsLiveSupportMessage;
use App\Models\liveSupportMessage;
use App\Models\liveSupportTopic;
use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Index extends Component
{
    use LivewireAlert;


    public $topics = [];
    public $users = [];
    public $topicId;
    public $userId;
    public $messages = [];
    public $message;
    public $lastMessageId = 0;
    public $topicName;

    // protected $listeners = ['loadUserMessages'];


    public function mount()
    {
        $this->topics = liveSupportTopic::orderBy('name')->withCount('messages')->get();

        // $this->messages = liveSupportMessage::all();
    }

    public function selectTopic($topicId)
    {
        $this->topicId = $topicId;

        $this->userId = null;

        $this->messages = [];

        $messagesId = liveSupportMessage::whereTopicId($topicId)->pluck('user_id')->toArray();

        $this->users = User::query()->whereIn('id', $messagesId)->get()->each(function ($user) use ($topicId) {
            $user->messages_count = liveSupportMessage::whereUserId($user->id)->whereTopicId($topicId)->count();
        });

        // Dispatch event to frontend to update the Pusher subscription
        $this->dispatch('topicSelected', $topicId);
    }

    public function selectUser($userId)
    {
        $this->userId = $userId;


        $this->loadUserMessages();
    }

    public function loadUserMessages()
    {
        if (!$this->topicId  || !$this->userId) {
            return;
        }

        // Security check: Ensure admin can only load messages from authorized topics
        $this->authorizeTopicAccess($this->topicId);

        $this->messages = liveSupportMessage::whereUserId($this->userId)->whereTopicId($this->topicId)
            ->orderBy('id', 'asc')
            ->get()->toArray();

        $this->scrollToBottom();
    }

    /**
     * Authorize admin access to specific topic
     */
    private function authorizeTopicAccess($topicId)
    {
        // Add your business logic here to check if the current admin
        // is authorized to handle this topic. For example:
        // - Check if admin is assigned to this topic
        // - Check if admin has permission for this topic category
        // - Add any other business rules

        // For now, we just verify the topic exists
        $topic = liveSupportTopic::find($topicId);
        if (!$topic) {
            abort(404, 'Topic not found');
        }

        // Add more specific authorization logic here as needed
    }

    public function sendMessage()
    {
        if (!$this->message) {
            $this->alert('warning', 'Please enter a message');
            return;
        }

        // $data = [
        //     'topic_id' => $this->topicId,
        //     'user_id' => $this->userId,
        //     'agent_id' => auth()->id(),
        //     'message' => $this->message
        // ];


        $message =  liveSupportMessage::create([
            'topic_id' => $this->topicId,
            'user_id' => $this->userId,
            'agent_id' => auth()->id(),
            'message' => $this->message
        ]);

        $message->readable_created_at = $message->created_at->diffForHumans();


        broadcast(new EventsLiveSupportMessage($message))->toOthers();


        // $this->messages[] = $message;

        // $this->lastMessageId = $message->id;

        $this->message = '';
    }

    public function handleNewMessage($data)
    {
        // Security check: Only process messages for current conversation
        if ($data['topic_id'] == $this->topicId && $data['user_id'] == $this->userId) {
            // Add the message to current conversation
            $this->messages[] = $data;
            $this->scrollToBottom();

            // Update user list to show new message count
            $this->refreshUsersList();
        } elseif ($data['topic_id'] == $this->topicId) {
            // Message is for current topic but different user - update user list
            $this->refreshUsersList();
        }
    }

    /**
     * Refresh the users list to show updated message counts
     */
    private function refreshUsersList()
    {
        if (!$this->topicId) {
            return;
        }

        $messagesId = liveSupportMessage::whereTopicId($this->topicId)->pluck('user_id')->toArray();

        $this->users = User::query()->whereIn('id', $messagesId)->get()->each(function ($user) {
            $user->messages_count = liveSupportMessage::whereUserId($user->id)->whereTopicId($this->topicId)->count();
        });
    }

    public function addTopic()
    {
        $this->validate([
            'topicName' => 'required'
        ]);

        liveSupportTopic::create([
            'name' => $this->topicName
        ]);

        $this->flash('success', 'Topic added successfully', [], route('admin.live-support'));
    }

    public function deleteTopic($topicId)
    {
        $topic = liveSupportTopic::findOrFail($topicId);

        $topic->messages()->delete();

        $topic->delete();

        $this->flash('success', 'Topic deleted successfully', [], route('admin.live-support'));
    }

    public function scrollToBottom()
    {
        $this->dispatch('scroll-to-end');
    }


    public function render()
    {
        return view('livewire.admin.live-support.index')->extends('layouts.admin');
    }
}
