Userbase client for PHP
=======================

## About Userbase

Userbase is a micro-service with a REST/JSON API that manages users, organizations and api keys.

A Userbase Client can make calls to a Userbase Server authenticate users, get account details, etc.

## Usage

### Instantiate the client

Instantiate a new client object:

```php
$url = 'https://joe:secret@userbase.example.com';
$client = new Client($url);
```
The provided credentials need to have "Admin" privileges on the Userbase Server.

### Check credentials

```php
if (!$client->checkCredentials('alice', 'shhhh')) {
  exit('Invalid credentials');
}
echo 'Welcome back!';
```

## Testing/Development

The `examples/` directory contains a few example scripts that you can use during testing and development.

First, copy the `.env.dist` file to `.env`. Edit the contents to match your Userbase server and credentials.

Then you can simply execute the examples like this:

    php examples/checkcredentials.php alice sshhh

Please refer to the `examples/` directory for other examples.

## Integrations

### Silex

A Silex Provider is available [here](https://github.com/linkorb/silex-provider-userbase-client)

### Symfony 4

A Symfony 4 bundle can be found [here](https://github.com/linkorb/userbase-client-bundle)

## License

MIT. Please refer to the [license file](LICENSE) for details.

## Brought to you by the LinkORB Engineering team

<img src="http://www.linkorb.com/d/meta/tier1/images/linkorbengineering-logo.png" width="200px" /><br />
Check out our other projects at [linkorb.com/engineering](http://www.linkorb.com/engineering).

Btw, we're hiring!
