# Mailer Package
Simple mailer for [Mako Framework](https://github.com/mako-framework/framework).

## Installing
1. Unzip package into a packages directory within your application's root directory.  
2. Add dependency in `composer.json` file:
```json
"require": {
	"php": ">=7.1.3",
	"mako/framework": "5.7.*",
	"packages/mailer": "*"
}
```
And add local repository path:
```json
"repositories": [
    {
        "type": "path",
        "url": "packages/mailer",
        "options": {
            "symlink": true
        }
    }
]
```
3. Rur `composer install` or `composer update` command in console terminal.
