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
                        <div class="btn-group" style="margin-bottom: 8px;">
                            <button class="btn btn-warning" id="tr-view-edit" data-id=""><span
                                    class="glyphicon glyphicon-pencil"></span> Edit
                            </button>
                            <button class="btn btn-danger tr-modal-delete" data-id=""><span
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
                            <td>Score</td>
                            <td id="training-score">
                                @for($i = 1; $i <= 5; $i++)
                                    <span
                                        class="glyphicon @if($i > $score) glyphicon-star-empty @else glyphicon-star @endif"></span>
                                    &nbsp;
                                @endfor
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
                <h4 class="modal-title" id="myModalLabel">Edit Training Record for <span
                        id="e-training-student"></span> on <span
                            class="e-training-position"></span></h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="text-center">
                        <div class="btn-group" style="margin-bottom: 8px;">
                            <button class="btn btn-danger tr-modal-delete" data-id=""><span
                                    class="glyphicon glyphicon-remove"></span> Delete
                            </button>
                        </div>
                    </div>
                    <form class="training-info" id="edit-tr-form" method="post">
                        <table id="edit-tr-layout" class="table table-striped table-responsive">
                            <tbody>
                            <tr>
                                <td><label for="e-training-position">Position</label></td>
                                <td><p class="form-control-static" id="e-training-artcc"></p> - <input class="form-control e-training-position"
                                                                                            type="text"
                                                                                            name="position"
                                                                                            placeholder="ex. SEA_APP"
                                                                                            required autocomplete="off">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="e-training-score">Progress</label></td>
                                <td>
                                    <select class="form-control" name="score" id="e-training-score" required autocomplete="off">
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
                                <td><input class="form-control" type="text" name="datetime" id="e-training-datetime"
                                           required autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td><label for="e-training-duration-hrs">Duration</label></td>
                                <td><input class="form-control" type="number" name="duration"
                                           id="e-training-duration-hrs" min="0" autocomplete="off">:<input class="form-control" type="number"
                                                                                name="duration"
                                                                                id="e-training-duration-mins" step="15" min="0" max="45" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td><label for="e-training-movements">Number of Movements</label></td>
                                <td><input class="form-control" type="number" name="movements" id="e-training-movements" placeholder="ex. 8"
                                           required autocomplete="off">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="e-training-location">Location</label></td>
                                <td><select class="form-control" name="location" id="e-training-location" required autocomplete="off">
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
                                <td colspan="2">
                                    <label for="e-training-notes" class="text-center" style="display: block;">Training Notes</label>
                                    <textarea class="form-control" name="notes" id="e-training-notes"
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