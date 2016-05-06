<?php

namespace PP\Common\Quote;

class QuoteValid
{
    /**
     * @param int $age
     *
     * @return bool
     */
    public static function checkAge($age)
    {
        if (empty($age)) {
            return true;
        }

        return is_numeric($age);
    }

    /**
     * @param string $date
     *
     * @return bool
     */
    public static function checkDate($date)
    {
        if (empty($date)) {
            return true;
        }
        $dates = explode('/', $date);
        $d = isset($dates[0]) ? $dates[0] : false;
        $m = isset($dates[1]) ? $dates[1] : false;
        $y = isset($dates[2]) ? $dates[2] : false;

        return checkdate(intval($m), intval($d), intval($y));
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public static function checkEmail($email)
    {
        if (empty($email)) {
            return true;
        }
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * check phone.
     *
     * @param string $num
     *
     * @return bool
     */
    public static function checkPhone($num)
    {
        if (empty($num)) {
            return true;
        }
        $replaceArray = ['(', ')', ' ', '-', '+'];
        $new_str = str_replace($replaceArray, '', $num);

        if (strlen($new_str) < 6) {
            return false;
        }

        if (!is_numeric($new_str)) {
            return false;
        } else {
            return true;
        }
    }
}
