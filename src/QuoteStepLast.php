<?php

namespace PP\Common;

class QuoteStepLast
{
    /* @var $quote \PP\Common\Quote */
    public $quote;

    public function __construct($quote)
    {
        $this->quote = $quote;
    }

    public function process($nextPage)
    {
        if (!empty($_POST)) {
            if ($this->quote->validate($_POST)) {
                $this->quote->post();
                $this->quote->clearUID();
                header('Location: '.$nextPage);
            }
        }
    }
}
