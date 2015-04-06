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
	
	// command suffix
	$suffix = $config['command']['_suffix'];

	if (!file_exists($repo->staging->document_root)) {
		// clone
		$clone = shell_exec($config['git']['command']['clone'] . " " . $repo->git . " " . $repo->staging->document_root . " " . $suffix);
		writeLog($clone);
	} else {
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
		}
	}

	exit;
}

// shell exec is not available
writeLog($message['shell_exec_failed']);

// EOF
