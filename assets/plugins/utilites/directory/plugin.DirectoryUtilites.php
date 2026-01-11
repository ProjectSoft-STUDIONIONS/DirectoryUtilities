<?php
/**
 * DirectoryUtilites
 *
 * Плагин Evolution CMS для работы с директориями.
 *
 * @category     plugin
 * @version      1.0.0
 * @package      evo
 * @internal     @events OnManagerLogin,OnManagerLogout,OnDocFormRender,onAfterMoveDocument,OnDocFormSave,OnDocDuplicate
 * @internal     @modx_category Manager and Admin
 * @internal     @properties &leftPad=Длина имени директории;list;4,5,6,7,8,9,10;4;4;Описание для параметра;
 * @internal     @installset base
 * @internal     @disabled 0
 * @homepage     https://github.com/ProjectSoft-STUDIONIONS/DirectoryUtilites#readme
 * @license      https://github.com/ProjectSoft-STUDIONIONS/DirectoryUtilites/blob/master/LICENSE GNU General Public License v3.0 (GPL-3.0)
 * @reportissues https://github.com/ProjectSoft-STUDIONIONS/DirectoryUtilites/issues
 * @author       Чернышёв Андрей aka ProjectSoft <projectsoft2009@yandex.ru>
 * @lastupdate   2026-01-12
 */

if (!defined('MODX_BASE_PATH')):
	http_response_code(403);
	die('For');
endif;

$e = &$modx->event;
$params = $e->params;

$params["leftPad"] = $params["leftPad"] ? (int) $params["leftPad"] : 4;
$params["leftPad"] = $params["leftPad"] > 4 ? $params["leftPad"] : 4;

$permsFolder = octdec($modx->config['new_folder_permissions']);

// Файл теста (смотрим что в событиях)
$file = dirname(__FILE__) . "/params.txt";

if(!function_exists('removeEmptyFolders')):
	function removeEmptyFolders($path){
		$isFolderEmpty = true;
		$pathForGlob = (substr($path, -1) == "/") ? $path . "*" : $pathForGlob = $path . DIRECTORY_SEPARATOR . "*";
		// смотрим что есть внутри раздела
		foreach (glob($pathForGlob) as $file):
			if (is_dir($file)):
				if (!removeEmptyFolders($file)):
					$isFolderEmpty = false;
				endif;
			else:
				$isFolderEmpty = false;
			endif;
		endforeach;
		if ($isFolderEmpty):
			@rmdir($path);
		endif;
		return $isFolderEmpty;
	}
endif;

if(!function_exists('getDocumentParent')):
	function getDocumentParent(\DocumentParser $modx, $id, &$lists, $params)
	{
		$table_content = $modx->getFullTableName('site_content');
		$parent = $modx->db->getValue($modx->db->select('parent', $table_content, "id='{$id}'"));
		if($parent):
			$lists[] = str_pad($parent, $params["leftPad"], "0", STR_PAD_LEFT);
			getDocumentParent($modx, $parent, $lists, $params);
		endif;
	}
endif;

switch ($e->name) {
	// Создание директорий
	case "OnDocFormRender":
	case "onAfterMoveDocument":
	case "OnDocFormSave":
	case "OnDocDuplicate":
		/**
		 * id - id документа
		 * OnDocFormRender
			Array
			(
			    [id] => 1
			    [template] => 3
			    [leftPad] => 4
			)
		 *
		 * OnDocFormSave
			Array
			(
			    [id] => 1
			    [mode] => upd
			    [leftPad] => 4
			)
		 *
		 * id_document - id документа
		 * onAfterMoveDocument
			Array
			(
			    [id_document] => 1
			    [old_parent] => 0
			    [new_parent] => 2
			    [leftPad] => 4
			)
		 *
		 * new_id - id документа
		 * OnDocDuplicate
			Array
			(
			    [id] => 2
			    [new_id] => 3
			    [leftPad] => 4
			)
		 */
		// Получаем id документа
		$id = $params['new_id'] ? (int) $params['new_id'] : ($params['id_document'] ? (int) $params['id_document'] : ($params['id'] ? (int) $params['id'] : 0));
		// Получаем путь согласно дерева сайта
		$lists = array(str_pad($id, $params["leftPad"], "0", STR_PAD_LEFT));
		// Создаём директорию в директориях files, images, media
		getDocumentParent($modx, $id, $lists, $params);
		$dir = implode('/', array_reverse($lists));

		$assetsPath = $modx->config['rb_base_dir'];

		if(!is_dir($assetsPath."files/".$dir)):
			@mkdir($assetsPath."files/".$dir, $permsFolder, true);
		endif;
		if(!is_dir($assetsPath."images/".$dir)):
			@mkdir($assetsPath."images/".$dir, $permsFolder, true);
		endif;
		if(!is_dir($assetsPath."media/".$dir)):
			@mkdir($assetsPath."media/".$dir, $permsFolder, true);
		endif;
		break;
	// Удаление пустых директорий при входе/выходе
	case "OnManagerLogin":
	case "OnManagerLogout":
		// Запустим для директорий images, files, media
		$assetsPath = $modx->config['rb_base_dir'];
		removeEmptyFolders($assetsPath . 'images');
		removeEmptyFolders($assetsPath . 'files');
		removeEmptyFolders($assetsPath . 'media');
		break;
}
