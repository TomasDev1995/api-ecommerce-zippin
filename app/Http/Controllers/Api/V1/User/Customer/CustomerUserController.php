<?php

namespace App\Http\Controllers\Api\V1\User\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerUserController extends Controller
{
    /**
     * Display the authenticated user's details.
     */
    public function show()
    {
        $user = Auth::user();
        return response()->json($user);
    }

    /**
     * Update the authenticated user's details.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8|confirmed'
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }
}
