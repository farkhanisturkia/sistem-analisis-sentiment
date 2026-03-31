<?php

namespace App\Services;

use App\Models\Comment;

class NaiveBayesService
{
    protected $classes = ['positif', 'netral', 'negatif'];

    protected $vocab = [];
    protected $wordCount = [];
    protected $classCount = [];
    protected $totalWordsPerClass = [];

    public function predict($text)
    {
        $this->train();

        $words = $this->preprocess($text);
        $vocabSize = count($this->vocab);
        $totalDocs = array_sum($this->classCount);

        $scores = [];

        foreach ($this->classes as $class) {

            // prior
            $prior = $this->classCount[$class] / ($totalDocs ?: 1);
            $score = log($prior ?: 1);

            foreach ($words as $word) {
                $wordFreq = $this->wordCount[$class][$word] ?? 0;

                // Laplace smoothing
                $prob = ($wordFreq + 1) /
                        (($this->totalWordsPerClass[$class] ?? 0) + $vocabSize);

                $score += log($prob);
            }

            $scores[$class] = $score;
        }

        // aturan netral
        if (abs(($scores['positif'] ?? 0) - ($scores['negatif'] ?? 0)) < 0.5) {
            return [
                'label' => 'netral',
                'scores' => $scores
            ];
        }

        arsort($scores);

        return [
            'label' => array_key_first($scores),
            'scores' => $scores
        ];
    }

    protected function train()
    {
        $comments = Comment::all();

        foreach ($this->classes as $class) {
            $this->wordCount[$class] = [];
            $this->classCount[$class] = 0;
            $this->totalWordsPerClass[$class] = 0;
        }

        foreach ($comments as $row) {
            $label = $row->label;
            $this->classCount[$label]++;

            $words = $this->preprocess($row->comment);

            foreach ($words as $word) {
                $this->vocab[$word] = true;

                $this->wordCount[$label][$word] =
                    ($this->wordCount[$label][$word] ?? 0) + 1;

                $this->totalWordsPerClass[$label]++;
            }
        }
    }

    protected function preprocess($text)
    {
        $text = strtolower($text);

        // hapus simbol
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);

        $words = explode(' ', $text);

        return array_values(array_filter($words));
    }
}