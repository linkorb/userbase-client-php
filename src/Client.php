<?php

namespace UserBase\Client;

use UserBase\Client\Model\User;
use UserBase\Client\Model\Account;
use UserBase\Client\Model\AccountUser;
use UserBase\Client\Model\AccountEmail;
use UserBase\Client\Model\AccountProperty;
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
        $user->setPassword($data['password']);
        $user->setCreatedAt($data['created_at']);
        $user->setDeletedAt($data['deleted_at']);
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
        $account->setUrl($data['url']);
        $account->setEmail($data['email']);
        $account->setEmailVerified($data['email_verified']);
        $account->setMobile($data['mobile']);
        $account->setMobileVerified($data['mobile_verified']);
        $account->setStatus($data['status']);

        if (isset($data['type'])) {
            $account->setAccountType($data['type']);
        }
        if (isset($data['account_type'])) {
            $account->setAccountType($data['account_type']);
        }
        $account->setPictureUrl($data['picture_url']);
        $account->setCreatedAt($data['created_at']);
        $account->setDeletedAt($data['deleted_at']);
        $account->setMessage($data['message']);
        $account->setExpireAt($data['expire_at']);


        if (isset($data['emails'])) {
            foreach ($data['emails'] as $accountEmailData) {
                $accountEmail = new AccountEmail();
                $accountEmail->setAccountName($account->getName());
                $accountEmail->setEmail($accountEmailData['email']);
                $accountEmail->setVerifiedAt($accountEmailData['verified_at']);
                if ($accountEmailData['email'] == $data['email']) {
                    $accountEmail->setPrimary(true);
                }
                $account->addAccountEmail($accountEmail);
            }
        }

        if (isset($data['members'])) {
            foreach ($data['members'] as $accountUserData) {
                $accountUser = new AccountUser();
                $accountUser->setAccountName($account->getName());
                $accountUser->setUsername($accountUserData['user_name']);
                $accountUser->setIsOwner($accountUserData['is_owner']);
                $account->addAccountUser($accountUser);
            }
        }

        if (isset($data['properties'])) {
            foreach ($data['properties'] as $accountPropertyData) {
                $accountProperty = new AccountProperty();
                $accountProperty->setAccountName($account->getName());
                $accountProperty->setName($accountPropertyData['name']);
                $accountProperty->setValue($accountPropertyData['value']);
                $account->addAccountProperty($accountProperty);
            }
        }

        return $account;
    }

    public function getUserByUsername($username)
    {

        $data = $this->getData('/users/' . $username);
    
        if (isset($data['error'])) {
            throw new RuntimeException('User not found: ' . $username);
        }
        $user = $this->itemToUser($data);
        return $user;
    }

    public function getAccountByName($name)
    {
        $data = $this->getData('/accounts/' . $name);
        if (!isset($data['name'])) {
            return false;
        }
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
    }

    public function getData($uri, $jsonData = null)
    {
        $url =  $this->baseUrl . $uri;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);

        if ($jsonData) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        }

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

    public function checkCredentials($username, $password)
    {
        try {
            $user = $this->getUserByUsername($username);
        } catch (\Exception $e) {
            return false;
        }
        $encoder = new \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder();
        $valid = $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt());
        return $valid;
    }

    public function setAccountProperty($accountName, $propertyName, $propertyValue)
    {
        $data = $this->getData('/accounts/'.$accountName.'/setProperty/'.$propertyName.'/'.$propertyValue);
        return $data;
    }
    
    public function setAccountPicture($accountName, $filename)
    {
        $data = $this->getData('/accounts/'.$accountName.'/setPicture');
        return $data;
    }

    public function addAccountUser($accountName, $userName, $isAdmin)
    {
        $data = $this->getData('/accounts/'.$accountName.'/addUser/'.$userName.'/'.$isAdmin);
        if ($data['status'] != 'ok') {
            throw new RuntimeException("Failed to add user to account");
        }
        return true;
    }

    public function addEvent($accountName, $eventName, $data)
    {
        $data = $this->getData('/accounts/'.$accountName.'/addEvent/'.urlencode($eventName).'?'.$data);
        return $data;

    }

    public function createAccount($accountName, $accountType)
    {
        $data = $this->getData('/accounts/create/'.urlencode($accountName).'/'.urlencode($accountType));
        if (isset($data['error'])) {
            throw new RuntimeException($data['error']['code'] . ': ' . $data['error']['message']);
        }
        return true;
    }

    public function updateAccount($accountName, $properties)
    {
        $url = '/accounts/'.$accountName.'/update?x=1';
        foreach ($properties as $key => $value) {
            $url .= '&' . $key . '=' . urlencode($value);
        }
        //echo $url; exit();
        $data = $this->getData($url);
        return $data;
    }

    public function createNotification($accountName, $jsonData = null)
    {
        $data = $this->getData('/accounts/'.$accountName.'/notifications/add', $jsonData);
        return $data;
    }

    public function getNotifications($accountName, $jsonData)
    {
        $data = $this->getData('/accounts/'.$accountName.'/notifications', $jsonData);
        return $data;
    }
    
    public function setAccountPrimaryEmail($accountName, $email)
    {
        $data = $this->getData('/accounts/'.$accountName.'/defaultEmail/' . $email);
        return $data;
    }
    public function setAccountEmailVerified($accountName, $email)
    {
        $data = $this->getData('/accounts/'.$accountName.'/verifyEmail/' . $email);
        return $data;
    }
    
    public function addAccountEmail($accountName, $email)
    {
        $data = $this->getData('/accounts/'.$accountName.'/addEmail/' . $email);
        return $data;
    }
}
