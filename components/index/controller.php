<?php
/**
 * @package    MOAM.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\components\index;

defined('_EXEC') or die();

use moam\core\Framework;
if (! class_exists('Application')) {
    $application = Framework::getApplication();
}

if ($application->is_authentication()) {
    $application->redirect(PATH_WWW . "?component=home");
}

?>
<div class="content content-alt">
	<div class="container">
		<div class="row">
			<div class="">




				<div class="boxnews">

					<h4
						style="font-size: 22px; font-weight: bold; margin-bottom: 8px; color: #a93529;">News</h4>


					<table>
						<tr>
							<td>27/04/2017<br>

								<ul>
									<li>Task Manager.</li>
								</ul>

							</td>
						</tr>
						<tr>
							<td>25/04/2017<br>

								<ul>
									<li>Removido o bloqueio de execução.</li>
									<li>Núcleo reescrito.</li>
								</ul>

							</td>
						</tr>
						<tr>
							<td>17/02/2017<br>

								<ul>
									<li>Permite enviar e manter mais de uma versão de software por
										usuário.</li>
									<li>Correções de problemas no componente de gestão de Scripts.</li>
									<li>Novidades no componente de execução de scripts.</li>
								</ul>

							</td>
						</tr>
					</table>


				</div>

			</div>
		</div>
	</div>
</div>