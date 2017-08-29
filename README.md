# Composer Installers for WordPress
This is a simple project to allow for easier support of WordPress-related paths when using [Composer](http://getcomposer.org). 


## Usage

### Project Packages _(e.g. WordPress sites)_
You can adjust the directories or paths using any of the `wordpress-*-install-dir` or `wordpress-*-path` properties
listed below by including them in the top-level `extra` property of your `composer.json` file.
```
"extra": {
    "wordpress-webroot-path":     "web",
    "wordpress-content-path":     "components",
    "wordpress-core-install-dir": "web/wordpress"
}
```
The project package defined in the above `composer.json` will create a directory layout like the following:

|Package Type:|Install directory:
|------------|-----------------|
|`wordpress-core`|`web/wordpress/`|
|`wordpress-theme`|`web/components/themes/{$name}/`|
|`wordpress-plugin`|`web/components/plugins/{$name}/`|
|`wordpress-muplugin`|`web/components/mu-plugins/{$name}/`|

### Component Packages _(e.g. plugins, themes, etc.)_
When defining component packages _(such as of the types `wordpress-core`, `wordpress-theme`, `wordpress-plugin`, `wordpress-muplugin` and/or `wordpress-devops-core`)_
be sure to include a `composer.json` in the root of your component's Git repository that contains the following type of information:

```
{
    "name": "your-org/your-plugin",
    "description": "Your Plugin Description",
    "type": "wordpress-plugin",
    "minimum-stability":"stable",
    "require": {
        "wplib/wp-composer-installers": ">=1.0"
    }
}
```

The component package defined in the above `composer.json` will be installed in `www/content/plugins/your-plugin` by default.

## Reference
### Package Types

_"Composer Installers for WordPress" (wp-composer-installers)_ defines the following types of Composer Packages:

| Package Type |Description |
|--------------|-------------|
|`wordpress-core`| [WordPress core](https://wordpress.org/download/release-archive/) itself.<br><br>**Note** the standard WordPress directory layout with WordPress in the web root<br>cannot be supported by Composer so you will need to place it somewhere else,<br>such as defined by the [WordPress Skeleon](https://markjaquith.wordpress.com/2012/05/26/wordpress-skeleton/) which uses`wp/` _(this is our default)_.
|`wordpress-theme`|[WordPress themes](https://wordpress.org/themes/) that you want Composer to install.
|`wordpress-plugin`|[WordPress plugins](https://wordpress.org/plugins/) you want Composer to install in the standard plugins<br>directory.
|`wordpress-muplugin`|[WordPress plugins](https://wordpress.org/plugins/) you want Composer to install in the **must-use** plugins<br>directory.
|`wordpress-devops-core`|A special package type for [WP DevOps](https://github.com/wplib/wp-devops) which includes scripts and config<br>files for building, testing and deploying WordPress projects on various<br>continuous integration services.

 Or you may prefer another well-known configuration such as  which uses `app/`

### Install Directory Properties for `composer.extra`
The following are properties that _"Composer Installers for WordPress"_ will recognize if found in `composer.json`:

|Package Type:| Property that sets the directory:|Default directory:
|--------------|--------------|-------------|
|`wordpress-core`|`wordpress-core-install-dir`|`www/wp/`|
|`wordpress-theme`|`wordpress-theme-install-dir`|`www/content/themes/{$name}/`|
|`wordpress-plugin`|`wordpress-plugin-install-dir`|`www/content/plugins/{$name}/`|
|`wordpress-muplugin`|`wordpress-muplugin-install-dir`|`www/content/mu-plugins/{$name}/`|
|`wordpress-devops-core`|`wordpress-devops-core-install-dir`|`devops/core/`|

If, for example, you wantyour WordPress core installed in `www/wordpress` you would set your `extra` property in your`composer.json` to include the following:
```
"extra": {
    "wordpress-core-install-dir": "www/wordpress/"
}
```

### Relative Path Properties for `composer.extra`

These additional properties allow you to fine tune the install directories:

| Property Name:|Default Relative Path Segment:|Affects these Properties:
|--------------|--------------|-------------|
|`wordpress-webroot-path`|`www/`|`wordpress-core-install-dir`,<br>`wordpress-theme-install-dir`,<br>`wordpress-plugin-install-dir`, and<br>`wordpress-muplugin-install-dir`
|`wordpress-core-path`|`{webroot}wp/`|`wordpress-core-install-dir`
|`wordpress-content-path`|`{webroot}content/`|`wordpress-theme-install-dir`,<br>`wordpress-plugin-install-dir`, and<br>`wordpress-muplugin-install-dir`

So, for example, if you want your projects to use [Bedrock](https://roots.io/bedrock/), set your `extra` property in your`composer.json` to include the following:
```
"extra": {
    "wordpress-webroot-path": "web/",
    "wordpress-content-path": "app/"
}
```

If this is not clear it might be easier to understand by [**viewing the source code**](https://github.com/wplib/wp-composer-installers/blob/master/src/WordPressRelatedInstallers.php). Look at how the `protected` property `$locations` is initialized.

### About Trailing Slashes
When specifying directories or paths you may choose to add trailing slashes or omit them, your choice.
Either way _"Composer Installers for WordPress"_ handles them correctly so the following is valid:

```
"extra": {
    "wordpress-webroot-path": "wp/",
    "wordpress-content-path": "content"
}
```

## Compatibility
_"Composer Installers for WordPress"_ is intended to supercede the need for the Composer installers listed below for WordPress
projects but its goal is to be compatible with the package types defined in these installers even if the default
directories differ:

- [Composer Installers](https://github.com/composer/installers)
- [WordPress Core Installer](https://github.com/johnpbloch/wordpress-core-installer)



## License
This is licensed using [GPL version 3](https://www.gnu.org/licenses/gpl-3.0.en.html).
