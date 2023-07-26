<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'author', 'isbn', 'published_at', 'copies'];

    // Define the relationship between Book and Checkouts (many-to-many)
    public function checkouts()
    {
        return $this->belongsToMany(User::class, 'checkouts')->withPivot('checkout_date', 'return_date');
    }
}
