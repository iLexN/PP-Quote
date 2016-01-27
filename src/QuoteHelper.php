<?php

namespace PP\Common;

class QuoteHelper
{
    public static function googleAdInputField()
    {
        return <<<END
<input type="hidden" value="{$_GET['mtch']} {$_GET['kwds']}" name="keywords">
<input type="hidden" value="{$_GET['cmid']}" name="cmid">
<input type="hidden" value="{$_GET['dgid']}" name="dgid">
<input type="hidden" value="{$_GET['kwid']}" name="kwid">
<input type="hidden" value="{$_GET['netw']}" name="netw">
<input type="hidden" value="{$_GET['dvce']}" name="dvce">
<input type="hidden" value="{$_GET['crtv']}" name="crtv">
<input type="hidden" value="{$_GET['adps']}" name="adps">
END;
    }

    public static function googleBuildQueryStr()
    {
        $ar = array('kwds', 'mtch', 'cmid', 'dgid', 'kwid', 'netw', 'dvce', 'crtv', 'adps');
        $out = array();
        foreach ($ar as $field) {
            $out[$field] = $_GET[$field];
        }

        return http_build_query($out);
    }

    public static function visitorJS($source)
    {
        return '<script type="text/javascript" src="//resources.pacificprime.com/widget/visitor.js?ref=PPI&tag='.$source.'"></script>';
    }

    public static function conversionJS($uid)
    {
        return '<script src="//resources.pacificprime.com/widget/conversion.js?uid='.$uid.'"></script>';
    }
}
