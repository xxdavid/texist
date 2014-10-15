Texist
=====
Texist is a [Texy!](http://texy.info) live editor with [Dropbox](http://dropbox.com) integration. It has also a public interface for viewing notes so it's perfect for sharing school notes.

## Requirements
 - PHP 5.4+
 -  MySQL database
 - Dropbox app (instructions in [Set Dropbox app keys](#user-content-set-dropbox-app-keys) section)


## Installation

Installation is little bit non-standard but it's the only solution I found to keep updating and customizing simple.

### 1. Require package via [Composer](https://getcomposer.org)
```
composer require xxdavid/texist:dev-master
```

You may also have to set [`minimum-stability`](https://getcomposer.org/doc/04-schema.md#minimum-stability) to `dev`.

### 2. Create file to handle requests
Create `index.php` in the root of your project folder with following content:

```php
<?php
require_once __DIR__ . 'vendor/autoload.php';

$texist = new Texist();

//Configuration here

$texist->run();
```
Configuration will be described [below](#user-content-configuration).

### 3. Rewrite requests to index.php
#### Apache (.htaccess)
```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*\.(js|ico|gif|jpg|png|css))$ vendor/xxdavid/texist/www/$1 [L]
RewriteRule !(.*\.(js|ico|gif|jpg|png|css))$ index.php [L]
```

#### Nginx
```
location / {
    if (!-e $request_filename) {
        rewrite ^/(.*\.(js|ico|gif|jpg|png|css))$ /vendor/xxdavid/texist/www/$1 break;
    }
        rewrite ^ /index.php last;
}
```

## Configuration
### Required settings
#### Set Dropbox app keys
If you don't have registered Dropbox app, you can do this [here](https://www.dropbox.com/developers/apps/create) (Dropbox API app -> Files and datastores -> My app only needs access to files it creates).

```php
$texist->setDropboxAppKeys('appKey', 'appSecret');
```

#### Set Database
Database is only used for storing Dropbox app keys and informations about user. Tables will be automatically created before first signing in.

```php
 $texist->setDatabase('host', 'dbname', 'username', 'password');
```

#### Set Temporary Directory
This directory is used by [Latte](http://latte.nette.org/en/) for caching templates.

```php
$texist->setTempDirectory(__DIR__ . '/temp/');
```

### Optional Settings
#### Set Logging Directory
```php
$texist->setLogDirectory(__DIR__ . '/log/');
```

#### Set Email
This email will be used for sending notifications of errors on the production server.

```php
$texist->setEmail('john@doe.com');
```

#### Set [Debug Mode](http://doc.nette.org/en/2.2/configuring#toc-development-mode)
This is a little bit complicated.
Syntax is:

```php
$texist->setDebugMode($value);
```
And possible values are: 

 - array of IPs (IP) (visitor from this IPs will see Tracy -- Debug bar, verbose exceptions etc.)
 - string with IPs (IP) separated by comma (visitor from this IP will see Tracy)
 - true (force debug mode)
 - false (force production mode)
 - null (detect automatically)

By default it's detected automatically.

#### Set custom Texy Wrapper
By default Texy library is used as is without any configuration. That probably isn't what you want.  Therefore you can write your own Texy Wrapper. You can inspire yourself in the [default Texy Wrapper](app/model/TexyWrapper.php). There are only two rules your class have to comply: 

 1. Implement Texist\ITexyWrapper
 2. Have `process` function (that's result of the first rule)

That means you can even use Markdown for rendering. But why would you do that when there's a better alternative.

So your Texy Wrapper could look like this:
```php
<?php

use Texy\Texy,
    Texist\ITexyWrapper,
    Texy\Modules\HeadingModule;

class MyTexyWrapper implements ITexyWrapper
{
    /** @var  Texy */
    private $texy;

    public function __construct(Texy $texy)
    {
        $this->texy = $texy;
        $this->texy->headingModule->top = 2;
        $this->texy->headingModule->balancing = HeadingModule::FIXED;
    }

    public function process($text)
    {
        return $this->texy->process($text);
    }
}
```
Notice the injecting of Texy library in the constructor. You can use it because this class will be loaded by Nette.

Next you have to load that file and call `setTexyWrapper()` with the name of your Texy Wrapper class.

```php
include 'MyTexyWrapper.php';
$texist->setTexyWrapper('MyTexyWrapper');
```

### First sign in
If you now go to the base URL of your project, it will throw an error. So go to `/sign/in` . There you should see Dropbox permissions request. Grant it. **And you're done.** Enjoy your writing.

## License: MIT
See [LICENSE](LICENSE).
