<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use sinkcup\LaravelMakeAuthSocialite\SocialAccount;
use Validator;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $user = auth()->user();
        $social_login_providers = config('auth.social_login.providers');
        $linked_providers = SocialAccount::where('user_id', $user->id)->select(['provider'])->pluck('provider')->all();
        return view('user.profile_edit', compact('user', 'social_login_providers', 'linked_providers'));
    }

    /**
     * Update the user's profile.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();
        Validator::make($request->all(), [
            'email' => [
                Rule::unique('users')->ignore($user->id),
            ],
            'name' => 'string|max:255',
        ])->validate();
        $user->update($request->all());
        return redirect(route('profile.edit'));
    }
}
