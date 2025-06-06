<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function index()
    {

        return Inertia::render('customer/settings/Index');
    }

    public function password(Request $request)
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->symbols()->letters()],
        ]);

        try {
            $user = $request->user();

            $user->update([
                'password' => bcrypt($validated['password']),
            ]);

            return back()->with('message', [
                'type' => 'success',
                'body' => 'Password updated successfully'
            ]);
        } catch (\Throwable $th) {
            return back()->with('message', [
                'type' => 'error',
                'body' => 'Something went wrong'
            ]);
        }
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', Rule::unique('users', 'email')->ignore($user)],
            'phone' => ['required', 'string'],
            'avatar' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:5120'],
            'country' => ['required', 'string'],
            'city' => ['required', 'string'],
            'address' => ['required', 'string'],
        ]);

        try {
            if ($request->hasFile('avatar')) {
                $extension = $request->file('avatar')->getClientOriginalExtension();
                $path = $request->file('avatar')->storeAs('avatars', uniqid() . '.' . $extension, 'public');
                $validated['avatar'] = $path;
            } else {
                unset($validated['avatar']);
            }

            $user->update($validated);

            return redirect()->route('customer.account')->with(
                'message',
                [
                    'type' => 'success',
                    'body' => 'Updated successfully'
                ]
            );
        } catch (\Throwable $th) {
            return back()->with('messages', [
                'type' => 'error',
                'body' => 'Something went wrong'
            ]);
        }

    }
}
