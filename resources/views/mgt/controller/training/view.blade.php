<div class="modal fade" id="view-training-record" tabindex="-1" role="dialog" aria-labelledby="View-Training-Record">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Training on <span class="training-position"><i>Loading...</i></span> for <span
                            class="training-student"><i>Loading...</i></span></h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <table id="training-info" class="table table-striped table-responsive">
                        <tbody>
                        <tr>
                            <td>Position</td>
                            <td><span id="training-artcc">Seattle ARTCC</span> - <span class="training-position">SEA_APP</span></td>
                        </tr>
                        <tr>
                            <td>Score</td>
                            <td id="training-score">
                                @php $score = 4; @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    <span
                                        class="glyphicon @if($i > $score) glyphicon-star-empty @else glyphicon-star @endif"></span>
                                    &nbsp;
                                @endfor
                            </td>
                        </tr>
                        <tr>
                            <td>Date and Time</td>
                            <td id="training-datetime">January 8, 2001</td>
                        </tr>
                        <tr>
                            <td>Duration</td>
                            <td>1 hour 30 minutes</td>
                        </tr>
                        <tr>
                            <td>Number of Movements</td>
                            <td>45</td>
                        </tr>
                        <tr>
                            <td>Location</td>
                            <td>Sweatbox</td>
                        </tr>
                        <tr>
                            <td>Instructor</td>
                            <td>Aaron Schwartz</td>
                        </tr>
                        <tr>
                            <td colspan="2">

                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed eu tempor nibh, quis
                                aliquam nunc. Pellentesque hendrerit at nisi volutpat rhoncus. Class aptent taciti
                                sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. In orci lacus,
                                hendrerit in ultricies quis, vulputate sed lectus. Donec felis risus, fringilla in
                                accumsan tempor, porta eget diam. Duis et libero ante. Phasellus auctor condimentum
                                tortor sit amet elementum. Morbi iaculis ligula felis, quis suscipit justo aliquet sit
                                amet. Quisque molestie, diam at auctor tempor, metus nibh porttitor dui, nec aliquet
                                dolor urna vitae dolor. Nam justo justo, feugiat vitae euismod sit amet, fermentum et
                                enim. Vivamus sit amet lectus lectus. In sollicitudin libero ac massa facilisis
                                bibendum. Nullam ex erat, mollis vitae pharetra porta, sagittis pulvinar turpis.
                                Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;
                                Curabitur vitae tincidunt odio. Praesent in risus euismod, faucibus tortor lacinia,
                                lobortis tortor.

                                Nulla faucibus est in nulla tincidunt hendrerit. Vivamus a orci urna. Pellentesque
                                venenatis tempor enim. Nunc blandit elit eget consequat tristique. Aliquam mollis, dolor
                                at dapibus egestas, nulla libero rutrum ligula, nec suscipit enim elit in dui. Ut
                                pharetra justo vel libero elementum fermentum. Sed non varius nisi. Curabitur hendrerit
                                id lacus at porta. In hac habitasse platea dictumst. Suspendisse elementum arcu aliquam
                                felis pulvinar, non varius leo dictum. Vestibulum tempor eros eget pharetra porta.
                                Pellentesque vel tortor ultrices, volutpat tellus et, imperdiet est.

                                Suspendisse faucibus purus vel tellus lacinia, vel tincidunt purus luctus. Nulla
                                facilisi. Nulla vitae neque turpis. Etiam varius tincidunt erat, eget vehicula tellus
                                pharetra eu. Fusce vel sem fringilla magna semper lobortis at eu tortor. Praesent sed
                                lacinia est. Cras scelerisque nec enim et rutrum. Mauris diam augue, interdum a dapibus
                                eu, elementum eu dui. Suspendisse hendrerit faucibus consequat. Nam eleifend diam vel
                                erat facilisis ornare. Sed nec urna non nunc fermentum dictum. Morbi lobortis velit non
                                diam lobortis auctor id in neque.

                                Etiam finibus lacus eu dictum aliquam. Nam sed est nec ligula blandit facilisis non nec
                                diam. In eget venenatis sapien. Cras euismod eleifend hendrerit. Nullam in urna sed leo
                                dignissim lobortis. Integer ut arcu non nunc molestie gravida nec quis lorem. Sed
                                scelerisque odio eget maximus vulputate. Curabitur pharetra ipsum ut dignissim congue.
                                Vestibulum dignissim blandit interdum. Vestibulum ante ipsum primis in faucibus orci
                                luctus et ultrices posuere cubilia Curae; Nullam egestas eu erat at tempus. In hac
                                habitasse platea dictumst.

                                Vestibulum tellus mauris, rhoncus id lobortis et, ultricies ullamcorper nibh. Nam
                                rhoncus, urna id efficitur rutrum, massa erat venenatis urna, vitae fermentum sem nisl
                                vel magna. Sed tincidunt tellus mauris, vel tincidunt purus molestie quis. Fusce sodales
                                diam consequat ipsum malesuada, vel rhoncus mauris euismod. Morbi sit amet libero sit
                                amet magna egestas vulputate a id augue. Aliquam et vehicula lacus. Nam ullamcorper
                                posuere ligula dictum luctus. Quisque sagittis porta venenatis. Mauris ut mattis libero,
                                suscipit elementum risus. Donec commodo tellus arcu, ac imperdiet enim auctor in.
                                Vivamus id massa venenatis, pulvinar lorem id, scelerisque orci. Donec volutpat congue
                                odio, sed semper dolor ornare tempus. Aliquam erat volutpat. Nullam tincidunt tellus
                                urna, ut lacinia mi ornare volutpat.
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