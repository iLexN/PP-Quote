<?php

namespace PP\Common;

class QuoteController
{
    public $quote;
    
    public function __construct($quote)
    {
        $this->quote = $quote;
    }
    
    public function firstStep($nextPage)
    {
        $this->quote->clearUID();
        if (!empty($_POST)) {
            if ($this->quote->validate($_POST)) {
                $this->quote->post();
                header('Location: '.$nextPage.'?uid='.$this->quote->getUid());
            }
        }
    }
    
    public function lastStep($nextPage)
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
