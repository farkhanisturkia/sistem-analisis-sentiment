<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['comment', 'label'])]
class NaiveBayes extends Model
{
    use HasFactory;

    protected $table = 'naive_bayes';
}