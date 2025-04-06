<?php

namespace UserBase\Client;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\PasswordHasher\Hasher\MessageDigestPasswordHasher;
use UserBase\Client\Model\Account;
use UserBase\Client\Model\AccountEmail;
use UserBase\Client\Model\AccountProperty;
use UserBase\Client\Model\AccountUser;
use UserBase\Client\Model\Policy;
use UserBase\Client\Model\User;

if (!function_exists('curl_file_create')) {
    function curl_file_create($filename, $mimetype = '', $postname = '')
    {
        return "@$filename;filename="
            .($postname ?: basename($filename))
            .($mimetype ? ";type=$mimetype" : '');
    }
}

class Client
{
    protected string $baseUrl;
    protected mixed $username;
    protected mixed $password;
    protected mixed $partition;
    protected $timeDataCollector = null;
    protected CacheItemPoolInterface $cache;
    protected int $cacheDuration;

    public function __construct(
        string $url,
        ?string $username = null,
        ?string $password = null,
        string $partition = 'dev'
    ) {
        $parts = parse_url($url);

        if (isset($parts['user']) && isset($parts['pass'])) {
            $this->parse_dsn($parts);
        } else {
            $this->baseUrl = $url;
        }

        if (!isset($parts['path'])) {
            $this->baseUrl .= '/api/v1';
        }

        if ($username) {
            $this->username = $username;
        }
        if ($password) {
            $this->password = $password;
        }

        $this->partition = $partition;
        $this->cache = new ArrayAdapter();
    }

    private function parse_dsn(array $parts): void
    {
        $this->username = $parts['user'];
        $this->password = $parts['pass'];

        $this->baseUrl = "{$parts['scheme']}://{$parts['host']}";
        if (isset($parts['port'])) {
            $this->baseUrl .= ":{$parts['port']}";
        }
        if (isset($parts['path'])) {
            $this->baseUrl .= $parts['path'];
        }
    }

    public function setTimeDataCollector($timeDataCollector)
    {
        $this->timeDataCollector = $timeDataCollector;
    }

    public function setCache(CacheItemPoolInterface $cache, int $cacheDuration = 60): void
    {
        $this->cache = $cache;
        $this->cacheDuration = $cacheDuration;
    }

    private function getStatusCode($ch): int
    {
        $info = curl_getinfo($ch);

        return (int) $info['http_code'];
    }

    public function getBaseUrl(): string
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

    public function getUsersWithDetails(): array
    {
        $data = $this->getData('/users?details');
        $users = array();
        foreach ($data['items'] as $item) {
            $user = $this->itemToUser($item);
            $users[] = $user;
        }

        return $users;
    }

    protected function itemToUser($data): User
    {
        $user = new User($data['username']);
        $user->setUsername($data['username']);
        $user->setPassword($data['password']);
        $user->setCreatedAt($data['created_at']);
        $user->setDeletedAt($data['deleted_at']);
        // $user->setAccountNonLocked((int) $data['accountNonLocked']);
        $user->setAccountNonLocked(isset($data['accountNonLocked']) ? (int) $data['accountNonLocked'] : true);

        if (isset($data['accounts'])) {
            foreach ($data['accounts'] as $accountData) {
                $accountUser = new AccountUser();
                $accountUser->setUserName($user->getName());
                $accountUser->setAccountName($accountData['name']);
                $account = $this->itemToAccount($accountData);
                $accountUser->setAccount($account);
                $user->addAccountUser($accountUser);
                if ('user' == $account->getAccountType()) {
                    if ('ACTIVE' != $accountData['status']) {
                        $user->setEnabled(false);
                    }
                }
            }
        }

        if (isset($data['policies'])) {
            foreach ($data['policies'] as $policyData) {
                $policy = new Policy();
                $policy->setEffect($policyData['effect']);
                $policy->setResource($policyData['resource']);
                foreach ($policyData['action'] as $action) {
                    $policy->addAction($action);
                    if ('allow' == $policy->getEffect()) {
                        $roleName = 'ROLE_'.$policy->getResource();
                        $roleName .= '@'.$action;
                        $user->addRole($roleName);
                    }
                }
                $user->addPolicy($policy);
            }
        }

        return $user;
    }

    protected function itemToAccount(array $data): Account
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

    /**
     * @throws InvalidArgumentException
     */
    public function getUserByUsername(string $username): User
    {
        $cacheKey = 'user.'.$username.'.data';
        $cacheKey = str_replace('@', '%', $cacheKey);

        $dataCache = $this->cache->getItem($cacheKey);
        if (!$dataCache->isHit()) {
            $username = 'base64:'.base64_encode($username);
            $data = $this->getData('/users/'.$username);
            if (isset($data['error'])) {
                throw new RuntimeException('User not found: '.$username);
            }

            $dataCache->set($data);
            $dataCache->expiresAfter($this->cacheDuration);
            $this->cache->save($dataCache);
        }
        $data = $dataCache->get();

        return $this->itemToUser($data);
    }

    public function getAccountByName($name): false|Account
    {
        $data = $this->getData('/accounts/'.$name);
        if (!isset($data['name'])) {
            return false;
        }

        return $this->itemToAccount($data);
    }

