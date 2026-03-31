<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['desc', 'img_filename'])]
class Product extends Model
{
    use HasFactory;

    // Relasi ke Comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}