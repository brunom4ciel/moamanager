<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\settings;

defined('_EXEC') or die();

use moam\core\Framework;
use moam\core\AppException;
if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if (! $application->is_authentication()) {
    $application->alert("Error: you do not have credentials.");
}

function runExternal($cmd)
{
    $descriptorspec = array(
        0 => array(
            "pipe",
            "r"
        ), // stdin is a pipe that the child will read from
        1 => array(
            "pipe",
            "w"
        ), // stdout is a pipe that the child will write to
        2 => array(
            "pipe",
            "w"
        ) // stderr is a file to write to
    );

    $pipes = array();
    $process = proc_open($cmd, $descriptorspec, $pipes);

    $output = "";

    if (! is_resource($process))
        return false;

    // close child's input imidiately
    fclose($pipes[0]);

    stream_set_blocking($pipes[1], false);
    stream_set_blocking($pipes[2], false);

    $todo = array(
        $pipes[1],
        $pipes[2]
    );

    while (true) {
        $read = array();

        if (! feof($pipes[1]))
            $read[] = $pipes[1];
        if (! feof($pipes[2]))
            $read[] = $pipes[2];

        if (! $read)
            break;

        $ready = @stream_select($read, $write = NULL, $ex = NULL, 2);

        if ($ready === false) {
            break; // should never happen - something died
        }

        foreach ($read as $r) {
            $s = fread($r, 1024);
            $output .= $s;
        }
    }

    fclose($pipes[1]);
    fclose($pipes[2]);

    $code = proc_close($process);

    return array(
        "output" => $output,
        "code" => $code
    );
}

$result_cmd = "";

$cmd = $application->getParameter("cmd");

if (isset($_POST['cmd'])) {

    try {

        $result = runExternal($cmd);

        $result_cmd = $result["output"];
    } catch (AppException $e) {

        throw new AppException($e->getMessage());
    }
}

?>



<div class="content content-alt">
	<div class="container" style="width: 70%">
		<div class="row">
			<div class="">
				<div class="card" style="width: 100%">



					<div class="page-header">
						<h1>Command Line</h1>
					</div>



					<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>"
						name="loginForm" enctype="multipart/form-data">
						<input type="hidden"
							value="<?php echo $application->getComponent()?>"
							name="component"> <input type="hidden"
							value="<?php echo $application->getController()?>"
							name="controller"> <input type="hidden"
							value="<?php echo $application->getParameter("task")?>"
							name="task">

						<textarea id="data" style="width: 100%; height: 400px;" name="cmd"><?php echo $cmd?></textarea>
						<br>

						<div style="text-align: right; display: block;">

							<input type="submit" name="Execute" value="Execute" /> <input
								type="button"
								onclick="javascript: window.location.href='?component=settings';"
								name="cancel" value="Cancel" />

						</div>

					</form>
							
							
							<?php

    if (! empty($result_cmd)) {

        echo "<pre style='  display:block;
								
  width:100%;
  top:20px;
  left:0;
  font-size: 12px;
  padding:5px;
  border:1px solid #999;
  background:#000;
  color:#fff;'>" . $result_cmd . "</pre>";
    }

    ?>
							
							</div>

			</div>
		</div>
	</div>
</div>
</div>

