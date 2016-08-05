<?php

namespace PP\Common\Quote;

class Quote implements \ArrayAccess
{
    /**
     * url post api endpoint.
     *
     * @var string
     */
    private $url = '';

    /**
     * post data.
     *
     * @var array
     */
    private $postInfo = [];

    /**
     * errors.
     *
     * @var array
     */
    public $errors = [];

    private $fields = [];

    private $defaultFieldValue = [];

    /**
     * @param string $url
     * @param array  $setting
     */
    public function __construct($url, $setting = [])
    {
        $this->url = $url;

        if (!empty($setting)) {
            $this->fields = $setting['fields'];
            $this->defaultFieldValue = $setting['default'];
        }
        $this->defaultFieldValue['start_time'] = $this->getStartTime();
        $this->defaultFieldValue['referred_domain'] = $this->getRefDomain();
        $this->setPagePath();
    }

    /**
     * validate form data.
     *
     * @param array $postArray
     *
     * @return bool
     */
    public function validate($postArray)
    {
        $this->postInfo = $postArray;

        $this->errors = $this->getValidateError();

        if (empty($this->errors)) {
            return true;
        }

        return false;
    }

    /**
     * post.
     */
    public function post()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getPostDate());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * get post data.
     *
     * @return array
     */
    private function getPostDate()
    {
        $postData = array_merge($this->defaultFieldValue, $this->postInfo);
        $postData['remote_ip'] = $this->getClientIp();
        $postData['from_path'] = $_SERVER['REQUEST_URI'];
        $postData['end_time'] = date('Y-m-d H:i:s', time());
        $postData['page_path'] = implode(' -> ', $_SESSION['page_path']);

        $uid = $this->getUid();
        if (!empty($uid)) {
            $postData['uid'] = $uid;
        }

        foreach ($postData as $key => $value) {
            if (is_array($value)) {
                $postData[$key] = implode(',', $value);
            }
        }
        $_SESSION = array_merge($_SESSION, $postData);

        return $postData;
    }

    private function getValidateError()
    {
        $error = [];
        foreach ($this->fields as $field => $ruleset) {
            if (empty($ruleset)) {
                continue;
            }
            $error = $this->checkFieldsbyRule($field, $ruleset);
        }

        return $error;
    }

    private function checkFieldsbyRule($field, $ruleset)
    {
        $error = [];
        foreach ($ruleset as $rule) {
            $errorStr = $this->checkRule($rule, $this->offsetGet($field));
            if (!empty($errorStr)) {
                $error[$field] = $errorStr;
            }
        }

        return $error;
    }

    /**
     * @param string $rule
     * @param string $checkValue
     *
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
                if (!QuoteValid::checkEmail($checkValue)) {
                    return 'invalid_email';
                }
                break;
            case 'phone':
                if (!QuoteValid::checkPhone($checkValue)) {
                    return 'invalid_phone';
                }
                break;
            case 'age':
                if (!QuoteValid::checkAge($checkValue)) {
                    return 'invalid_age';
                }
                break;
            case 'ddmmyyyy':
                if (!QuoteValid::checkDate($checkValue)) {
                    return 'invalid_date';
                }
                break;
            default:
                return 'rule not match';
        }
    }

    /**
     * check have error of field.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasError($key)
    {
        return isset($this->errors[$key]) ? true : false;
    }

    /**
     * check have error exist.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors) ? true : false;
    }

    /**
     * get error of field.
     *
     * @param string $key
     *
     * @return string
     */
    public function getError($key)
    {
        return $this->errors[$key];
    }

    /**
     * get all error.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * get Client IP , support cloudflare.
     *
     * @return string
     */
    public function getClientIp()
    {
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return  $_SERVER['HTTP_CF_CONNECTING_IP'];
        }

        return  $_SERVER['REMOTE_ADDR'];
    }

    /**
     * set up starttime.
     *
     * @return string
     */
    private function getStartTime()
    {
        if (!isset($_SESSION['start_time'])) {
            $_SESSION['start_time'] = date('Y-m-d H:i:s', time());
        }

        return $_SESSION['start_time'];
    }

    /**
     * get ref domain.
     *
     * @return string
     */
    private function getRefDomain()
    {
        if (isset($_SESSION['referred_domain'])) {
            return $_SESSION['referred_domain'];
        }
        $_SESSION['referred_domain'] = '';

        $refUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $referer = parse_url($refUrl, PHP_URL_HOST);
        $serverHost = $_SERVER['HTTP_HOST'];

        if ($serverHost != $referer) {
            $_SESSION['referred_domain'] = $referer;
        }

        return $_SESSION['referred_domain'];
    }

    /**
     * set Page Path array.
     */
    private function setPagePath()
    {
        $url = $_SERVER['REQUEST_URI'];
        if (!isset($_SESSION['page_path']) || !is_array($_SESSION['page_path'])) {
            $_SESSION['page_path'] = [$url];

            return;
        }

        if ($_SESSION['page_path'][count($_SESSION['page_path']) - 1] != $url) {
            $_SESSION['page_path'][] = $url;
        }
    }

    /**
     * get uid.
     *
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
     * clear uid.
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
