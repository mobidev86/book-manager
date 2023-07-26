<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'book_id', 'checkout_date', 'return_date'];

    // Define the relationship between Checkout and User (many-to-one)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship between Checkout and Book (many-to-one)
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
