<?php if(!defined('ALLOWED')) die('Error');
/**
 * File loader
 **/

// composer packages
require_once 'vendor/autoload.php';

// application specific configuration
require_once 'app/config/config.inc.php';
require_once 'app/config/locale.php';

// common functions
require_once 'app/common.php';

session_start();

$message = $language[$config['language']];

if (isset($_GET['module']) && isset($_GET['id'])) {
	$currentModule = 'app/' . $_GET['module'] . '.php';
	if (file_exists(__DIR__ . '/' . $currentModule)) {
		// fetch repo configuration
		$repoConfig = '../repo/' . strtolower($_GET['id']) . '.json';
		if (!file_exists($repoConfig)) {
			die('Invalid repository configuration');
		}

		$repo = json_decode(file_get_contents($repoConfig));
		
		// check deploy.json
		$deploy = $repo->staging->document_root . '/' . $config['rsync']['deploy_file'];
		if (!file_exists($deploy)) {
			writeLog('[failed] ' . $message['no_deploy']);
			exit;
		}

		require_once __DIR__ . '/' . $currentModule;

		// load template
		$tpl = __DIR__ . '/app/tpl/' . $_GET['module'] . '.tpl.php';
		if (file_exists($tpl)) {
			require_once $tpl;
		}

	}
} else {
	echo 'Thank you for using <a href="https://github.com/agoes/pull2rsync">pull2rsync</a> :)';
}
