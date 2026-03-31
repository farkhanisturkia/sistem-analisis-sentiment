<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NaiveBayesService;

class CommentController extends Controller
{
    public function store(Request $request, NaiveBayesService $nb)
    {
        $result = $nb->predict($request->comment);

        Comment::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'comment' => $request->comment,
            'label' => $result['label'],
        ]);

        return back();
    }
}
