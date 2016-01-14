<?php

namespace PP\Common;

class Quote implements \ArrayAccess
{
    
    /**
     * url post api endpoint
     * @var string 
     */
    private $url = 'http://r.web7.dev/dbctrl/';
    //private $url = 'http://resources.pacificprime.com/dbctrl/';

    /**
     * post data
     * @var array 
     */
    private $postInfo = array();
    
    /**
     * errors
     * @var array() 
     */
    public $errors = array();
    
    private $fields = array();
    
    private $defaultFieldValue = array();
    
    /**
     * 
     * @param array $option
     */
    public function __construct($setting = array())
    {
        $this->fields = $setting['fields'];
        $this->defaultFieldValue = $setting['default'];
        $this->defaultFieldValue['start_time'] = $this->getStartTime();
    }
    
    
    /**
     * validate form data
     * @param array $postArray
     * @return boolean
     */
    public function validate($postArray)
    {
        $this->postInfo = $postArray;
        
        $error = array();
        foreach ($this->fields as $field => $ruleset) {
            if (empty($ruleset)) {
                continue;
            }

            foreach ($ruleset as $rule) {
                $checkValue = isset($postArray[$field])?$postArray[$field]:'';
                $errorStr = $this->checkRule($rule, $checkValue);
                if (!empty($errorStr)) {
                    $error[$field] = $errorStr;
                }
            }
        }
        
        if (empty($error)) {
            return true;
        }
        $this->errors = $error;
        return false;
    }
    
    /**
     * post 
     */
    public function post()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getPostDate());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result= curl_exec($ch);
        curl_close($ch);
        
        $_SESSION['uid'] = $result;
    }
    
    /**
     * get post data
     * @return array
     */
    private function getPostDate()
    {
        $postData = array_merge($this->defaultFieldValue, $this->postInfo);
        $postData['remote_ip'] = $_SERVER['REMOTE_ADDR'];
        $postData['from_path'] = $_SERVER["REQUEST_URI"];
        $postData['end_time'] =date('Y-m-d H:i:s', time());
        
        $uid = $this->getUid();
        if (!empty($uid)) {
            $postData['uid'] = $uid;
        }
        
        foreach ($postData as $key => $value) {
            if (is_array($value)) {
                $postData[$key] = implode(',', $value);
            }
        }
        return $postData;
    }
    
    /**
     * 
     * @param string $rule
     * @param string $checkValue
     * @return string
     */
    private function checkRule($rule, $checkValue)
    {
        switch ($rule) {
            case 'required':
                if (empty($checkValue)) {
                    return 'empty_field';
                }
                break;
            case 'email':
                if (!$this->checkEmail($checkValue)) {
                    return 'invalid_email';
                }
                break;
            case 'phone':
                if (!$this->checkPhone($checkValue)) {
                    return 'invalid_phone';
                }
                break;
            case 'age':
                if (!$this->checkAge($checkValue)) {
                    return 'invalid_age';
                }
                break;
            case 'ddmmyyyy':
                if (!$this->checkDate($checkValue)) {
                    return 'invalid_date';
                }
            default :
                return 'rule not match';
        }
        
    }
    
    /**
     * 
     * @param int $age
     * @return boolean
     */
    private function checkAge($age)
    {
        if (empty($age)) {
            return true;
        }
        return is_numeric($age);
    }

    /**
     * 
     * @param string $date
     * @return boolean
     */
    public function checkDate($date)
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
     * 
     * @param string $email
     * @return boolean
     */
    public function checkEmail($email)
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
     * check phone
     * @param string $num
     * @return boolean
     */
    private function checkPhone($num)
    {
        if (empty($num)) {
            return true;
        }
        $replaceArray = array('(',')',' ','-','+');
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
    /**
     * check have error of field
     * @param string $key
     * @return boolean
     */
    public function hasError($key)
    {
        return isset($this->errors[$key])?true:false;
    }
    
    /**
     * get error of field
     * @param string $key
     * @return string
     */
    public function getError($key)
    {
        return $this->errors[$key];
    }
    
    /**
     * set up starttime
     * @return type
     */
    private function getStartTime()
    {
        if (!isset($_SESSION['start_time'])) {
            $_SESSION['start_time'] = date('Y-m-d H:i:s', time());
        }
        return $_SESSION['start_time'];
    }
    
    /**
     * get uid
     * @return string
     */
    public function getUid()
    {
        if (isset($_SESSION['uid'])) {
            return $_SESSION['uid'];
        } elseif (isset($_GET['uid'])) {
            return  $_GET['uid'];
        }
        return '';
    }
    
    /**
     * clear uid
     */
    public function clearUID()
    {
        unset($_SESSION['uid']);
    }
    
    public function offsetSet($offset, $value)
    {
        $this->postInfo[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->postInfo[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->postInfo[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->postInfo[$offset]) ? $this->postInfo[$offset] : null;
    }
}
