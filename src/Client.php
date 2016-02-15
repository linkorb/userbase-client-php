<?php

namespace UserBase\Client;

use UserBase\Client\Model\User;
use UserBase\Client\Model\Account;
use UserBase\Client\Model\AccountUser;
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

    public function getUsersWithDetails()
    {
        $data = $this->getData('/users?details');
        $users = array();
        foreach ($data['items'] as $item) {
            $user = $this->itemToUser($item);
            $users[] = $user;
        }
        return $users;
    }
    
    protected function itemToUser($data)
    {
        $user = new User($data['username']);
        $user->setUsername($data['username']);
        $user->setDisplayName($data['display_name']);
        $user->setEmail($data['email']);
        $user->setPictureUrl($data['picture_url']);
        $user->setPassword($data['password']);
        $user->setCreatedAt($data['created_at']);
        $user->setDeletedAt($data['deleted_at']);
        $user->setPasswordUpdatedAt($data['passwordupdated_at']);
        if (isset($data['accounts'])) {
            foreach ($data['accounts'] as $accountData) {
                $accountUser = new AccountUser();
                $accountUser->setUserName($user->getName());
                $accountUser->setAccountName($accountData['name']);
                $account = $this->itemToAccount($accountData);
                $accountUser->setAccount($account);
                $user->addAccountUser($accountUser);
            }
        }

        if (isset($data['policies'])) {
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
        }
        return $user;
    }
    
    protected function itemToAccount($data)
    {
        $account = new Account($data['name']);
        $account->setDisplayName($data['display_name']);
        $account->setAbout($data['about']);
        $account->setEmail($data['email']);
        if (isset($data['type'])) {
            $account->setAccountType($data['type']);
        }
        if (isset($data['account_type'])) {
            $account->setAccountType($data['account_type']);
        }
        $account->setPictureUrl($data['picture_url']);
        $account->setCreatedAt($data['created_at']);
        $account->setDeletedAt($data['deleted_at']);
        if (isset($data['members'])) {
            foreach ($data['members'] as $accountUserData) {
                $accountUser = new AccountUser();
                $accountUser->setAccountName($account->getName());
                $accountUser->setUsername($accountUserData['user_name']);
                $accountUser->setIsOwner($accountUserData['is_owner']);
                $account->addAccountUser($accountUser);
            }
        }
        return $account;
    }
    
    public function getUserByUsername($username)
    {
        
        $data = $this->getData('/users/' . $username);
        $user = $this->itemToUser($data);
        return $user;
    }
    
    public function getAccountByName($name)
    {
        $data = $this->getData('/accounts/' . $name);
        $account = $this->itemToAccount($data);
        return $account;
    }
    
    public function getAccountsWithDetails()
    {
        $data = $this->getData('/accounts?details');
        $users = array();
        foreach ($data['items'] as $item) {
            $user = $this->itemToAccount($item);
            $users[] = $user;
        }
        return $users;
        //print_r($data);
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
