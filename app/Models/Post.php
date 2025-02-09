<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Import the User model

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'type',
        'user_id', //foreign key
        'firstName',
        'lastName'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
