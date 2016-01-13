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

	// get branch
	$json = json_decode(file_get_contents("php://input"));
	if (!isset($json->ref)) {
		writeLog($message['webhook_failed']);
		exit;
	} else {
		$branch = str_replace('refs/heads/', '', $json->ref);
		$branch_dir = '/' . $config['git']['branch_dir'] . '/' . $branch;

		// dont create subdirectory for master
		if ($branch == 'master') {
			$branch_dir = '/';
		}
	}

	if (!file_exists($repo->staging->document_root . $branch_dir)) {
		// clone
		$clone = shell_exec($config['git']['command']['clone'] . " -b " . $branch . " " . $repo->git . " " . $repo->staging->document_root . $branch_dir . " " . $suffix);
		writeLog('Clone : ' . $clone);
	} else {

		// load repository config based on GET['id']
		if (isset($_GET['id'])) {

			// pull repository
			$init_cmd = $cd . "/" . $branch_dir . " && ";
			$cmd = shell_exec($init_cmd . $config['git']['command']['pull'] . " " . $suffix);
			writeLog('Pull : ' . $branch . $cmd);

			// pull failed !!
			if (stristr($cmd, 'error:')) {
				$cmd = shell_exec($init_cmd . $config['git']['command']['fetch_all'] . " " . $suffix);
				$cmd = shell_exec($init_cmd . $config['git']['command']['hard_reset'] . . $branch . " " . $suffix);
				writeLog('Reset : ' . $cmd);
			}
		}
	}

	exit;
}

// shell exec is not available
writeLog($message['shell_exec_failed']);

// EOF
