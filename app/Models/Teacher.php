<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Teacher extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

     // Specify the table if it's not named plural of the model
    protected $table = 'teachers';


    // Set the fillable attributes (which can be mass-assigned)
    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'data',  // JSON data field for additional teacher information
    ];

    // Protect these attributes from mass-assignment
    protected $guarded = [];

    // Cast the `data` attribute to an array or object when retrieving it
    protected $casts = [
        'data' => 'array',  // Automatically casts the JSON `data` field to an array
        'password' => 'hashed',
    ];

    // Ensure the password is hidden in serialization
    protected $hidden = [
        'password', 
        'remember_token',
    ];

    // Automatically hash the password when it's being set
    // public function setPasswordAttribute($value)
    // {
    //     $this->attributes['password'] = bcrypt($value);
    // }

    // Add any custom methods or relationships here
}
