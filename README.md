# pull2rsync
Simple php script for deployment workflow between your local workspace, staging environment to production environment. This script will do  `git pull` automatically in your staging environment when you push something into your repository. And you can also do `rsync` from this script too.

### Dependencies
 - [PHPMailer](https://github.com/PHPMailer/PHPMailer) (already added in [composer.json](composer.json))

### Installing
Make sure you run Linux with `git` and `rsync` command availabe. For web server, I recommend you to using [Apache](http://apache.org/) or [nginx](http://nginx.org) with PHP enabled.

 1. Put the script inside your document root. I recommend you to create a virtual host and set its document root to `/path/to/document/root/of/pull2rsync/public`.
 2. Set configuration file at `pull2rsync/app/config.inc.php`. And make sure you read all the comment block there. And all the configuration is **mandatory**.
 3. Install [PHPMailer](https://github.com/PHPMailer/PHPMailer) by using composer by typing `composer update`. And of course, you have to install `composer` first.
 4. If token and log path already set in config.inc.php, don't forget to create the directories and set to writeable (`chmod 777 /path/to/token /path/to/log`).
 5. If all the steps above is done, visit http://your-pull2srync-host/index.php. There should be a message there :)

### Add webhook (push event)
Let say you have to start a project named "My awesome website". So this is what you have to do :

 1. Create a new git repository inside GitLab/BitBucket/Github or other git repository management that has webhooks feature after git push.
 2. Add deploy keys for your git repository for your staging server. For more detailed information please check url below :
  * GitHub : https://developer.github.com/guides/managing-deploy-keys/
  * GitLab : http://doc.gitlab.com/ce/ssh/README.html
  * BitBucket : https://confluence.atlassian.com/display/BITBUCKET/Use+deployment+keys
 3. Create repository configuration file  `/repo/your-awesome-website.json` inside your pull2rsync installation directory. That json file will contains all information about git ssh url, document root (staging and production) and authorized users of the project. Please check at [repo/example.json](repo/example.json) for more detailed information. If you want to create more another project you have to create another repo configuration file.
 4. Add http://your-pull2rsync-host/index.php?module=pull&id=**your-awesome-website** as your webhook url of your repository.
 5. Test your webhook url, and normally if the staging directory is not already exists, it will automatically clone the repository into it.

### Deploy to production
1. Open your web browser and access this url : http://your-pull2rsync-host/index.php?module=rsync&id=**your-awesome-website**. It will show some new / modified directory or file by running rsync in dry run mode.
2. Run request token if you want to rsync those changes to production server.
3. Pull2rsync will send an email contains the token number for your request. Normally, email will be sent to administrator or lead-developer (check [app/config.inc.php](app/config.inc.php))
4. If you had the token, paste it into click "validate token".
5. If rsync was succeeded, notification will be sent to all project member. 

