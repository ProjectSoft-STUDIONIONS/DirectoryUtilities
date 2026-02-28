<?php
/**
 * EvoTreeManager
 *
 * Добавление класса к body для починки отображения трея.
 *
 * @category     plugin
 * @version      2.1.2
 * @package      evo
 * @internal     @events OnManagerTreeInit
 * @internal     @modx_category Utilites
 * @internal     @installset base
 * @internal     @disabled 0
 * @homepage     https://github.com/ProjectSoft-STUDIONIONS/EvolutionCMS-Utilites#readme
 * @license      https://github.com/ProjectSoft-STUDIONIONS/EvolutionCMS-Utilites/blob/master/LICENSE GNU General Public License v3.0 (GPL-3.0)
 * @reportissues https://github.com/ProjectSoft-STUDIONIONS/EvolutionCMS-Utilites/issues
 * @author       Чернышёв Андрей aka ProjectSoft <projectsoft2009@yandex.ru>
 * @lastupdate   2026-03-01
 */

if (!defined('MODX_BASE_PATH')):
	http_response_code(403);
	die('For');
endif;

$e = &$modx->event;
$params = $e->params;

switch ($e->name) {
	// Trigger reloading tree for relevant actions
	case 'OnManagerTreeInit':
		$output = <<<EOD
<script>document.body.classList.add('ElementsInTree');</script>
<style>.ElementsInTree #tree .actionButtons--eit{top:0!important;}</style>
EOD;
		$modx->event->output($output);
		break;
}
