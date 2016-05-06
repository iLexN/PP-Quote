<?php

namespace PP\Common\Quote;

class QuoteStepJson
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
                return $this->parseResult($this->quote->post());
            }
        }
    }

    private function parseResult($result)
    {
        $json = json_decode($result, 1);
        if (!$json['result']) {
            $this->quote->errors = $json['error'];
            return false;
        }
        return true;
    }
}
