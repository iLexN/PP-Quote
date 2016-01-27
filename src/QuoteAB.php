<?php

namespace PP\Common;

class QuoteAB
{
    private $path;

    private $key;

    private $version = array();

    /**
     *
     * @param type $path    json file path
     * @param type $key  unique key
     * @throws Exception
     */
    public function __construct($path, $version = array(), $key = 'langing')
    {
        $this->checkSession();
        $this->checkPath($path);
        $this->checkData($version);

        $this->path = $path;
        $this->key = $key;
        $this->version = $version;
    }

    public function getVersion()
    {
        if (!isset($_SESSION['abTest'][$this->key]) && $_SESSION['abTest'][$this->key] == '') {
            $_SESSION['abTest'][$this->key] = $this->getAB($this->getJsonDate());
        }
        return $this->version[$_SESSION['abTest'][$this->key]];
    }

    private function getJsonDate()
    {
        $numJson = file_get_contents($this->path);

        if (empty($numJson)) {
            return array('A'=>0,'B'=>0);
        }
        return json_decode($numJson, true);
    }

    private function getAB($data = array())
    {
        $versionAB = '';
        if ($data['A'] <= $data['B']) {
            $data['A'] =  $data['A'] + 1 ;
            $versionAB =  'A';
        } else {
            $data['B'] =  $data['B'] + 1 ;
            $versionAB =  'B';
        }
        $this->writeJsonData($data);
        return $versionAB;
    }

    private function writeJsonData($data)
    {
        file_put_contents($this->path, json_encode($data));
    }

    private function checkPath($path)
    {
        if (!is_writable($path)) {
            throw new \Exception('Path not exist or not writeable : ' . $path);
        }
    }

    private function checkData($version)
    {
        if (empty($version) || !array_key_exists('A', $version) || !array_key_exists('B', $version)) {
            throw new \InvalidArgumentException('version data is wrong, please set it ["A"=>"versionA","B"=>"versionB"]');
        }
    }

    private function checkSession(){
        if (!isset($_SESSION)) {
            throw new \RuntimeException('Session not found. Forget session_start() ?');
        }
    }

}
