<?php

namespace PP\Common\Quote;

class QuoteHelper
{
    private $source;

    private $ref;

    public function __construct($ref, $source)
    {
        $this->source = $source;
        $this->ref = $ref;
    }

    public function googleAdInputField()
    {
        $ar = ['kwds', 'mtch', 'cmid', 'dgid', 'kwid', 'netw', 'dvce', 'crtv', 'adps'];
        $out = [];
        foreach ($ar as $k) {
            $out[$k] = isset($_GET[$k]) ? $_GET[$k] : '';
        }

        return <<<END
<input type="hidden" value="{$out['mtch']} {$out['kwds']}" name="keywords">
<input type="hidden" value="{$out['cmid']}" name="cmid">
<input type="hidden" value="{$out['dgid']}" name="dgid">
<input type="hidden" value="{$out['kwid']}" name="kwid">
<input type="hidden" value="{$out['netw']}" name="netw">
<input type="hidden" value="{$out['dvce']}" name="dvce">
<input type="hidden" value="{$out['crtv']}" name="crtv">
<input type="hidden" value="{$out['adps']}" name="adps">
END;
    }

    public function googleBuildQueryStr()
    {
        $ar = ['kwds', 'mtch', 'cmid', 'dgid', 'kwid', 'netw', 'dvce', 'crtv', 'adps'];
        $out = [];
        foreach ($ar as $field) {
            $out[$field] = $_GET[$field];
        }

        return http_build_query($out);
    }

    public function visitorJS()
    {
        return '<script type="text/javascript" src="//resources.pacificprime.com/widget/visitor.js?ref='.$this->ref.'&tag='.$this->source.'"></script>';
    }

    public function conversionJS($uid)
    {
        return '<script src="//resources.pacificprime.com/widget/conversion.js?uid='.$uid.'"></script>';
    }
}
