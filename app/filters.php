<?php

App::before(function(\Illuminate\Http\Request $request)
{

	$languages = ['en', 'ru'];
	$lang = Input::get('lang');
	if(!$lang) {
		$path = explode('/', rtrim($request->path()));
		if(in_array(end($path), $languages) && $path[count($path)-2] == 'lang'){
			$lang = end($path);
		}
	}

	if($lang && !in_array($lang, $languages)){
		$lang = 'ru';
	}

	if($lang) {
		Session::set('lang', $lang);
	}else{
		$lang = Session::get('lang');
	}

	if(!$lang){
		$lang = 'ru';
		Session::set('lang', $lang);
	}

	App::setLocale($lang);

});


App::after(function($request, $response)
{
	//
});

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		return Redirect::guest('login');
	}
});

Route::filter('auth.basic', function()
{
	return Auth::basic();
});

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

Route::filter('csrf', function()
{
	if (Session::token() !== Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});


Route::filter('manage.auth.basic', function()
{
	if('dh-manage' == Request::getUser() && 'b53EAB2XF4BH2jH0L2Qa' == Request::getPassword()){
		return null;
	}
	header('HTTP/1.1 401 Unauthorized');
	header('WWW-Authenticate: Basic realm="Dryharder"');
	die('Требуется авторизация');
});
