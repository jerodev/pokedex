<?php

namespace App\Irc\Responders;

/**
 *  Answers simple yes/no questions, Dutch edition.
 */
class QuestionResponderNL extends QuestionResponder
{
    public function __construct()
    {
        $this->answers = [
            'Ja',
            'Ja!',
            'Natuurlijk',

            'Nee',
            'Nee!',
            'Natuurlijk niet',
        ];

        $this->prefixes = [
            '!heeft',
            '!is',
            '!kan',
            '!zal',
        ];
    }
}
