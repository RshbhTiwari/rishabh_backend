<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    // Define which attributes can be mass assigned
    protected $fillable = [
        'product_id',
        'user_id',
        'firstname',
        'email',
        'rating',
        'comment',
    ];

    /**
     * Relationship: Review belongs to a product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relationship: Review belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
