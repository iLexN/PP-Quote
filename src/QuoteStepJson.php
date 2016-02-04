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
                return $this->parseResult( $this->quote->post() );
            }
        }
    }

    private function parseResult($result){
        $json = json_decode($result,1);
        if ( !$json['result']) {
            $this->quote->errors = $json['error'];
            return false;
        }
        return true;
    }
}
