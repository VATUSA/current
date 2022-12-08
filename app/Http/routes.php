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
Route::get('readiness', function () {
    try {
        DB::connection()->getPdo();
    } catch (Exception $e) {
        return response('Not Ready', 500);
    }

    return 'Ready';
});

Route::group([
    'domain'     => config('app.url'),
    'middleware' => ['smf', 'csrf', 'lastactivity']
], function () {
    Route::get('/login', 'AuthController@getLogin');
    Route::get('/logout', function () {
        \Auth::logout();

        return redirect("/");
    });

    /* * * * *
     *  TMU  *
     * * * * */
    Route::group(['prefix' => 'tmu'], function () {
        Route::get('map/{fac}', 'TMUController@getMap');
        Route::get('map/{fac}/dark', 'TMUController@getMapDark');
        Route::get('map/{fac}/coords', 'TMUController@getCoords');
        Route::get('notices/{sector?}', 'TMUController@getNotices');
    });

    Route::group(['middleware' => 'privacy-agree'], function () {
        Route::get('/', ['as' => '/', 'uses' => 'HomeController@index']);

        /* * * * * *
         *  AJAX  *
         * * * * * */
        Route::group(['prefix' => 'ajax'], function () {
            Route::get('/cid', 'AJAXController@getCID');
            Route::get('/events', 'AJAXController@getEvents');
            Route::get('/news', 'AJAXController@getNews');
            Route::get('/help/staffc/{facility}', 'AJAXController@getHelpStaffc');
            Route::get('/help/staff/{facility}', 'AJAXController@getHelpStaff');
            Route::get('/passstrength/{pass}', function ($pass) {
                echo \App\Classes\cPanelHelper::getPassStrength($pass);
            });
        });

        /* * * * *
         *  CBT  *
         * * * * */
        Route::group(['prefix' => 'cbt'], function () {
            Route::get('/', 'CBTController@getIndex');
            Route::group(['middleware' => 'auth'], function () {
                Route::get('{fac}', 'CBTController@getIndex')->where('fac', '[A-Z]{3}');
                Route::put('{id}', 'CBTController@putIndex')->where('id', '[0-9]+');

                // * * * * * * Editor * * * * * *
                Route::group(['prefix' => 'editor'], function () {
                    Route::get('/', 'CBTController@getEditor');
                    Route::get('{fac}', 'CBTController@getEditor')->where('fac', '[A-Z]{3}');
                    Route::get('{id}', 'CBTController@getEditorBlock')->where('id', '[0-9]+');

                    Route::group(['prefix' => 'ajax'], function () {
                        Route::post('blocktoggle/{id}', 'CBTController@ajaxBlockToggle')->where('id',
                            '[0-9]+');
                        Route::delete('block/{id}', 'CBTController@ajaxDeleteBlock')->where('id', '[0-9]+');
                        Route::put('block/{fac}', 'CBTController@ajaxPutBlock')->where('fac', '[A-Z]{3}');
                        Route::post('block/order/{fac}', 'CBTController@ajaxOrderBlock')->where('fac',
                            '[A-Z]{3}');
                        Route::post('block/rename/{id}', 'CBTController@ajaxRenameBlock')->where('id',
                            '[0-9]+');
                        Route::post('block/access/{id}', 'CBTController@ajaxChangeAccess')->where('id',
                            '[0-9]+');
                        Route::delete('chapter/{id}', 'CBTController@ajaxChapterDelete')->where('id',
                            '[0-9]+');
                        Route::put('chapter/{id}', 'CBTController@ajaxChapterNew')->where('id', '[0-9]+');
                        Route::post('chapter/{id}', 'CBTController@ajaxChapterModify')->where('id', '[0-9]+');
                    });
                });
            });
        });

        /* * * * * * * * * *
         *  Knowledgebase  *
         * * * * * * * * * */
        Route::get('/help', function () {
            return Redirect::to('/help/kb');
        });
        Route::get('/help/kb', 'HelpdeskController@getKBIndex');
        Route::group(['middleware' => 'auth'], function () {
            Route::group(['prefix' => 'help'], function () {
                Route::group(['prefix' => 'kbe'], function () {
                    Route::get('/', 'HelpdeskController@getKBE');

                    // * * * * * * Editor * * * * * *
                    Route::delete('{id}', 'HelpdeskController@deleteKBECategory');
                    Route::put('/', 'HelpdeskController@putKBECategory');
                    Route::post('{id}', 'HelpdeskController@postKBECategory');
                    Route::get('{id}', 'HelpdeskController@getKBECategory');
                    Route::get('{cid}/{id}', 'HelpdeskController@getKBEeditQuestion');
                    Route::post('{cid}/{id}', 'HelpdeskController@postKBEeditQuestion');

                    // * * * * * * Editor AJAX * * * * * *
                    Route::group(['prefix' => 'ajax/question'], function () {
                        Route::get('{id}', 'HelpdeskController@getKBEQuestion');
                        Route::post('order/{id}', 'HelpdeskController@postKBEQuestionOrder');
                        Route::delete('{id}', 'HelpdeskController@deleteKBEQuestion');
                        Route::put('{id}', 'HelpdeskController@putKBEQuestion');
                        Route::post('{id}', 'HelpdeskController@postKBEQuestion');
                    });
                });

                /* * * * * * *
                 *  Support  *
                 * * * * * * */
                Route::group(['prefix' => 'ticket'], function () {
                    Route::get('new', 'HelpdeskController@getNew');
                    Route::post('new', 'HelpdeskController@postNew');
                    Route::get('{status}', 'HelpdeskController@getList')->where('status', '[A-Za-z]+');
                    Route::post('{status}', 'HelpdeskController@postList')->where('status',
                        '[A-Za-z]+');
                    Route::get('{id}', 'HelpdeskController@getTicket');
                    Route::post('{id}', 'HelpdeskController@postTicket');
                    Route::post('ajax/{id}', 'HelpdeskController@postTicketAjax');
                    Route::get('{id}/status', 'HelpdeskController@getTicketToggleStatus');
                });
            });

            /* * * * * * * *
             *  My Profle  *
             * * * * * * * */
            Route::group(['prefix' => 'my'], function () {
                Route::get('exams', 'MyController@getExamIndex');
                Route::get('profile', 'MyController@getProfile');
                Route::post('profile', 'MyController@getProfile');
                Route::post('profile/toggleBroadcast', 'MyController@toggleBroadcastEmails');
                Route::get('select', 'MyController@getSelect');
                Route::post('select', 'MyController@postSelect');
                Route::get('transfer', 'MyController@getTransfer');
                Route::post('transfer/do', 'MyController@doTransfer');
                Route::get('assignbasic', 'MyController@getAssignBasic');
                Route::get('discord/{mode}', 'MyController@linkDiscord');
            });

            /* * * * * * * * *
             *  Exam Center  *
             * * * * * * * * */
            Route::group(['prefix' => 'exam'], function () {
                Route::get('/', 'ExamController@getIndex');

                // * * * * * * Exam Assignment * * * * * *
                Route::get('assign', 'ExamController@getAssign');
                Route::post('assign', 'ExamController@postAssign');
                Route::delete('assignment/{id}', 'ExamController@deleteAssignment')->where('id',
                    '[0-9]+');
                Route::delete('reassignment/{id}', 'ExamController@deleteReassignment')->where('id',
                    '[0-9]+');
                Route::get('{id}', 'ExamController@getTakeExam')->where('id', '[0-9]+');
                Route::put('{id}', 'ExamController@putTakeExam')->where('id', '[0-9]+');
                Route::get('delete/{id}', 'ExamController@getDeleteExam')->where('id', '[0-9]+');

                // * * * * * * Editor - Create * * * * * *
                Route::get('create', 'ExamController@getCreate');
                Route::post('create', 'ExamController@postCreate');

                // * * * * * * Editor - Edit * * * * * *
                Route::group(['prefix' => 'edit'], function () {
                    Route::get('/', 'ExamController@getEdit');
                    Route::post('/', 'ExamController@editExam');
                    Route::get('{id}', 'ExamController@editExam')->where('id', '[0-9]+');
                    Route::post('{id}', 'ExamController@postEditExam')->where('id', '[0-9]+');
                    Route::get('{examid}/{qid}', 'ExamController@getEditQuestion')->where('examid',
                        '[0-9]+')->where('qid', '[0-9]+');
                    Route::post('{examid}/{qid}', 'ExamController@postEditQuestion')->where('examid',
                        '[0-9]+')->where('qid', '[0-9]+');
                    Route::delete('{examid}/{qid}', 'ExamController@deleteQuestion')->where('examid',
                        '[0-9]+')->where('qid', '[0-9]+');
                });

                // * * * * * * Exam View * * * * * *
                Route::get('view', 'ExamController@getAssignments');
                Route::get('view/{fac}', 'ExamController@getAssignments')->where('fac', '[A-Z]{3}');
                Route::get('download/{id}', 'ExamController@getDownload')->where('id', '[0-9]+');
                Route::get('result/{id}', 'ExamController@getResult');
            });
        });

        /* * * * * * * * * * * *
         * Public Information  *
         * * * * * * * * * * * */
        Route::group(['prefix' => 'info'], function () {
            Route::get('ace', 'InfoController@getACE');
            Route::get('join', 'InfoController@getJoin');
            Route::get('members', 'InfoController@getMembers');
            Route::get('policies', 'PolicyController@index');
            Route::get('policies/{slug}', 'PolicyController@show');
            Route::get('solo', function () {
                return view('info.solo');
            });
            Route::post('ajax/members', 'InfoController@ajaxFacilityInfo');
            Route::get('privacy', function () {
                return view('info.privacy');
            });
        });

        /* * * * * * * * *
         *  Management  *
         * * * * * * * */
        Route::group(['middleware' => 'auth', 'prefix' => 'mgt'], function () {
            // * * * * * * Facility * * * * * *
            Route::group(['prefix' => 'facility'], function () {
                Route::get('{fac?}', 'FacMgtController@getIndex');
                Route::group(['prefix' => '{fac}'], function () {
                    Route::delete('{cid}', 'FacMgtController@deleteController');
                    Route::post('api/generate', 'FacMgtController@postAPIGenerate');
                    Route::post('api/generate/sandbox', 'FacMgtController@postAPISandboxGenerate');
                    Route::post('api/update', 'FacMgtController@postAPIIP');
                    Route::post('api/update/sandbox', 'FacMgtController@postAPISandboxIP');
                    Route::post('uls/generate', 'FacMgtController@postULSGenerate');
                    Route::post('uls/return', 'FacMgtController@postULSReturn');
                    Route::post('uls/devreturn', 'FacMgtController@postULSDevReturn');
                });
            });

            // * * * * * * Ace * * * * * *
            Route::get('ace', 'MgtController@getAce');
            Route::post('ace', 'MgtController@putAce');
            Route::get('ace/delete/{cid}', 'MgtController@deleteAce');


            // * * * * * * AJAX * * * * * *
            Route::group(['prefix' => 'ajax'], function () {
                Route::post('position/{facility}/{id}', 'FacMgtController@ajaxPosition');
                Route::post('del/position/{facility}', 'FacMgtController@ajaxPositionDel');
                Route::post('staff/{facility}', 'FacMgtController@ajaxStaffTable');
                Route::post('transfers/{status}', 'FacMgtController@ajaxTransfers');
                Route::get('transfer/reason', 'FacMgtController@ajaxTransferReason');
            });


            // * * * * * * Controller * * * * * *
            Route::group(['prefix' => 'controller'], function () {
                Route::get('/', 'MgtController@getController');

                Route::group(['prefix' => '{cid}'], function () {
                    Route::get('/', 'MgtController@getController')->name('mgt.controller.index');
                    Route::post('/', 'MgtController@getController');
                    Route::get('mentor/{facility?}', 'MgtController@getControllerMentor');
                    Route::get('exams', 'MgtController@getControllerExams');
                    Route::post('rating', 'MgtController@postControllerRating');
                    Route::get('transfers', 'MgtController@getControllerTransfers');
                    Route::get('transferwaiver', 'MgtController@getControllerTransferWaiver');
                    Route::get('actions', 'MgtController@getControllerActions');
                    Route::get('transfer/override', 'MgtController@getControllerTransferOverride');
                    Route::put('transfer/override', 'MgtController@putControllerTransferOverride');
                    Route::get('promote', 'MgtController@getControllerPromote');
                    Route::get('togglebasic', 'MgtController@getControllerToggleBasic');
                    Route::post('promote', 'MgtController@postControllerPromote');
                    Route::delete('transfer/override',
                        'MgtController@deleteControllerTransferOverride');
                });

                Route::group(['prefix' => 'ajax'], function () {
                    Route::post('toggleStaffPrevent', 'MgtController@toggleStaffPrevent')->middleware('vatusastaff');
                    Route::post('toggleInsRole', 'MgtController@toggleInsRole')->middleware('vatusastaff');
                    Route::post('toggleAcademyEditor', 'MgtController@toggleAcademyEditor');
                    Route::post('toggleSMTRole', 'MgtController@toggleSMTRole')->middleware('vatusastaff');
                    Route::post('toggleTTRole', 'MgtController@toggleTTRole')->middleware('vatusastaff');
                });
                Route::post('action/add', 'MgtController@addLog');
                Route::delete('action/delete/{log}', 'MgtController@deleteActionLog')->where('id', '[0-9]+');
            });

            // * * * * * * Policies * * * * * *
            Route::get('policies', 'PolicyController@edit');
            Route::get('policies/{policy:slug}', 'PolicyController@show');
            Route::get('policies/getInfo/{policy}', 'PolicyController@getPolicy');
            Route::post('policies/store', 'PolicyController@store');
            Route::post('policies/updatePolicy/{policy}', 'PolicyController@update');
            Route::delete('policies/{policy}', 'PolicyController@destroy');
            Route::get('policies/newCategory', 'PolicyController@storeCategory');
            Route::put('policies/updateCategory/{category}', 'PolicyController@updateCategory');
            Route::get('policies/deleteCategory/{category}', 'PolicyController@destroyCategory');

            // * * * * * * Training - Evals * * * * * *
            Route::get('controller/{cid}/eval/{form?}', 'TrainingController@getOTSEval')->where('form',
                '[0-9]+');

            Route::group(['prefix' => 'facility/training'], function () {
                // * * * * * * Training - Stats * * * * * *
                Route::get('eval/{form?}/view',
                    'TrainingController@viewOTSEval')->where('form',
                    '[0-9]+');
                Route::get('eval/{form?}/stats',
                    'TrainingController@viewOTSEvalStatistics')->where('form', '[0-9]+');
                Route::post('eval/{form?}/stats',
                    'TrainingController@viewOTSEvalStatistics')->where('form', '[0-9]+');

                Route::get('stats', 'TrainingController@viewTrainingStatistics');
                Route::post('stats', 'TrainingController@viewTrainingStatistics');
                Route::get('evals', 'TrainingController@viewEvals');
                Route::post('evals', 'TrainingController@viewEvals');
            });

            // * * * * * * Training - Permissions * * * * * *
            Route::get('controller/ajax/canModifyRecord/{record}',
                'TrainingController@ajaxCanModifyRecord');

            // * * * * * * Transfer * * * * * *
            Route::get('transfer', 'MgtController@getManualTransfer');
            Route::post('transfer', 'MgtController@postManualTransfer');

            // * * * * * * Solo Endorsements * * * * * *
            Route::get('solo', 'MgtController@getSolo');
            Route::post('solo', 'MgtController@postSolo');
            Route::delete('solo/{id}', 'MgtController@deleteSolo')->where('id', '[0-9]+');

            // * * * * * * Division Staff * * * * * *
            Route::get('staff', 'MgtController@getStaff');
            Route::delete('staff/{role}', 'MgtController@deleteStaff');
            Route::put('staff/{role}', 'MgtController@putStaff');

            // * * * * * * Checklists * * * * * *
            Route::group(['prefix' => 'checklists'], function () {
                Route::get('/', 'MgtController@getChecklists');
                Route::put('/', 'MgtController@putChecklists');
                Route::post('order', 'MgtController@postChecklistsOrder');
                Route::post('{id}', 'MgtController@postChecklist')->where('id', '[0-9]+');
                Route::delete('{id}', 'MgtController@deleteChecklist')->where('id', '[0-9]+');
                Route::get('{id}', 'MgtController@getChecklistItems');
                Route::put('{id}', 'MgtController@putChecklistItem');
                Route::post('{id}/order', 'MgtController@postChecklistItemsOrder');
                Route::delete('{clid}/{id}', 'MgtController@deleteChecklistItem');
                Route::post('{clid}/{id}', 'MgtController@postChecklistItem');
            });

            // * * * * * * TMU * * * * * *
            Route::group(['prefix' => 'tmu'], function () {
                Route::get('/', 'TMUController@getMgtIndex');
                Route::get('{fac}', 'TMUController@getMgtIndex');
                Route::get('{fac}/colors', 'TMUController@getMgtColors');
                Route::post('colors', 'TMUController@postMgtColors');
                Route::post('{fac}/colors', 'TMUController@postMgtColors');
                Route::post('{fac}/coords', 'TMUController@postMgtCoords');
                Route::get('{fac}/mapping/{id}', 'TMUController@getMgtMapping');
                Route::post('{fac}/mapping/{id}', 'TMUController@postMgtMapping');
            });

            // * * * * * * iDENT APP * * * * * *
            Route::group(['prefix' => 'app'], function () {
                Route::get('push', 'AppController@getIndex');
                Route::post('push', 'AppController@postPush');
                Route::get('log', 'AppController@getLog');
                Route::get('log', 'AppController@getPushLog');
            });

            // * * * * * * Mail * * * * * *
            Route::group(['prefix' => 'mail'], function () {
                Route::get('/', 'EmailMgtController@getIndex');
                Route::get('broadcast', 'EmailMgtController@getBroadcast');
                Route::post('broadcast', 'EmailMgtController@postBroadcast');
                Route::get('conf', 'EmailMgtController@getConfig');
                Route::post('conf', 'EmailMgtController@postConfig');
                Route::get('account', 'EmailMgtController@getAccount');
                Route::get('{cid}', 'EmailMgtController@getIndividual')->where('cid', '[0-9]+');
                Route::get('get/{user}', 'EmailMgtController@getType');
                Route::get('welcome', 'EmailMgtController@getWelcome');
                Route::post('welcome', 'EmailMgtController@postWelcome');
                Route::get('template', 'EmailMgtController@getTemplates');
                Route::get('template/{template}/{action}', 'EmailMgtController@getTemplateAction');
                Route::post('template/{template}', 'EmailMgtController@postTemplate');
            });

            /* * * * * * * * *
             *   Statistics  *
             * * * * * * * * */
            Route::group(['prefix' => 'stats'], function () {
                Route::get('/', 'StatsController@getIndex');
                Route::get('details/{facility}', 'StatsController@getDetails');
                Route::get('export/details', 'StatsController@getExportDetails');
                Route::get('export/overview', 'StatsController@getExportOverview');
            });
        });
    });

    /* * * * * * *
     *  Errors  *
     * * * * * * */
    Route::get('404', function () {
        return View('errors.404');
    });
    Route::get('401', function () {
        return View('errors.401');
    });
    Route::get('500', function () {
        return View('errors.500');
    });
});