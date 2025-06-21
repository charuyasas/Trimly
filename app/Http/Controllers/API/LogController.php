<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Log; // Import the Log model
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Eager load the user associated with the log entry.
        // Select specific user fields to avoid sending sensitive data like password.
        // Order by the latest logs first.
        // Paginate the results.
        $logs = Log::with('user:id,name,email')
                    ->orderBy('created_at', 'desc')
                    ->paginate(15); // Default 15 per page, can be adjusted

        return response()->json($logs);
    }
}
