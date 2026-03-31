<?php

namespace App\Services;

use App\Models\NaiveBayes;
use Illuminate\Support\Facades\Cache;

class NaiveBayesService
{
    protected $classes = ['positif', 'netral', 'negatif'];

    protected $vocab = [];
    protected $wordCount = [];
    protected $classCount = [];
    protected $totalWordsPerClass = [];

    public function predict($text)
    {
        $model = Cache::remember('naive_bayes_model', 3600, function () {
            return $this->train();
        });

        // load model
        $this->vocab = $model['vocab'];
        $this->wordCount = $model['wordCount'];
        $this->classCount = $model['classCount'];
        $this->totalWordsPerClass = $model['totalWordsPerClass'];

        $words = $this->preprocess($text);
        $vocabSize = count($this->vocab);
        $totalDocs = array_sum($this->classCount);

        $scores = [];

        foreach ($this->classes as $class) {

            $prior = $this->classCount[$class] / ($totalDocs ?: 1);
            $score = log($prior ?: 1);

            foreach ($words as $word) {
                $wordFreq = $this->wordCount[$class][$word] ?? 0;

                $prob = ($wordFreq + 1) /
                        (($this->totalWordsPerClass[$class] ?? 0) + $vocabSize);

                $score += log($prob);
            }

            $scores[$class] = $score;
        }

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
        $data = NaiveBayes::all();

        $vocab = [];
        $wordCount = [];
        $classCount = [];
        $totalWordsPerClass = [];

        foreach ($this->classes as $class) {
            $wordCount[$class] = [];
            $classCount[$class] = 0;
            $totalWordsPerClass[$class] = 0;
        }

        foreach ($data as $row) {
            $label = $row->label;
            $classCount[$label]++;

            $words = $this->preprocess($row->comment);

            foreach ($words as $word) {
                $vocab[$word] = true;

                $wordCount[$label][$word] =
                    ($wordCount[$label][$word] ?? 0) + 1;

                $totalWordsPerClass[$label]++;
            }
        }

        return [
            'vocab' => $vocab,
            'wordCount' => $wordCount,
            'classCount' => $classCount,
            'totalWordsPerClass' => $totalWordsPerClass
        ];
    }

    protected function preprocess($text)
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);

        $words = explode(' ', $text);

        return array_values(array_filter($words));
    }
}