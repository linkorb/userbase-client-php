<?php

namespace UserBase\Client;

use UserBase\Client\User;
use RuntimeException;

if (!function_exists('curl_file_create')) {
    function curl_file_create($filename, $mimetype = '', $postname = '') {
        return "@$filename;filename="
            . ($postname ?: basename($filename))
            . ($mimetype ? ";type=$mimetype" : '');
    }
}

class Client
{
    protected $baseUrl;
    protected $username;
    protected $password;

    public function __construct($baseUrl, $username, $password)
    {
        $this->baseUrl = $baseUrl;
        $this->username = $username;
        $this->password = $password;
    }
    
    private function getStatusCode($ch)
    {
        $info = curl_getinfo($ch);
        return (int)$info['http_code'];
    }
    
    public function getUserByUsername($username)
    {
        $data = $this->getData('/users/' . $username);
        $user = new User($data['username']);
        $user->setUsername($data['username']);
        $user->setDisplayName($data['display_name']);
        $user->setEmail($data['email']);
        $user->setPictureUrl($data['picture_url']);
        $user->setPassword($data['password']);
        $user->setCreatedAt($data['created_at']);
        $user->setDeletedAt($data['deleted_at']);
        $user->setPasswordUpdatedAt($data['passwordupdated_at']);
        
        print_r($userdata);
        return $user;
    }
    
    public function getData($uri)
    {
        
        $url =  $this->baseUrl . $uri;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);

        $json = curl_exec($ch);
        $info = curl_getinfo($ch);
        $code = $this->getStatusCode($ch);
        if ($code != 200) {
            throw new RuntimeException("HTTP Status code: " . $code);
        }
        $data = @json_decode($json, true);
        curl_close($ch);

        return $data;
    }
}
