<?php
/**
 * AdminBarLogo
 *
 * Логотип компании в административной панели
 *
 * @category     plugin
 * @version      2.0.0
 * @package      evo
 * @internal     @events OnManagerMenuPrerender
 * @internal     @modx_category Utilites
 * @internal     @properties &logotip=Логотип в Админ Панели;text;assets/plugins/utilites/adminbarlogo/noimage-logotip.png;assets/plugins/utilites/adminbarlogo/noimage-logotip.png;К логотипу будет применён ресайз до размера 140x40
 * @internal     @installset base
 * @internal     @disabled 0
 * @homepage     https://github.com/ProjectSoft-STUDIONIONS/DirectoryUtilites#readme
 * @license      https://github.com/ProjectSoft-STUDIONIONS/DirectoryUtilites/blob/master/LICENSE GNU General Public License v3.0 (GPL-3.0)
 * @reportissues https://github.com/ProjectSoft-STUDIONIONS/DirectoryUtilites/issues
 * @author       Чернышёв Андрей aka ProjectSoft <projectsoft2009@yandex.ru>
 * @lastupdate   2026-01-13
 */

if (!defined('MODX_BASE_PATH')):
	http_response_code(403);
	die('For');
endif;

$e = &$modx->event;
$params = $e->params;

switch ($e->name) {
	case "OnManagerMenuPrerender":
		$logotip = $modx->runSnippet('phpthumb', array(
			'input' => $params["logotip"],
			'options' => 'w=144,h=40,f=png,far=C',
			'noImage' => 'assets/plugins/utilites/adminbarlogo/noimage-logotip.png'
		));

		//$out = $logotip;
		$out = <<<EOT
<style id="adminBarLogo-style">
@media (min-width: 1200px) {
	body.light #mainMenu #nav #site::before,
	body.dark #mainMenu #nav #site::before,
	body.darkness #mainMenu #nav #site::before {
		background-image: url(/$logotip);
		background-repeat: no-repeat;
		background-size: contain;
		background-position: center center;
	}
}
li#adminBarLogo {
	display: none !important;
}
#mainMenu #nav > li:hover {
	background-color: rgba(255,255,255,0.07);
	box-shadow: 0 0 1px rgba(0, 0, 0, .7);
}
</style>
EOT;

		$menuparams = ['adminBarLogo', 'main', $out, '', '', '', '', 'main', 0, 100, ''];
		$menuparams[3] = 'javscript:;';
		$menuparams[5] = 'return false;';
		$params['menu']['adminBarLogo'] = $menuparams;

		$modx->event->output(serialize($params['menu']));
		break;
}
