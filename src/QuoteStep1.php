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

    public function process()
    {
        $this->quote->clearUID();
        if (!empty($_POST)) {
            if ($this->quote->validate($_POST)) {
                $this->parseResult($this->quote->post());
                //header('Location: '.$nextPage.'?uid='.$this->quote->getUid());
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