    public function getAccountsWithDetails(): array
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
        if ($this->timeDataCollector) {
            $this->timeDataCollector->startMeasure('getData', $uri);
        }
        $url = $this->baseUrl.$uri;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username.':'.$this->password);

        if ($jsonData) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        }

        $json = curl_exec($ch);

        if (false === $json) {
            throw new RuntimeException('Curl error: '.curl_error($ch));
        }

        $info = curl_getinfo($ch);
        $code = $this->getStatusCode($ch);
        if ($this->timeDataCollector) {
            $this->timeDataCollector->stopMeasure('getData');
        }

        if (200 != $code) {
            throw new RuntimeException('HTTP Status code: '.$code.'message: '.$json);
        }
        $data = @json_decode($json, true);
        curl_close($ch);

        return $data;
    }

    public function checkCredentials(string $username, string $password): bool
    {
        try {
            $user = $this->getUserByUsername($username);
        } catch (\Exception $e) {
            return false;
        } catch (InvalidArgumentException $e) {
            return false;
        }

        $hasher = new MessageDigestPasswordHasher();
        return $hasher->verify($user->getPassword(), $password, $user->getSalt());
    }

    public function setAccountProperty($accountName, $propertyName, $propertyValue)
    {
        return $this->getData('/accounts/'.$accountName.'/setProperty/'.$propertyName.'/'.$propertyValue);
    }

    public function setAccountPicture($accountName, $filename)
    {
        return $this->uploadPhoto('/accounts/'.$accountName.'/setPicture', $filename);
    }

    public function addAccountUser(string $accountName, string $userName, $isAdmin): true
    {
        $data = $this->getData('/accounts/'.$accountName.'/addUser/'.$userName.'/'.$isAdmin);
        if ('ok' != $data['status']) {
            throw new RuntimeException('Failed to add user to account');
        }

        return true;
    }

    public function addEvent($accountName, $eventName, $data)
    {
        $data = $this->getData('/accounts/'.$accountName.'/addEvent/'.urlencode($eventName).'?'.$data);

        return $data;
    }

    public function createAccount(string $accountName, $accountType): true
    {
        $data = $this->getData('/accounts/create/'.urlencode($accountName).'/'.urlencode($accountType));
        if (isset($data['error'])) {
            throw new RuntimeException($data['error']['code'].': '.$data['error']['message']);
        }

        return true;
    }

    public function updateAccount(string $accountName, array $properties)
    {
        $url = '/accounts/'.$accountName.'/update?x=1';
        foreach ($properties as $key => $value) {
            $url .= '&'.$key.'='.urlencode($value);
        }
        //echo $url; exit();
        $data = $this->getData($url);

        return $data;
    }

    public function createNotification(string $accountName, $jsonData = null)
    {
        $data = $this->getData('/accounts/'.$accountName.'/notifications/add', $jsonData);

        return $data;
    }

    public function getNotifications(string $accountName, $jsonData)
    {
        $data = $this->getData('/accounts/'.$accountName.'/notifications', $jsonData);

        return $data;
    }

    public function setAccountPrimaryEmail(string $accountName, string $email)
    {
        $data = $this->getData('/accounts/'.$accountName.'/defaultEmail/'.$email);

        return $data;
    }

    public function setAccountEmailVerified(string $accountName, string $email)
    {
        $data = $this->getData('/accounts/'.$accountName.'/verifyEmail/'.$email);

        return $data;
    }

    public function addAccountEmail(string $accountName, string $email)
    {
        $data = $this->getData('/accounts/'.$accountName.'/addEmail/'.$email);

        return $data;
    }

    public function invite(string $accountName, string $displayName, string $email, $payload = null)
    {
        $data = $this->getData('/invites/create/'.$accountName.'/'.rawurlencode($displayName).'/'.rawurlencode($email).'?payload='.rawurlencode(base64_encode($payload)));

        return $data;
    }

    public function sendPushMessage(string $username, string $message, $data = [])
    {
        $data = $this->getData('/push?username='.$username.'&message='.rawurlencode($message), json_encode($data));

        return $data;
    }

    public function uploadPhoto($uri, $file)
    {
        $files['file'] = base64_decode($file);
        $boundary = uniqid();
        $delimiter = '-------------'.$boundary;

        $postData = $this->buildDataFiles($boundary, $files);

        $url = $this->baseUrl.$uri;
        $ch = curl_init();
        curl_setopt_array($ch, array(
          CURLOPT_USERPWD => $this->username.':'.$this->password,
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POST => 1,
          CURLOPT_POSTFIELDS => $postData,
          CURLOPT_HTTPHEADER => array(
            'Content-Type: multipart/form-data; boundary='.$delimiter,
            'Content-Length: '.strlen($postData),
          ),
        ));

        $json = curl_exec($ch);
        $info = curl_getinfo($ch);
        $code = $this->getStatusCode($ch);
        if ($this->timeDataCollector) {
            $this->timeDataCollector->stopMeasure('getData');
        }
        if (200 != $code) {
            throw new RuntimeException('HTTP Status code: '.$code);
        }
        $data = @json_decode($json, true);
        curl_close($ch);

        return $data;
    }

    /**
     * @return string file content and post data
     */
    public function buildDataFiles($boundary, array $files): string
    {
        $data = '';
        $eol = "\r\n";

        $delimiter = '-------------'.$boundary;

        foreach ($files as $name => $content) {
            $data .= '--'.$delimiter.$eol
                .'Content-Disposition: form-data; name="'.$name.'"; filename="'.$name.'.png"'.$eol
                .'Content-Type: image/png'.$eol
                .'Content-Transfer-Encoding: binary'.$eol
                ;

            $data .= $eol;
            $data .= $content.$eol;
        }
        $data .= '--'.$delimiter.'--'.$eol;

        return $data;
    }
}
