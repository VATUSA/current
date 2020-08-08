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
                            <td><span class="label label-danger" id="training-ots-exam-fail"><span
                                        class="glyphicon glyphicon-remove"></span> Fail</span>
                                <span class="label label-info" id="training-ots-exam-rec"><span
                                        class="glyphicon glyphicon-info-sign"></span> Recommended</span>
                                <span class="label label-success" id="training-ots-exam-pass"><span
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