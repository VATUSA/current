<div class="modal fade" id="view-training-record" tabindex="-1" role="dialog" aria-labelledby="View-Training-Record">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Training on <span class="training-position"><i>Loading...</i></span>
                    for <span
                        class="training-student"><i>Loading...</i></span></h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="text-center">
                        <div class="btn-group" id="v-modify-group" style="margin-bottom: 8px;">
                            <button class="btn btn-warning" id="tr-view-edit" data-id=""><span
                                    class="glyphicon glyphicon-pencil"></span> Edit
                            </button>
                            <button class="btn btn-danger" id="tr-view-delete" data-id=""><span
                                    class="glyphicon glyphicon-remove"></span> Delete
                            </button>
                        </div>
                    </div>
                    <table class="table table-striped table-responsive training-info">
                        <tbody>
                        <tr>
                            <td>Position</td>
                            <td><span id="training-artcc">Seattle ARTCC</span> - <span
                                    class="training-position"></span></td>
                        </tr>
                        <tr>
                            <td>Progress</td>
                            <td id="training-score">
                                @php $score = rand(1,5); @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    <span
                                        class="glyphicon @if($i > $score) glyphicon-star-empty @else glyphicon-star @endif"></span>
                                    &nbsp;
                                @endfor
                                <br>
                            </td>
                        </tr>
                        <tr>
                            <td>Date and Time</td>
                            <td id="training-datetime"></td>
                        </tr>
                        <tr>
                            <td>Duration</td>
                            <td id="training-duration"></td>
                        </tr>
                        <tr>
                            <td>Number of Movements</td>
                            <td id="training-movements"></td>
                        </tr>
                        <tr>
                            <td>Location</td>
                            <td id="training-location"></td>
                        </tr>
                        <tr>
                            <td>Instructor</td>
                            <td id="training-instructor"></td>
                        </tr>
                        <tr id="training-ots-exam">
                            <td>OTS Exam</td>
                            <td><span class="label label-danger" id="training-ots-exam-fail" rel="tooltip"><span
                                        class="glyphicon glyphicon-remove"></span> Fail</span>
                                <span class="label label-info" id="training-ots-exam-rec"><span
                                        class="glyphicon glyphicon-info-sign"></span> Recommended</span>
                                <span class="label label-success" id="training-ots-exam-pass" rel="tooltip"><span
                                        class="glyphicon glyphicon-ok"></span> Pass</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" id="training-notes">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="edit-training-record" tabindex="-1" role="dialog" aria-labelledby="Edit-Training-Record">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Edit Training Record for <span
                        id="e-training-student"></span> on <span
                        class="e-training-position"></span></h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="text-center">
                        <div class="btn-group" style="margin-bottom: 8px;">
                            <button class="btn btn-danger tr-modal-delete" data-id="" id="tr-edit-delete"><span
                                    class="glyphicon glyphicon-remove"></span> Delete
                            </button>
                        </div>
                    </div>
                    <form class="training-info" id="edit-tr-form" method="post">
                        <table id="edit-tr-layout" class="table table-striped table-responsive tr-modal-layout">
                            <tbody>
                            <tr>
                                <td><label for="e-training-position">Position</label></td>
                                <td><p class="form-control-static" id="e-training-artcc"></p> - <input
                                        class="form-control e-training-position training-position"
                                        type="text"
                                        name="position"
                                        id="e-training-position"
                                        placeholder="ex. SEA_APP"
                                        required autocomplete="off">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="e-training-score">Progress</label></td>
                                <td>
                                    <select class="form-control training-score" name="score" id="e-training-score"
                                            required
                                            autocomplete="off">
                                        <option value="0">-- Select One --</option>
                                        <option value="1">1 - No Progress</option>
                                        <option value="2">2 - Little Progress</option>
                                        <option value="3">3 - Average Progress</option>
                                        <option value="4">4 - Great Progress</option>
                                        <option value="5">5 - Exceptional Progress</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="e-training-datetime">Date and Time (UTC)</label></td>
                                <td><input class="form-control training-datetime" type="text" name="session_date"
                                           id="e-training-datetime"
                                           required autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td><label for="e-training-duration-hrs">Duration</label></td>
                                <td><input class="form-control training-duration" type="number" name="duration-hours"
                                           id="e-training-duration-hrs" min="0" autocomplete="off">:<input
                                        class="form-control training-duration" type="number"
                                        name="duration-mins"
                                        id="e-training-duration-mins" step="15" min="0" max="45" autocomplete="off">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="e-training-movements">Number of Movements</label></td>
                                <td><input class="form-control training-movements" type="number" name="movements"
                                           id="e-training-movements"
                                           placeholder="ex. 8"
                                           required autocomplete="off">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="e-training-location">Location</label></td>
                                <td><select class="form-control training-location" name="location"
                                            id="e-training-location" required
                                            autocomplete="off">
                                        <option value="-1">-- Select One --</option>
                                        <option value="0">Classroom</option>
                                        <option value="1">Live</option>
                                        <option value="2">Sweatbox</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="e-training-instructor">Instructor</label></td>
                                <td>
                                    <!--<select class="form-control" name="instructor" id="e-training-instructor" required autocomplete="off">
                                        <option value="">-- Select One --</option>
                                    </select>
                                    -->
                                    <p class="form-control-static" id="e-training-instructor"></p>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="e-training-ots-grp">OTS Exam</label></td>
                                <td>
                                    <div class="btn-group" data-toggle="buttons" id="e-training-ots-grp">
                                        <label class="btn btn-default active ots-status-input-label">
                                            <input type="radio" name="ots_status" id="e-ots-status-0" value="0"
                                                   autocomplete="off" class="ots-status-input" checked>
                                            No OTS
                                        </label>
                                        <label class="btn btn-default ots-status-input-label">
                                            <input type="radio" name="ots_status" id="e-ots-status-1" value="1"
                                                   class="ots-status-input" autocomplete="off"><span
                                                class="glyphicon glyphicon-ok"></span> Pass
                                        </label>
                                        <label class="btn btn-default ots-status-input-label">
                                            <input type="radio" name="ots_status" id="e-ots-status-2" value="2"
                                                   class="ots-status-input" autocomplete="off"><span
                                                class="glyphicon glyphicon-remove"></span> Fail
                                        </label>
                                        <label class="btn btn-default ots-status-input-label">
                                            <input type="radio" name="ots_status" id="e-ots-status-3" value="3"
                                                   class="ots-status-input" autocomplete="off"><span
                                                class="glyphicon glyphicon-info-sign"></span> Recommend
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <label for="e-training-notes" class="text-center" style="display: block;">Training
                                        Notes</label>
                                    <textarea class="form-control training-notes" name="notes" id="e-training-notes"
                                              required autocomplete="off"></textarea>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-id="" id="e-training-submit"><span
                            class="glyphicon glyphicon-ok"></span> Submit
                    </button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal"><span
                            class="glyphicon glyphicon-remove"></span> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="new-training-record" tabindex="-1" role="dialog" aria-labelledby="New-Training-Record">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Create Training Record for <span
                        id="n-training-student">{{ $user->fullname() }}</span></h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form class="training-info" id="new-tr-form" method="post">
                        <table id="new-tr-layout" class="table table-striped table-responsive tr-modal-layout">
                            <tbody>
                            <tr>
                                <td><label for="n-training-position">Position</label></td>
                                <td><p class="form-control-static" id="n-training-artcc">{{ $trainingfacname }}</p> -
                                    <input
                                        class="form-control n-training-position training-position"
                                        type="text"
                                        name="position"
                                        id="n-training-position"
                                        placeholder="ex. SEA_APP"
                                        required autocomplete="off">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="e-training-score">Progress</label></td>
                                <td>
                                    <select class="form-control training-score" name="score" id="n-training-score"
                                            required
                                            autocomplete="off">
                                        <option value="0">-- Select One --</option>
                                        <option value="1">1 - No Progress</option>
                                        <option value="2">2 - Little Progress</option>
                                        <option value="3">3 - Average Progress</option>
                                        <option value="4">4 - Great Progress</option>
                                        <option value="5">5 - Exceptional Progress</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="n-training-datetime">Date and Time (UTC)</label></td>
                                <td><input class="form-control training-datetime" type="text" name="session_date"
                                           id="n-training-datetime"
                                           placeholder="____-__-__ __:__"
                                           required autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td><label for="n-training-duration-hrs">Duration</label></td>
                                <td><input class="form-control training-duration" type="number" name="duration-hours"
                                           id="n-training-duration-hrs" min="0" autocomplete="off">:<input
                                        class="form-control training-duration" type="number"
                                        name="duration-mins"
                                        id="n-training-duration-mins" step="15" min="0" max="45" autocomplete="off">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="n-training-movements">Number of Movements</label></td>
                                <td><input class="form-control training-movements" type="number" name="movements"
                                           id="n-training-movements"
                                           placeholder="ex. 8"
                                           required autocomplete="off">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="n-training-location">Location</label></td>
                                <td><select class="form-control training-location" name="location"
                                            id="n-training-location" required
                                            autocomplete="off">
                                        <option value="-1">-- Select One --</option>
                                        <option value="0">Classroom</option>
                                        <option value="1">Live</option>
                                        <option value="2">Sweatbox</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="n-training-instructor">Instructor/Mentor</label></td>
                                <td>
                                    @if(\App\Classes\RoleHelper::isFacilitySeniorStaff(Auth::user()->cid, Auth::user()->facility, false, false))
                                        <select class="form-control" name="instructor" id="n-training-instructor"
                                                required
                                                autocomplete="off">
                                            <option value="">-- Select One --</option>
                                            @foreach($ins as $type => $users)
                                                <optgroup label="{{ $type === "ins" ? "Instructors" : "Mentors" }}">
                                                    @foreach($users as $cid => $name)
                                                        <option value="{{ $cid }}">{{ $name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    @else
                                        <p class="form-control-static">{{ Auth::user()->fullname() }}</p>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><label for="e-training-ots-grp">OTS Exam</label></td>
                                <td>
                                    <div class="btn-group" data-toggle="buttons" id="n-training-ots-grp">
                                        <label class="btn btn-default active ots-status-input-label">
                                            <input type="radio" name="ots_status" id="n-ots-status-0" value="0"
                                                   autocomplete="off" class="ots-status-input" checked>
                                            No OTS
                                        </label>
                                        <label class="btn btn-default ots-status-input-label">
                                            <input type="radio" name="options" id="n-ots-status-1" value="1"
                                                   class="ots-status-input" autocomplete="off"><span
                                                class="glyphicon glyphicon-ok"></span> Pass
                                        </label>
                                        <label class="btn btn-default ots-status-input-label">
                                            <input type="radio" name="options" id="n-ots-status-2" value="2"
                                                   class="ots-status-input" autocomplete="off"><span
                                                class="glyphicon glyphicon-remove"></span> Fail
                                        </label>
                                        <label class="btn btn-default ots-status-input-label">
                                            <input type="radio" name="options" id="n-ots-status-3" value="3"
                                                   class="ots-status-input" autocomplete="off"><span
                                                class="glyphicon glyphicon-info-sign"></span> Recommend
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <label for="n-training-notes" class="text-center" style="display: block;">Training
                                        Notes</label>
                                    <textarea class="form-control training-notes" name="notes" id="n-training-notes"
                                              required autocomplete="off"></textarea>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-id="" id="n-training-submit"><span
                            class="glyphicon glyphicon-ok"></span> Submit
                    </button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal"><span
                            class="glyphicon glyphicon-remove"></span> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>