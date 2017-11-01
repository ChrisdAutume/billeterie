<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SlackHook extends Model
{
    use Notifiable;

    public function routeNotificationForSlack() {
        return config('slack.endpoint');
    }
}
