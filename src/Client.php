<?php

namespace UserBase\Client;

use UserBase\Client\Model\User;
use UserBase\Client\Model\Account;
use UserBase\Client\Model\Policy;
use RuntimeException;

if (!function_exists('curl_file_create')) {
    function curl_file_create($filename, $mimetype = '', $postname = '')
    {
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
    protected $partition;

    public function __construct($baseUrl, $username, $password, $partition = 'dev')
    {
        $this->baseUrl = $baseUrl;
        $this->username = $username;
        $this->password = $password;
        $this->partition = $partition;
    }

    private function getStatusCode($ch)
    {
        $info = curl_getinfo($ch);
        return (int)$info['http_code'];
    }
    
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
    
    public function getPartition()
    {
        return $this->partition;
    }
    
    public function getUsername()
    {
        return $this->username;
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
        foreach ($data['accounts'] as $accountData) {
            $account = new Account();
            $account->setName($accountData['name']);
            $account->setDisplayName($accountData['display_name']);
            $account->setAbout($accountData['about']);
            $account->setPictureUrl($accountData['picture_url']);
            $account->setEmail($accountData['email']);
            $account->setCreatedAt($accountData['created_at']);
            $account->setDeletedAt($accountData['deleted_at']);
            $account->setAccountType($accountData['account_type']);
            
            $user->addAccount($account);
        }

        foreach ($data['policies'] as $policyData) {
            $policy = new Policy();
            $policy->setEffect($policyData['effect']);
            $policy->setResource($policyData['resource']);
            foreach ($policyData['action'] as $action) {
                $policy->addAction($action);
                if ($policy->getEffect() == 'allow') {
                    $roleName = 'ROLE_' . $policy->getResource();
                    $roleName .= '@' . $action;
                    $user->addRole($roleName);
                }
            }
            $user->addPolicy($policy);
        }

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
