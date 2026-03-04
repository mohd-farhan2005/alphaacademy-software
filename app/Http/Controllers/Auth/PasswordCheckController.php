<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PasswordCheckController extends Controller
{
    /**
     * Check if the user's password hash has changed.
     */
    public function check(Request $request)
    {
        $user = $request->user();
        
        // If the session doesn't have the hash, initialize it
        if (!$request->session()->has('password_hash_check')) {
            $request->session()->put('password_hash_check', $user->password);
            return response()->json(['changed' => false]);
        }

        $sessionHash = $request->session()->get('password_hash_check');
        
        // Check if the current user password matches the one in session
        if ($user->password !== $sessionHash) {
            return response()->json(['changed' => true]);
        }

        return response()->json(['changed' => false]);
    }

    /**
     * Verify the entered password against the new hash.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (Hash::check($request->password, $user->password)) {
            // Update the session hash to the new valid hash
            $request->session()->put('password_hash_check', $user->password);
            
            // Also need to manually update the regular auth session hash
            // to prevent Laravel's built-in session invalidation from logging them out
            $request->session()->put([
                'password_hash_' . Auth::getDefaultDriver() => $user->getAuthPassword(),
            ]);
            
            return response()->json(['success' => true]);
        }

        return response()->json([
            'success' => false,
            'message' => 'The provided password does not match our records.'
        ], 422);
    }
}
