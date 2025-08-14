<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        $request->validate([
            'cleaner_id' => 'required|exists:cleaners,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $cleaner = Cleaner::findOrFail($request->cleaner_id);
        
        if (empty($cleaner->fcm_token)) {
            return redirect()->back()->with('error', 'Selected cleaner does not have a registered device token');
        }

        $response = Http::withHeaders([
            'Authorization' => 'key=' . env('FIREBASE_SERVER_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'to' => $cleaner->fcm_token,
            'notification' => [
                'title' => $request->title,
                'body' => $request->body,
                'sound' => 'default',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
            'data' => [
                'cleaner_id' => $cleaner->id,
                'type' => 'admin_notification',
            ],
        ]);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Notification sent successfully!');
        }

        return redirect()->back()->with('error', 'Failed to send notification: ' . $response->body());
    }
}