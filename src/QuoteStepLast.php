<?php

namespace PP\Common\Quote;

class QuoteStepLast
{
    /**
     * @var Quote
     */
    public $quote;

    public function __construct($quote)
    {
        $this->quote = $quote;
    }

    public function process()
    {
        if (!empty($_POST)) {
            if ($this->quote->validate($_POST)) {
                $this->quote->post();
                $this->quote->clearUID();
                return true;
            }
        }
        return false;
    }
}
