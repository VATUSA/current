<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//
// VATUSA API Functions
// api.vatusa.devel/(apikey)/(request)/(params)
//
Route::group([
    'domain' => ((config('app.env') == 'dev') ? 'www.vatusa.devel' : ((config('app.env') == 'alpha') ? 'alpha' : 'www') . '.vatusa.net'),
    'middleware' => 'api'
], function () {
    Route::group(['prefix' => '{apikey}/', 'middleware' => 'apikey'], function () {
        // CBT
        Route::get('cbt/block', 'APIController@getCBTBlocks');
        Route::get('cbt/block/{id}', 'APIController@getCBTChapters')->where('id', '[0-9]+');
        Route::get('cbt/chapter/{id}', 'APIController@getCBTChapter')->where('id', '[0-9]+');
        Route::put('cbt/progress/{cid}', 'APIController@putCBTProgress')->where('cid', '[0-9]+');

        Route::get('controller/{cid}', 'APIController@getController')->where('cid', '[0-9]+');

        // Exam
        Route::get('exam', 'APIController@getExam');
        Route::get('exam/assignment/{id}', 'APIController@getExamAssignment');
        Route::put('exam/assignment/{id}', 'APIController@putExamAssignment');
        Route::delete('exam/assignment/{id}/{cid}', 'APIController@deleteExamAssignment');
        Route::get('exam/score/{cid}', 'APIController@getExamScores');
        Route::get('exam/results/{cid}', 'APIController@getExamUserResults')->where('cid', '[0-9]+');
        Route::get('exam/result/{rid}', 'APIController@getExamResult')->where('rid', '[0-9]+');

        // Promotion
        Route::get('promotion', 'APIController@getPromotion');
        Route::post('promotion/{cid}', 'APIController@postPromotion')->where('cid', '[0-9]+');

        // Roster
        Route::get('roster', 'APIController@getRoster');
        Route::get('roster/{fac}', 'APIController@getRoster')->where('fac', '[A-Z]{3}');

        Route::delete('roster/{cid}', 'APIController@deleteRoster')->where('cid', '[0-9]+');
        Route::delete('roster/{fac}/{cid}', 'APIController@deleteRoster')->where('fac', '[A-Z]{3}')->where('cid',
            '[0-9]+');

        // Solo Certs
        Route::get('solo/{cid}', 'APIController@getSolo')->where('cid', '[0-9]+');
        Route::post('solo/{cid}/{position}', 'APIController@postSolo')->where('cid', '[0-9]+')->where("position",
            "[0-9A-Z_]+");
        Route::delete('solo/{cid}/{position}', 'APIController@deleteSolo')->where('cid', '[0-9]+')->where("position",
            "[0-9A-Z_]+");

        // Transfer
        Route::get('transfer', 'APIController@getTransfer');
        Route::get('transfer/{fac}', 'APIController@getTransfer')->where('fac', '[A-Z]{3}');
        Route::post('transfer/{id}', 'APIController@postTransfer')->where('id', '[0-9]+');
        Route::post('register', 'APIController@postRegister');

        Route::get('conntest', 'APIController@getConnTest');
    });
    Route::get('news.{ext},{limit}', 'APIController@getPublicNews')->where(['ext' => '[A-Za-z]+', 'limit' => '\d+']);
    Route::get('news,{limit}', 'APIController@getPublicNews')->where(['ext' => '[A-Za-z]+', 'limit' => '\d+']);
    Route::get('news.{ext}', 'APIController@getPublicNews')->where(['ext' => '[A-Za-z]+', 'limit' => '\d+']);
    Route::get('news,{limit}.{ext}', 'APIController@getPublicNews')->where(['ext' => '[A-Za-z]+', 'limit' => '\d+']);
    Route::get('news', 'APIController@getPublicNews')->where(['ext' => '[A-Za-z]+', 'limit' => '\d+']);

    Route::get('events.{ext},{limit}', 'APIController@getPublicEvents')->where(['ext' => '[A-Za-z]+',
        'limit' => '\d+'
    ]);
    Route::get('events,{limit}', 'APIController@getPublicEvents')->where(['ext' => '[A-Za-z]+', 'limit' => '\d+']);
    Route::get('events.{ext}', 'APIController@getPublicEvents')->where(['ext' => '[A-Za-z]+', 'limit' => '\d+']);
    Route::get('events,{limit}.{ext}', 'APIController@getPublicEvents')->where(['ext' => '[A-Za-z]+',
        'limit' => '\d+'
    ]);
    Route::get('events', 'APIController@getPublicEvents')->where(['ext' => '[A-Za-z]+', 'limit' => '\d+']);

    Route::get('roster-{fac}.{ext},{limit}', 'APIController@getPublicRoster')->where(['fac' => '[A-Z][A-Z][A-Z]',
        'ext' => '[A-Za-z]+',
        'limit' => '\d+'
    ]);
    Route::get('roster-{fac},{limit}', 'APIController@getPublicRoster')->where(['fac' => '[A-Z][A-Z][A-Z]',
        'ext' => '[A-Za-z]+',
        'limit' => '\d+'
    ]);
    Route::get('roster-{fac}.{ext}', 'APIController@getPublicRoster')->where(['fac' => '[A-Z][A-Z][A-Z]',
        'ext' => '[A-Za-z]+',
        'limit' => '\d+'
    ]);
    Route::get('roster-{fac},{limit}.{ext}', 'APIController@getPublicRoster')->where(['fac' => '[A-Z][A-Z][A-Z]',
        'ext' => '[A-Za-z]+',
        'limit' => '\d+'
    ]);
    Route::get('roster-{fac}', 'APIController@getPublicRoster')->where(['fac' => '[A-Z][A-Z][A-Z]',
        'ext' => '[A-Za-z]+',
        'limit' => '\d+'
    ]);

    Route::get('planes', 'APIController@getPublicPlanes');

    Route::get('/', function () {
        return view('api.index');
    });
});

