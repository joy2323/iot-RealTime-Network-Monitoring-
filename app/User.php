<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'name', 'email', 'password', 'address', 'phone_number', 'image','role', 'owner_id',
    // ];
    protected $guarded = [
       
    ]; 

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function role()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
    * Check multiple roles
    * @param array $roles
    */
    // public function hasAnyRole($roles)
    // {
    //     return null !== $this->roles()->whereIn('name', $roles)->first();
    // }

    
    /**
    * Check one role
    * @param string $role
    */
    // public function hasRole($role)
    // {
    //     return null !== $this->roles()->where('name', $role)->first();
    // }

    // Deactivate Normal Users like Site admin and UT admin from deleting things
    // public function authorizeRoles($roles)
    // {
    //     if (is_array($roles)){
    //         return $this->hasAnyRole($roles) ||
    //             abort(401, 'Not authorised to Perform this action');
    //     } else {
    //         return $this->hasRole($roles) ||
    //             abort(401, 'This action is Unauthorized');
    //     }
    // }


    public function communication()
    {
        return $this->hasMany(Communication::class);
    }

    public function details()
    {
        return $this->hasMany(Detail::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function location()
    {
        return $this->hasMany(Location::class);
    }

    public function power()
    {
        return $this->hasMany(Power::class);
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function siteReport()
    {
        return $this->hasMany(SiteReport::class);
    }
    
    
}