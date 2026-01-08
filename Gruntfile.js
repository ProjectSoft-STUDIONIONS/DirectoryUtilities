module.exports = function(grunt) {
	var fs = require('fs'),
		PACK = grunt.file.readJSON('package.json'),
		COPYPACK = {
			dest: false
		},
		path = require('path'),
		date = new  Date(),
		year = date.getFullYear(),
		month = String(date.getMonth() + 1).padStart(2, "0"),
		day = String(date.getDate()).padStart(2, "0"),
		replacement = `/**
 * ${PACK.title}
 *
 * Плагин Evolution CMS для работы с директориями.
 *
 * @category     plugin
 * @version      ${PACK.version}
 * @package      evo
 * @internal     @events OnManagerLogin,OnManagerLogout,OnDocFormRender,onAfterMoveDocument,OnDocFormSave,OnDocDuplicate
 * @internal     @modx_category Manager and Admin
 * @internal     @properties &leftPad=Длина имени директории;list;4,5,6,7,8,9,10;4;4;Описание для параметра;
 * @internal     @installset base
 * @internal     @disabled 0
 * @homepage     ${PACK.homepage}#readme
 * @license      ${PACK.homepage}/blob/master/LICENSE GNU General Public License v3.0 (GPL-3.0)
 * @reportissues ${PACK.homepage}/issues
 * @author       ${PACK.author}
 * @lastupdate   ${year}-${month}-${day}
 */`;
	require('load-grunt-tasks')(grunt);
	require('time-grunt')(grunt);
	if(fs.existsSync('copypack.json')) {
		COPYPACK = grunt.file.readJSON('copypack.json');
	}

	grunt.initConfig({
		globalConfig : {},
		pkg : PACK,
		clean: {
			zip: ['*.zip']
		},
		'string-replace': {
			tpl: {
				files: {
					'install/assets/plugins/': 'install/assets/plugins/**',
				},
				options: {
					replacements: [
						{
							pattern: /(\/\*(?:[^*]|[\s]|(\*+(?:[^*/]|[\s])))*\*+\/)/g,
							replacement: replacement,
						},
					],
				},
			},
			php: {
				files: {
					'assets/plugins/utilites/directory/': 'assets/plugins/utilites/directory/**',
				},
				options: {
					replacements: [
						{
							pattern: /(\/\*(?:[^*]|[\s]|(\*+(?:[^*/]|[\s])))*\*+\/)/,
							replacement: replacement,
						},
					],
				},
			},
		},
		pug: {
			main: {
				options: {
					doctype: 'html',
					client: false,
					pretty: '',
					separator:  '',
					data: function(dest, src) {
						return {
							"pagetitle":     PACK.title,
							"description":   PACK.description,
							"keywords":      PACK.keywords,
						}
					},
				},
				files: [
					{
						expand: true,
						cwd: __dirname + '/src/pug/',
						src: [ 'index.pug' ],
						dest: __dirname + '/docs/',
						ext: '.html',
					},
				],
			},
		},
		copy: {
			main: {
				files: [
					{
						expand: true,
						cwd: 'assets/plugins',
						src: ['**/*.*'],
						dest: COPYPACK.dest ? COPYPACK.dest : __dirname + "/test/",
					},
				],
			},
		},
		compress: {
			main: {
				options: {
					archive: 'DirectoryUtilites.zip',
				},
				files: [
					{
						src: ['assets/**', 'install/**'],
						dest: 'DirectoryUtilites/',
					},
				],
			},
		},
	});
	grunt.registerTask('default', [
		'clean',
		'string-replace',
		'pug',
		'copy',
		'compress'
	]);
}
