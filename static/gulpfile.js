//*********** IMPORTS *****************
var config = require('config');
var request = require('request');
var fs = require('fs');
var gulp = require('gulp');
var sass = require('gulp-ruby-sass');
var rename = require("gulp-rename");
var map = require("map-stream");
var concat = require("gulp-concat");
var uglify = require('gulp-uglify');
var watch = require('gulp-watch');
var minifycss = require('gulp-minify-css');
var eol = require('gulp-eol');
var fileinclude = require('gulp-file-include');
var replace = require('gulp-replace');
var languages = ['ru', 'en'];
var ts = Math.floor(Math.random() * 10000);

var sourcePath = './';
var prodPath = './build/assets/';
var publicPath = './build/';

gulp.task('app.html', function () {

	compile('ru');
	compile('ru', true);

	compile('en');
	compile('en', true);

	compileTablet();

	function compileTablet(){

		gulp.src(sourcePath + 'templates/tablet.html')
			.pipe(replace(/T_LANG/g, JSON.stringify(mergeLang('ru'))))
			.pipe(fileinclude({
				prefix: '@@',
				basepath: '@file'
			}))
			.pipe(replace(/@@ts/g, ts))
			.pipe(replace(/@@lang/g, 'ru'))
			.pipe(gulp.dest(publicPath));

	}

	function compile(lang, test) {

		test = test || false;

		var htmlFiles, translate, name, postfix;

		if (test) {
			postfix = '_test.html';
		} else {
			postfix = '.html';
		}

		htmlFiles = [
			sourcePath + 'templates/index' + postfix
		];

		if (lang == 'ru') {
			name = 'index' + postfix;
		} else if (lang == 'en') {
			name = lang + postfix;
		}

		translate = mergeLang(lang);

		gulp.src(htmlFiles)
			.pipe(replace(/T_LANG/g, JSON.stringify(translate)))
			.pipe(fileinclude({
				prefix: '@@',
				basepath: '@file'
			}))
			.pipe(replace(/@@ts/g, ts))
			.pipe(replace(/@@lang/g, lang))
			.pipe(rename(name))
			.pipe(gulp.dest(publicPath));

	}

});

gulp.task('app.css', function () {

	var cssFiles = [
		sourcePath + 'css/lib/bootstrap.css',
		sourcePath + 'css/project/style.css',
		sourcePath + 'css/project/style.over.css'
	];

	gulp.src(cssFiles)
		.pipe(minifycss())
		.pipe(concat('app.css'))
		.pipe(rename({suffix: '.min'}))
		.pipe(eol("\n"))
		.pipe(gulp.dest(prodPath + 'css'));

	gulp.src(sourcePath + 'css/project/ie.css')
		.pipe(minifycss())
		.pipe(concat('ie.css'))
		.pipe(rename({suffix: '.min'}))
		.pipe(eol("\n"))
		.pipe(gulp.dest(prodPath + 'css'));

	gulp.src(sourcePath + 'css/project/tablet.css')
		.pipe(minifycss())
		.pipe(concat('tablet.css'))
		.pipe(rename({suffix: '.min'}))
		.pipe(eol("\n"))
		.pipe(gulp.dest(prodPath + 'css'));

});


gulp.task('app.js', function () {

	var srcFiles = [
		sourcePath + 'js/lib/jquery.js',
		sourcePath + 'js/lib/bootstrap.js',
		sourcePath + 'js/lib/jquery.cookie.js',
		sourcePath + 'js/lib/jquery.plugins.js'
	];

	var appFiles = [
		sourcePath + 'js/project/define.js',
		sourcePath + 'js/project/inc/promo.js',
		sourcePath + 'js/project/inc/signUp.js',
		sourcePath + 'js/project/inc/reSubmitSms.js',
		sourcePath + 'js/project/inc/register.js',
		sourcePath + 'js/project/inc/passwordReset.js',
		sourcePath + 'js/project/inc/autoLogin.js',
		sourcePath + 'js/project/inc/feedback.js',
		sourcePath + 'js/project/inc/signupLogic.js',
		sourcePath + 'build/assets/js/order-form.js',
		sourcePath + 'js/project/jquery.main.js',
		sourcePath + 'js/project/functions.js',
		sourcePath + 'js/project/inner.js'
	];

	gulp.src(srcFiles)
		.pipe(uglify())
		.pipe(concat('src.js'))
		.pipe(rename({suffix: '.min'}))
		.pipe(eol("\n"))
		.pipe(gulp.dest(prodPath + 'js'));

	gulp.src(appFiles)
		.pipe(concat('app.js'))
		.pipe(eol("\n"))
		.pipe(gulp.dest(prodPath + 'js'));

	gulp.src(sourcePath + 'js/lib/ie.js')
		.pipe(concat('ie.js'))
		.pipe(eol("\n"))
		.pipe(gulp.dest(prodPath + 'js'));

});

