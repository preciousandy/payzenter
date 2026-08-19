<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Live Support Chat Channels
Broadcast::channel('live-support.user.{userId}', function ($user, $userId) {
    // Only the specific user can listen to their own support channel
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('live-support.admin.{topicId}', function ($user, $topicId) {
    // Only authenticated admin/agent users can listen to admin channels
    // Add your admin role check logic here
    return $user->hasRole('admin') || $user->hasRole('support_agent') || $user->hasPermission('view_live_support');
});
