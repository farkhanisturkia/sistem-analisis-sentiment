<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Services\NaiveBayesService;

class CommentController extends Controller
{
    public function store(Request $request, NaiveBayesService $nb)
    {
        $request->validate([
            'comment' => 'required|string',
            'product_id' => 'required|exists:products,id',
        ]);

        $result = $nb->predict($request->comment);

        Comment::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'comment' => $request->comment,
            'label' => $result['label'],
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan');
    }
}