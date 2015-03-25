# pull2rsync

Simple php script for deployment workflow between your local workspace, staging environment to production environment. This script will do  `git pull` automatically in your staging environment when you push something into your repository. And you can also do `rsync` from this script too.

### Dependencies


----------


 - [PHPMailer](https://github.com/PHPMailer/PHPMailer) (already added in [composer.json](composer.json))

### Installing


----------


Make sure you run Linux with `git` and `rsync` command availabe. For web server, I recommend you to using [Apache](http://apache.org/) or [nginx](http://nginx.org) with PHP enabled.

 1. Put the script inside your document root. I recommend you to create a virtual host and set its document root to `/path/to/document/root/of/pull2rsync/public`
 2. Set configuration file at `pull2rsync/app/config.inc.php`. And make sure you read all the comment block there. And all the configuration is **mandatory**
 3. Install [PHPMailer](https://github.com/PHPMailer/PHPMailer) by using composer by typing `composer update`. And of course, you have to install `composer` first.
 4. If token and log path already set in config.inc.php, don't forget to create the directories and set to writeable (`chmod 777 /path/to/token /path/to/log`)
 5. If all the steps above is done, visit http://your-pull2srync-host/index.php. There should be a message there :)
