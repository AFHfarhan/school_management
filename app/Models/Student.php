<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SoftDeletes;


class Student extends Model
{
    //// Specify the table if it's not named plural of the model
    protected $table = 'students';

    // Set the fillable attributes (which can be mass-assigned)
    protected $fillable = [
        'name',
        'data',  // JSON data field for additional student information
    ];

    // Cast the `data` attribute to an array or object when retrieving it
    protected $casts = [
        'data' => 'array',  // Automatically casts the JSON `data` field to an array
    ];

    // Define any relationships here (if needed, e.g., a Student may belong to a Class)
    // Example:
    // public function classes() {
    //     return $this->belongsToMany(ClassModel::class);
    // }

    // You can also add any custom methods related to students, such as:
    // public function getFullName() {
    //     return "{$this->first_name} {$this->last_name}";
    // }
}
