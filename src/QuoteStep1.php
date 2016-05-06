<?php

namespace PP\Common\Quote;

class QuoteStep1
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
        $this->quote->clearUID();
        if (!empty($data)) {
            if ($this->quote->validate($_POST)) {
                $this->parseResult($this->quote->post());
                return true;
            }
        }
        return false;
    }

    private function parseResult($result)
    {
        $_SESSION['uid'] = $result;
    }
}
