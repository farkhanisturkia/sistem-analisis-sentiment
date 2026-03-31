<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Comment;
use App\Models\NaiveBayes;
use Illuminate\Support\Facades\Cache;

#[Signature('nb:update')]
#[Description('Update Naive Bayes from comments table into naive_bayes table')]
class UpdateNaiveBayes extends Command
{
    public function handle(): int
    {
        $this->info('🚀 Update Naive Bayes...');

        // kosongkan data training lama
        NaiveBayes::truncate();

        $comments = Comment::select('comment', 'label')->get();

        $bar = $this->output->createProgressBar($comments->count());
        $bar->start();

        foreach ($comments as $c) {
            NaiveBayes::create([
                'comment' => $c->comment,
                'label' => $c->label,
            ]);

            $bar->advance();
        }

        $bar->finish();

        // reset cache model
        Cache::forget('naive_bayes_model');

        $this->newLine(2);
        $this->info('✅ Update selesai!');
        $this->info('Total data: ' . $comments->count());

        return self::SUCCESS;
    }
}