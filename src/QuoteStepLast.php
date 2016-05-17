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

    public function process($data)
    {
        if (!empty($_POST)) {
            if ($this->quote->validate($data)) {
                $this->quote['completed'] = 1;
                $this->quote->post();
                $this->quote->clearUID();

                return true;
            }
        }

        return false;
    }
}