Route::group([
    'domain' => ((config('app.env') == 'dev') ? 'www.vatusa.devel' : ((config('app.env') == 'alpha') ? 'alpha' : 'www') . '.vatusa.net'),
    'middleware' => ['smf', 'csrf', 'lastactivity']
], function () {
    Route::get('/login', 'AuthController@getLogin');
    Route::get('/logout', function () {
        \Auth::logout();

        return redirect("/");
    });

    Route::group(['middleware' => 'privacy-agree'], function () {
        Route::get('/', ['as' => '/', 'uses' => 'HomeController@index']);

// General global AJAX
        Route::get('/ajax/cid', 'AJAXController@getCID');
        Route::get('/ajax/events', 'AJAXController@getEvents');
        Route::get('/ajax/news', 'AJAXController@getNews');
        Route::get('/ajax/help/staffc/{facility}', 'AJAXController@getHelpStaffc');
        Route::get('/ajax/help/staff/{facility}', 'AJAXController@getHelpStaff');
        Route::get('/ajax/passstrength/{pass}', function ($pass) {
            echo \App\Classes\cPanelHelper::getPassStrength($pass);
        });

//
// VATUSA CBT
//
//
// #Viewer#
        Route::get('/cbt', 'CBTController@getIndex');
        Route::get('/cbt/{fac}', 'CBTController@getIndex')->where('fac', '[A-Z]{3}');
        Route::put('/cbt/{id}', 'CBTController@putIndex')->where('id', '[0-9]+');
// #Editor#
        Route::get('/cbt/editor', 'CBTController@getEditor');
        Route::get('/cbt/editor/{fac}', 'CBTController@getEditor')->where('fac', '[A-Z]{3}');
        Route::get('/cbt/editor/{id}', 'CBTController@getEditorBlock')->where('id', '[0-9]+');
// CBT Ajax Functions
// --- Block
        Route::post('/cbt/editor/ajax/blocktoggle/{id}', 'CBTController@ajaxBlockToggle')->where('id', '[0-9]+');
        Route::delete('/cbt/editor/ajax/block/{id}', 'CBTController@ajaxDeleteBlock')->where('id', '[0-9]+');
        Route::put('/cbt/editor/ajax/block/{fac}', 'CBTController@ajaxPutBlock')->where('fac', '[A-Z]{3}');
        Route::post('/cbt/editor/ajax/block/order/{fac}', 'CBTController@ajaxOrderBlock')->where('fac', '[A-Z]{3}');
        Route::post('/cbt/editor/ajax/block/rename/{id}', 'CBTController@ajaxRenameBlock')->where('id', '[0-9]+');
        Route::post('/cbt/editor/ajax/block/access/{id}', 'CBTController@ajaxChangeAccess')->where('id', '[0-9]+');
// --- Chapter
        Route::delete('/cbt/editor/ajax/chapter/{id}', 'CBTController@ajaxChapterDelete')->where('id', '[0-9]+');
        Route::put('/cbt/editor/ajax/chapter/{id}', 'CBTController@ajaxChapterNew')->where('id', '[0-9]+');
        Route::post('/cbt/editor/ajax/chapter/{id}', 'CBTController@ajaxChapterModify')->where('id', '[0-9]+');

// Helpdesk
        Route::get('/help', 'HelpdeskController@getIndex');
        Route::get('/help/kb', 'HelpdeskController@getKBIndex');
        // KB Category
        Route::get('/help/kbe', 'HelpdeskController@getKBE');
        Route::delete('/help/kbe/{id}', 'HelpdeskController@deleteKBECategory');
        Route::put('/help/kbe', 'HelpdeskController@putKBECategory');
        Route::post('/help/kbe/{id}', 'HelpdeskController@postKBECategory');
        // KB Question
        Route::get('/help/kbe/{id}', 'HelpdeskController@getKBECategory');
        Route::get('/help/kbe/{cid}/{id}', 'HelpdeskController@getKBEeditQuestion');
        Route::post('/help/kbe/{cid}/{id}', 'HelpdeskController@postKBEeditQuestion');

        Route::get('/help/kbe/ajax/question/{id}', 'HelpdeskController@getKBEQuestion');
        Route::post('/help/kbe/ajax/question/order/{id}', 'HelpdeskController@postKBEQuestionOrder');
        Route::delete('/help/kbe/ajax/question/{id}', 'HelpdeskController@deleteKBEQuestion');
        Route::put('/help/kbe/ajax/question/{id}', 'HelpdeskController@putKBEQuestion');
        Route::post('/help/kbe/ajax/question/{id}', 'HelpdeskController@postKBEQuestion');
        // Support Tickets
        Route::get('/help/ticket/new', 'HelpdeskController@getNew');
        Route::post('/help/ticket/new', 'HelpdeskController@postNew');
        Route::get('/help/ticket/{status}', 'HelpdeskController@getList')->where('status', '[A-Za-z]+');
        Route::post('/help/ticket/{status}', 'HelpdeskController@postList')->where('status', '[A-Za-z]+');
        Route::get('/help/ticket/{id}', 'HelpdeskController@getTicket');
        Route::post('/help/ticket/{id}', 'HelpdeskController@postTicket');
        Route::post('/help/ticket/ajax/{id}', 'HelpdeskController@postTicketAjax');
        Route::get('/help/ticket/{id}/status', 'HelpdeskController@getTicketToggleStatus');

//
// My VATUSA Function
// dev.vatusa.net/my/{route}
//
        Route::get('/my/exams', 'MyController@getExamIndex');
        Route::get('/my/profile', 'MyController@getProfile');
        Route::post('/my/profile/toggleBroadcast', 'MyController@toggleBroadcastEmails');
        Route::get('/my/select', 'MyController@getSelect');
        Route::post('/my/select', 'MyController@postSelect');
        Route::get('/my/transfer', 'MyController@getTransfer');
        Route::post('/my/transfer/do', 'MyController@doTransfer');
        Route::get('/my/assignbasic', 'MyController@getAssignBasic');

//
// VATUSA Exam Function
// dev.vatusa.net/exam/{route}
//
        Route::get('/exam', 'ExamController@getIndex');
        Route::get('/exam/assign', 'ExamController@getAssign');
        Route::post('/exam/assign', 'ExamController@postAssign');
        Route::get('/exam/{id}', 'ExamController@getTakeExam')->where('id', '[0-9]+');
        Route::put('/exam/{id}', 'ExamController@putTakeExam')->where('id', '[0-9]+');
        Route::get('/exam/delete/{id}', 'ExamController@getDeleteExam')->where('id', '[0-9]+');
// Editor
// Create
        Route::get('/exam/create', 'ExamController@getCreate');
        Route::post('/exam/create', 'ExamController@postCreate');
// Edit
        Route::get('/exam/edit', 'ExamController@getEdit');
        Route::post('/exam/edit', 'ExamController@editExam');
        Route::get('/exam/edit/{id}', 'ExamController@editExam')->where('id', '[0-9]+');
        Route::post('/exam/edit/{id}', 'ExamController@postEditExam')->where('id', '[0-9]+');
        Route::get('/exam/edit/{examid}/{qid}', 'ExamController@getEditQuestion')->where('examid',
            '[0-9]+')->where('qid', '[0-9]+');
        Route::post('/exam/edit/{examid}/{qid}', 'ExamController@postEditQuestion')->where('examid',
            '[0-9]+')->where('qid', '[0-9]+');
        Route::delete('/exam/edit/{examid}/{qid}', 'ExamController@deleteQuestion')->where('examid',
            '[0-9]+')->where('qid', '[0-9]+');
// View
        Route::get('/exam/view', 'ExamController@getAssignments');
        Route::get('/exam/view/{fac}', 'ExamController@getAssignments')->where('fac', '[A-Z]{3}');
        Route::get('/exam/download/{id}', 'ExamController@getDownload')->where('id', '[0-9]+');
        Route::delete('/exam/assignment/{id}', 'ExamController@deleteAssignment')->where('id', '[0-9]+');
        Route::delete('/exam/reassignment/{id}', 'ExamController@deleteReassignment')->where('id', '[0-9]+');
// Result
        Route::get('/exam/result/{id}', 'ExamController@getResult');

//
// VATUSA Info Function
// dev.vatusa.net/info/{route}
//
        Route::get('/info/ace', 'InfoController@getACE');
        Route::get('/info/join', 'InfoController@getJoin');
        Route::get('/info/members', 'InfoController@getMembers');
        Route::get('/info/policies', 'InfoController@getPolicies');
        Route::get('/info/solo', function () {
            return view('info.solo');
        });
        Route::post('/info/ajax/members', 'InfoController@ajaxFacilityInfo');
        Route::get('/info/privacy', function () {
            return view('info.privacy');
        });

//
// VATUSA Mgt Facility Function
// dev.vatusa.net/mgt/facility/{route}
//
        Route::get('/mgt/facility/{fac?}', 'FacMgtController@getIndex');
        Route::delete('/mgt/facility/{fac}/{cid}', 'FacMgtController@deleteController');
        Route::post('/mgt/facility/{fac}/api/generate', 'FacMgtController@postAPIGenerate');
        Route::post('/mgt/facility/{fac}/api/generate/sandbox', 'FacMgtController@postAPISandboxGenerate');
        Route::post('/mgt/facility/{fac}/api/update', 'FacMgtController@postAPIIP');
        Route::post('/mgt/facility/{fac}/api/update/sandbox', 'FacMgtController@postAPISandboxIP');
        Route::post('/mgt/facility/{fac}/uls/generate', 'FacMgtController@postULSGenerate');
        Route::post('/mgt/facility/{fac}/uls/return', 'FacMgtController@postULSReturn');
        Route::post('/mgt/facility/{fac}/uls/devreturn', 'FacMgtController@postULSDevReturn');
        Route::get('/mgt/ace', 'MgtController@getAce');
        Route::post('/mgt/ace', 'MgtController@putAce');
        Route::get('/mgt/ace/delete/{cid}', 'MgtController@deleteAce');
        Route::post('/mgt/action/add', 'MgtController@addLog');
        Route::post('/mgt/ajax/position/{facility}/{id}', 'FacMgtController@ajaxPosition');
        Route::post('/mgt/ajax/del/position/{facility}', 'FacMgtController@ajaxPositionDel');
        Route::post('/mgt/ajax/staff/{facility}', 'FacMgtController@ajaxStaffTable');
        Route::post('/mgt/ajax/transfers/{status}', 'FacMgtController@ajaxTransfers');
        Route::get('/mgt/ajax/transfer/reason', 'FacMgtController@ajaxTransferReason');
        Route::get('/mgt/controller', 'MgtController@getController');
        Route::get('/mgt/controller/{cid}', 'MgtController@getController');
        Route::get('/mgt/controller/{cid}/mentor', 'MgtController@getControllerMentor');
        Route::get('/mgt/controller/{cid}/exams', 'MgtController@getControllerExams');
        Route::post('/mgt/controller/{cid}/rating', 'MgtController@postControllerRating');
        Route::get('/mgt/controller/{cid}/transfers', 'MgtController@getControllerTransfers');
        Route::get('/mgt/controller/{cid}/transferwaiver', 'MgtController@getControllerTransferWaiver');
        Route::get('/mgt/controller/{cid}/actions', 'MgtController@getControllerActions');
        Route::get('/mgt/controller/{cid}/transfer/override', 'MgtController@getControllerTransferOverride');
        Route::put('/mgt/controller/{cid}/transfer/override', 'MgtController@putControllerTransferOverride');
        Route::get('/mgt/controller/{cid}/promote', 'MgtController@getControllerPromote');
        Route::get('/mgt/controller/{cid}/togglebasic', 'MgtController@getControllerToggleBasic');
        Route::post('/mgt/controller/{cid}/promote', 'MgtController@postControllerPromote');
        Route::delete('/mgt/controller/{cid}/transfer/override', 'MgtController@deleteControllerTransferOverride');
        Route::post('/mgt/controller/toggleStaffPrevent', 'MgtController@toggleStaffPrevent');
        Route::post('/mgt/controller/toggleInsRole', 'MgtController@toggleInsRole');
        Route::get('/mgt/err', 'MgtController@getERR');
        Route::post('/mgt/err', 'MgtController@postERR');
        Route::get('/mgt/solo', 'MgtController@getSolo');
        Route::post('/mgt/solo', 'MgtController@postSolo');
        Route::delete('/mgt/solo/{id}', 'MgtController@deleteSolo')->where('id', '[0-9]+');
        Route::get('/mgt/staff', 'MgtController@getStaff');
        Route::delete('/mgt/staff/{role}', 'MgtController@deleteStaff');
        Route::put('/mgt/staff/{role}', 'MgtController@putStaff');
        Route::get('/mgt/checklists', 'MgtController@getChecklists');
        Route::put('/mgt/checklists', 'MgtController@putChecklists');
        Route::post('/mgt/checklists/order', 'MgtController@postChecklistsOrder');
        Route::post('/mgt/checklists/{id}', 'MgtController@postChecklist')->where('id', '[0-9]+');
        Route::delete('/mgt/checklists/{id}', 'MgtController@deleteChecklist')->where('id', '[0-9]+');

        Route::delete('/mgt/deleteActionLog/{log}', 'MgtController@deleteActionLog')->where('id', '[0-9]+');

        Route::get('/mgt/tmu', 'TMUController@getMgtIndex');
        Route::get('/mgt/tmu/{fac}', 'TMUController@getMgtIndex');
        Route::get('/mgt/tmu/{fac}/colors', 'TMUController@getMgtColors');
        Route::post('/mgt/tmu/colors', 'TMUController@postMgtColors');
        Route::post('/mgt/tmu/{fac}/colors', 'TMUController@postMgtColors');

        Route::post('/mgt/tmu/{fac}/coords', 'TMUController@postMgtCoords');

        Route::get('/mgt/tmu/{fac}/mapping/{id}', 'TMUController@getMgtMapping');
        Route::post('/mgt/tmu/{fac}/mapping/{id}', 'TMUController@postMgtMapping');;

        Route::get('/mgt/checklists/{id}', 'MgtController@getChecklistItems');
        Route::put('/mgt/checklists/{id}', 'MgtController@putChecklistItem');
        Route::post('/mgt/checklists/{id}/order', 'MgtController@postChecklistItemsOrder');
        Route::delete('/mgt/checklists/{clid}/{id}', 'MgtController@deleteChecklistItem');
        Route::post('/mgt/checklists/{clid}/{id}', 'MgtController@postChecklistItem');

        // TMU ********************
        Route::get('tmu/map/{fac}', 'TMUController@getMap');
        Route::get('tmu/map/{fac}/dark', 'TMUController@getMapDark');
        Route::get('tmu/map/{fac}/coords', 'TMUController@getCoords');
        Route::get('tmu/notices/{sector?}', 'TMUController@getNotices');

//
// VATUSA Mgt Mail Function
// dev.vatusa.net/mgt/mail/{route}
//
        Route::get('/mgt/mail', 'EmailMgtController@getIndex');
        Route::get('/mgt/mail/broadcast', 'EmailMgtController@getBroadcast');
        Route::post('/mgt/mail/broadcast', 'EmailMgtController@postBroadcast');
        Route::get('/mgt/mail/conf', 'EmailMgtController@getConfig');
        Route::post('/mgt/mail/conf', 'EmailMgtController@postConfig');
        Route::get('/mgt/mail/account', 'EmailMgtController@getAccount');
        Route::get('/mgt/mail/{cid}', 'EmailMgtController@getIndividual')->where('cid', '[0-9]+');
        Route::get('/mgt/mail/get/{user}', 'EmailMgtController@getType');
        Route::get('/mgt/mail/welcome', 'EmailMgtController@getWelcome');
        Route::post('/mgt/mail/welcome', 'EmailMgtController@postWelcome');
        Route::get('/mgt/mail/template', 'EmailMgtController@getTemplates');
        Route::get('/mgt/mail/template/{template}/{action}', 'EmailMgtController@getTemplateAction');
        Route::post('/mgt/mail/template/{template}', 'EmailMgtController@postTemplate');

// Statistics
        Route::get('/stats', 'StatsController@getIndex');
        Route::get('/stats/details/{facility}', 'StatsController@getDetails');
        Route::get('/stats/export/details', 'StatsController@getExportDetails');
        Route::get('/stats/export/overview', 'StatsController@getExportOverview');
    });
});

//
// Errors
//
Route::get('404', function () {
    return View('errors.404');
});
Route::get('401', function () {
    return View('errors.401');
});
Route::get('500', function () {
    return View('errors.500');
});
