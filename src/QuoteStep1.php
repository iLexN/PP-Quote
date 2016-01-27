<?php

namespace PP\Common;

class QuoteStep1
{
    public $quote;

    public function __construct($quote)
    {
        $this->quote = $quote;
    }

    public function process($nextPage)
    {
        $this->quote->clearUID();
        if (!empty($_POST)) {
            if ($this->quote->validate($_POST)) {
                $this->quote->post();
                header('Location: '.$nextPage.'?uid='.$this->quote->getUid());
            }
        }
    }
}
