<?php if(!defined('ALLOWED')) die('Error');
/**
 * Configuration file
 *
 * @author agoes
 * @version 1.0
 * @copyright agoes, 17 March, 2015
 **/


/*
 * rsync configuration
 * ========================================================
 */

// excluded by default when doing rsync
$config['rsync']['exclude'] = array(
	'.git',
	'.gitignore',
	'.svn',
	'.Trash',
	'.DS_Store',
	'composer.json',
	'bower.json',
	'license.txt',
	'readme.txt',
	'__MACOSX',
	'deploy.json',
	'README.md'
);

// token expiration when doing rsync (time in seconds)
$config['rsync']['token_expire'] = 900;

// token path (whithout trailing slash) and make sure it is writeable
$config['rsync']['token_path'] = '/path/to/token/directory';

// deploy config filename
$config['rsync']['deploy_file'] = 'deploy-example.json';

// is rsync path and host need to be shown?
$config['rsync']['hide_information'] = TRUE;

// token will be sent to the user group below
$config['rsync']['token_role'] = array('administrator', 'lead-developer');

/*
 * end of rsync configuration
 * ========================================================
 */


/*
 * git configuration
 * ========================================================
 */

// git binary path
$config['git']['path'] = '/usr/bin/git';

// git commands
$config['git']['command'] = array(
	'remote_url' => $config['git']['path'] . ' ls-remote --get-url',
	'pull' => $config['git']['path'] . ' pull',
	'clone' => $config['git']['path'] . ' clone',
	'branch' => $config['git']['path'] . ' rev-parse --abbrev-ref --symbolic-full-name @{u}',
	'fetch_all' => $config['git']['path'] . ' fetch --all',
	'hard_reset' => $config['git']['path'] . ' reset --hard origin/',
);

// will pull/clone another branch (except master) into this directory
$config['git']['branch_dir'] = 'branch_dir';

/*
 * end of git configuration
 * ========================================================
 */


/*
 * email configuration
 * ========================================================
 */

// smtp
$config['smtp'] = array(
	'host' => '',
	'port' => '',
	'username' => '',
	'password' => '',
	'secure' => '',
	'from' => '',
	'from_name' => 'pull2rsync',
);

/*
 * end of email configuration
 * ========================================================
 */


/*
 * log configuration
 * ========================================================
 */

// activity log (without trailing slash)
$config['log']['path'] = '/path/to/log/directory';

/*
 * end of log configuration
 * ========================================================
 */

// locale setting
$config['language'] = 'en';

// default timezone 
date_default_timezone_set('Asia/Jakarta');

// command suffix
$config['command']['_suffix'] = '2>&1';

// EOF

