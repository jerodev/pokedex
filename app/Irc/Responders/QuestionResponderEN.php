<?php

namespace App\Irc\Responders;

/**
 *  Answers simple yes/no questions, English edition.
 */
class QuestionResponderEN extends QuestionResponder
{
    public function __construct()
    {
        $this->answers = [
            'Yes',
            'Yes!',
            'Of course',

            'No',
            'No!',
            'Of course not'
        ];

        $this->prefixes = [
            '!can',
            '!has',
            '!is',
            '!should',
            '!will',
        ];
    }
}