var watcher = null;
var watcherFiles = [
	sourcePath + 'js/lib/*.js',
	sourcePath + 'js/project/*.js',
	sourcePath + 'js/project/inc/*.js',
	sourcePath + 'css/lib/*.css',
	sourcePath + 'css/project/*.css',
	sourcePath + 'templates/*.html',
	sourcePath + 'templates/index/*.html',
	sourcePath + 'templates/index/main/*.html',
	sourcePath + 'templates/index/modal/*.html',
	sourcePath + 'templates/tablet/*',
	sourcePath + 'build/assets/js/order-form.js'
];

gulp.task('watch', function () {
	watcherInit(watcherFiles);
});

function watcherInit() {
	if (watcher) {
		watcher.remove();
		console.log('remove old watcher');
	}
	console.log('run new watcher');
	watcher = gulp.watch(watcherFiles, {verbose: true}, buildAll);
	watcher.on('change', buildEvent);
}


function buildEvent(event) {
	console.log('File ' + event.path + ' was ' + event.type + ' running tasks...');
	if (event.type == 'deleted') {
		console.log('ooops... deleted');
		watcherInit(watcherFiles);
	}
}

function buildAll() {
	console.log('run build all');
	gulp.start('app.css', 'app.js', 'app.html');
}

gulp.task('lang.js', function () {

	var cfg = config.get('lang.contentBlocks.production');

	var options = {
		url : cfg.url,
		//port: cfg.port,
		//path: '/man/content/blocks',
		headers: {
			'Accept': 'application/json, text/plain, */*',
			'Authorization': "Basic " + new Buffer(config.get('manage.auth')).toString("base64")
		}
	};

	/**
	 * скачиваем контентные блоки через API
	 */
	request(options, function (error, response, body) {

		if (!error && response.statusCode == 200) {
			console.log(body);
		}else{
			console.log('ERROR');
			console.log(error);
			console.log(response.statusCode);
			console.log(options);
			return;
		}

		var data = JSON.parse(body);

		/**
		 * формируем языковые разделы
		 *
		 * langBlocks = {
		 *   ru: {code: text, code: text, .. },
		 *   en: {code: text, code: text, .. },
		 *   ..
		 * }
		 *
		 */
		var langBlocks = {};
		for (var i = 0, unit, qnt = data.length; i < qnt; i++) {
			unit = data[i];
			for (var l = 0, text, lang; l < languages.length; l++) {
				lang = languages[l];
				langBlocks[lang] = langBlocks[lang] || {};
				text = unit.lang && unit.lang[lang];
				// если нет перевода, берем русский по умолчанию
				text = text || unit.lang['ru'];
				langBlocks[lang]['cb-' + unit.code] = text;
			}
		}

		/**
		 * сохраняем в специальный файлик blocks.js
		 */
		fs.writeFile(
			"./lang/gulp/blocks.js",
			'module.exports = ' + JSON.stringify(langBlocks, null, 4) + ';',
			function (err) {
				if (err) {
					console.log('ERROR: ' + err);
				} else {
					console.log('File blocks.js created!');
				}
			}
		);

	});

});

gulp.task('default', [
	'app.js',
	'app.css',
	'app.html'
]);

/**
 * объединение языковых файлов
 * ru.js + blocks.js[ru]
 * en.js + blocks.js[en]
 * @param {String} lang
 * @returns {Object}
 */
function mergeLang(lang) {
	var based = require('./lang/gulp/' + lang + '.js');
	var blocks = require('./lang/gulp/blocks.js');
	return merge(based, blocks[lang]);
}

/**
 * объединение объектов
 * @param {Object} obj1
 * @param {Object} obj2
 * @returns {Object}
 */
function merge(obj1, obj2) {

	var key, obj3 = {};

	for (key in obj1) {
		if (obj1.hasOwnProperty(key)) {
			obj3[key] = obj1[key];
		}
	}

	for (key in obj2) {
		if (obj2.hasOwnProperty(key)) {
			obj3[key] = obj2[key];
		}
	}

	return obj3;

}