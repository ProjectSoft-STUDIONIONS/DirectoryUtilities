<?php
/**
 * RenderParams
 *
 * Рендер параметров модуля, снипета, плагина
 *
 * @category     plugin
 * @version      2.1.2
 * @package      evo
 * @internal     @events OnPluginFormPrerender,OnSnipFormPrerender,OnModFormPrerender
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

global $_lang;
global $which_browser;
$output = "";
switch ($e->name) {
	case 'OnPluginFormPrerender':
		include dirname(__FILE__) . "/render.plugin.script.php";
		$modx->event->output($output);
		break;
	case 'OnSnipFormPrerender':
		include dirname(__FILE__) . "/render.snipet.script.php";
		$modx->event->output($output);
		break;
	case 'OnModFormPrerender':
		include dirname(__FILE__) . "/render.module.script.php";
		$modx->event->output($output);
		break;
}
