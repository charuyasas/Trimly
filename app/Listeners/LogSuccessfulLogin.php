<?php

namespace App\Listeners;

use App\Models\Log;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request; // Import Request
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSuccessfulLogin
{
    protected $request;

    /**
     * Create the event listener.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        Log::create([
            'user_id' => $event->user->id,
            'action' => 'user_logged_in',
            'message' => "User {$event->user->email} logged in.",
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
        ]);
    }
}
