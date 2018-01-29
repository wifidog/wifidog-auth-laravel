<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\SocialUser
 *
 * @property-read \App\User $user
 * @mixin \Eloquent
 */
class SocialUser extends Model
{
    protected $fillable = ['user_id', 'provider_user_id', 'provider'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
