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

// Файл теста (смотрим что в событиях)
$file = dirname(__FILE__) . "/params.txt";

// Получим путь assets из настроек сайта
$assetsPath = $modx->config['rb_base_dir'];

$e = &$modx->event;
$params = $e->params;

// Опции для filter_var
// Где минимальным значением установлено 4, а максимальное 10
$options = [
    'options' => [
        'min_range' => 4,
        'max_range' => 10,
    ]
];

// Вернёт число от 4 до 10 или false
$leftPad = filter_var($params["leftPad"], FILTER_VALIDATE_INT, $options);
// Если false, то установим дефолт в 4
$params["leftPad"] = $leftPad ? $leftPad : 4;

// Получаем права на директории из конфига сайта
$permsFolder = octdec($modx->config['new_folder_permissions']);

// Функция удаления пустых директорий
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

// Функция получения массива родителей от ребёнка
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
		$id = isset($params['new_id']) ? (int) $params['new_id'] : (isset($params['id_document']) ? (int) $params['id_document'] : (isset($params['id']) ? (int) $params['id'] : 0));
		// Получаем путь согласно дерева сайта
		$lists = array(str_pad($id, $params["leftPad"], "0", STR_PAD_LEFT));
		// Создаём директорию в директориях files, images, media
		getDocumentParent($modx, $id, $lists, $params);
		// Соберём путь, но перед этим перевернём массив
		$dir = implode('/', array_reverse($lists));

		// Создаём директории если не существуют
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
		// Запустим удаление пустых директорий в директориях images, files, media
		removeEmptyFolders($assetsPath . 'images');
		removeEmptyFolders($assetsPath . 'files');
		removeEmptyFolders($assetsPath . 'media');
		break;
}
