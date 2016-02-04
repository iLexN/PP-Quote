<?php

namespace PP\Common;

class QuoteStep1
{
    /* @var $quote \PP\Common\Quote */
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
                $this->parseResult($this->quote->post());
                header('Location: '.$nextPage.'?uid='.$this->quote->getUid());
            }
        }
    }

    private function parseResult($result)
    {
        $_SESSION['uid'] = $result;
    }
}
