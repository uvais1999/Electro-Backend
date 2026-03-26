<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use App\Models\UserLoginHistory;
use Illuminate\Http\Request;

class LoginAuditSubscriber
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle user login events.
     */
    public function handleUserLogin($event) {
        UserLoginHistory::create([
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'is_successful' => true,
        ]);
    }

    /**
     * Handle user login failure events.
     */
    public function handleUserLoginFailed($event) {
        UserLoginHistory::create([
            'user_id' => optional($event->user)->id, // User might not exists if credential invalid
            'email' => $event->credentials['email'] ?? 'unknown',
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'is_successful' => false,
        ]);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(
            Login::class,
            [LoginAuditSubscriber::class, 'handleUserLogin']
        );

        $events->listen(
            Failed::class,
            [LoginAuditSubscriber::class, 'handleUserLoginFailed']
        );
    }
}
