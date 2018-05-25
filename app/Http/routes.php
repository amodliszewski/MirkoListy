<?php

Route::get('/', array(
    'uses' => 'DefaultController@index',
    'as' => 'homepage'
));

Route::get('/notatkowator', array(
    function() {
        return view('default/notatkowator');
    },
    'as' => 'notatkowator'
));

Route::get('/offline', array(
    function() {
        if (!isset($_ENV['IS_OFFLINE']) || $_ENV['IS_OFFLINE'] == 0) {
            return redirect(route('homepage'));
        }

        return view('default/offline');
    },
    'as' => 'offline'
));

Route::get('/login', array(
    'uses' => '\WykoCommon\Http\Controllers\AuthController@login',
    'as' => 'login'
));

Route::get('/logout', array(
    'uses' => '\WykoCommon\Http\Controllers\AuthController@logout',
    'as' => 'logout'
))->middleware(['auth']);

// single calls
Route::get('/call', array(
    function() {
        if (isset($_ENV['IS_OFFLINE']) && $_ENV['IS_OFFLINE'] == 1) {
            return redirect(route('offline'));
        }

        return view('call/form');
    },
    'as' => 'callFormUrl'
))
    ->middleware(['auth', 'canAdd']);

Route::post('/call/optout', 'CallController@optout')
    ->middleware(['auth']);

Route::post('/call', 'CallController@call')
    ->middleware(['auth', 'canAdd']);

Route::get('/faq', array(
    function() {
        return view('default/faq');
    },
    'as' => 'faqUrl'
));

Route::get('/privacy', array(
    function() {
        return view('default/privacy');
    },
    'as' => 'privacyUrl'
));
    
// lists
Route::get('/lists', array(
    'uses' => 'SpamlistController@index',
    'as' => 'getSpamlistsUrl'
));

Route::get('/list/add', array(
    'uses' => 'SpamlistController@addForm',
    'as' => 'addSpamlistUrl'
))->middleware(['auth', 'canAdd']);

Route::post('/list/add', 'SpamlistController@add')
    ->middleware(['auth', 'canAdd']);

Route::get('/list/{uid}', array(
    'uses' => 'SpamlistController@get',
    'as' => 'getSpamlistUrl'
))
    ->where('uid', '[0-9A-Za-z]{16}');

Route::get('/list/{uid}/people', array(
    'uses' => 'SpamlistController@people',
    'as' => 'getSpamlistPeopleUrl'
))
    ->where('uid', '[0-9A-Za-z]{16}');

Route::post('/list/{uid}/people', array(
    'uses' => 'SpamlistController@changeRights',
    'as' => 'postSpamlistPeopleUrl'
))
    ->where('uid', '[0-9A-Za-z]{16}')
    ->middleware(['auth', 'canAdd']);

Route::post('/list/{uid}/call', array(
    'uses' => 'SpamlistController@call',
    'as' => 'postSpamlistCallUrl'
))
    ->where('uid', '[0-9A-Za-z]{16}')
    ->middleware(['auth', 'canAdd']);

Route::post('/list/{uid}/import', array(
    'uses' => 'SpamlistController@import',
    'as' => 'postSpamlistImportUrl'
))
    ->where('uid', '[0-9A-Za-z]{16}')
    ->middleware(['auth', 'gotExtendedRights']);

Route::post('/list/{uid}/join', array(
    'uses' => 'SpamlistController@join',
    'as' => 'postSpamlistJoinUrl'
))
    ->where('uid', '[0-9A-Za-z]{16}')
    ->middleware(['auth']);

Route::post('/list/{uid}/delete', array(
    'uses' => 'SpamlistController@delete',
    'as' => 'deleteSpamlistUrl'
))
    ->where('uid', '[0-9A-Za-z]{16}')
    ->middleware(['auth', 'canAdd']);

Route::get('/list/{uid}/edit', array(
    'uses' => 'SpamlistController@editForm',
    'as' => 'getSpamlistEditFormUrl'
))
    ->where('uid', '[0-9A-Za-z]{16}')
    ->middleware(['auth', 'canAdd']);

Route::post('/list/{uid}/edit', array(
    'uses' => 'SpamlistController@edit',
    'as' => 'getSpamlistEditUrl'
))
    ->where('uid', '[0-9A-Za-z]{16}')
    ->middleware(['auth', 'canAdd']);

Route::get('/scheduled', array(
    'uses' => 'ScheduledController@items',
    'as' => 'getScheduledItemsUrl'
));

Route::get('/scheduled/add', array(
    'uses' => 'ScheduledController@addForm',
    'as' => 'addScheduledUrl'
));

Route::post('/scheduled/add', array(
    'uses' => 'ScheduledController@add'
));

Route::post('/scheduled/{id}/delete', array(
    'uses' => 'ScheduledController@delete',
    'as' => 'deleteScheduledUrl'
))
    ->where('id', '[0-9]+');

Route::get('/scheduled/{id}/edit', array(
    'uses' => 'ScheduledController@editForm',
    'as' => 'editScheduledUrl'
))
    ->where('id', '[0-9]+');

Route::post('/scheduled/{id}/edit', array(
    'uses' => 'ScheduledController@edit'
))
    ->where('id', '[0-9]+');


Route::get('/api/user/{nick}/callable', array(
    'uses' => 'APIController@userCallable'
))
    ->where('nick', '[0-9A-Za-z\_\-]+');

Route::get('/api/list/{uid}/people', array(
    'uses' => 'APIController@usersOnSpamlist'
))
    ->where('uid', '[0-9A-Za-z]{16}');

Route::post('/api/list/{uid}/call/confirm', array(
    'uses' => 'APIController@confirmCall'
))
    ->where('uid', '[0-9A-Za-z]{16}');

Route::get('/rights', array(
    function() {
        return view('rights/form');
    },
    'as' => 'searchProfileUrl'
));

Route::post('/rights/search', array(
    'uses' => 'RightsController@search',
    'as' => 'checkProfileUrl'
));

Route::get('/rights/{id}', array(
    'uses' => 'RightsController@profile',
    'as' => 'profileUrl'
))
    ->where('id', '[0-9]+');

Route::post('/rights/changeRights', array(
    'uses' => 'RightsController@changeRights',
    'as' => 'changeProfileRightsUrl'
));

Route::get('/logs', array(
    'uses' => 'LogsController@index',
    'as' => 'logsUrl'
));

Route::get('/logs/{uid}', array(
    'uses' => 'LogsController@index',
    'as' => 'spamlistLogsUrl'
))
    ->where('uid', '[0-9A-Za-z]{16}');