<?php
/**
 * pull.php
 *
 * Tiny script for doing auto pull from git repository
 *
 * @author agoes
 * @version 1.0
 * @copyright agoes, 17 March, 2015
 **/

if (function_exists('shell_exec')) {
	$cd = "cd " . $repo->staging->document_root;

	// command suffix
	$suffix = $config['command']['_suffix'];
	
	// get remote host & check git host
	$remote = trim(shell_exec($cd . " && " . $config['git']['command']['remote_url']));

	// load repository config based on GET['id']
	if (isset($_GET['id'])) {

		// pull repository
		$cmd = shell_exec($cd . " && " . $config['git']['command']['pull'] . " " . $suffix);

		// if failed to pull, write to error log
		if ($cmd !== 'Already up-to-date.' || !stristr($cmd, 'file changed') || substr($cmd, 0, 5) == 'error') {
			writeLog($cmd);
		} else {
			// succeed
			writeLog('[complete] ' . $cmd);
		}

		// set writeable directories / files
		$deploy = json_decode(file_get_contents($deploy));
		foreach($deploy->writeable as $i) {
			shell_exec($cd . " && chmod 777 " . $i);
		}
	}

	exit;
}

// shell exec is not available
writeLog($message['shell_exec_failed']);

// EOF
