@push('styles')
    <link rel="stylesheet" type="text/css" href="/datatables/datatables.min.css"/>
@endpush

<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">Training Records</h3></div>
    <div class="panel-body">
        <div class="col-md-4" style="border-right: 1px solid #ccc;">
            <form class="form-inline">
                <div class="form-group">
                    <label for="tng-artcc-select">ARTCC:</label>
                    <select class="form-control" id="tng-artcc-select">
                        <option value="">-- Select One --</option>
                        <option value="2">Seattle ARTCC</option>
                    </select>
                </div>
            </form>
            <BR>
            <ul class="nav nav-pills nav-stacked" role="tablist" id="pos-types">
                <li role="presentation" class="active"><a href="#training" data-controls="all" aria-controls="all"
                                                          role="tab"
                                                          data-toggle="pill"><i
                            class="fa fa-list"></i> All Records</a></li>
                <li role="presentation"><a href="#training" data-controls="del" aria-controls="del" role="tab"
                                           data-toggle="pill">Clearance
                        Delivery</a></li>
                <li role="presentation"><a href="#training" data-controls="gnd" aria-controls="gnd" role="tab"
                                           data-toggle="pill">Ground</a></li>
                <li role="presentation"><a href="#training" data-controls="twr" aria-controls="twr" role="tab"
                                           data-toggle="pill">Tower</a></li>
                <li role="presentation"><a href="#training" data-controls="app" aria-controls="app" role="tab"
                                           data-toggle="pill">Approach</a>
                </li>
                <li role="presentation"><a href="#training" data-controls="ctr" aria-controls="ctr" role="tab"
                                           data-toggle="pill">Center</a></li>
            </ul>
        </div>
        <div class="col-md-8" id="training-content">
            <div class="tab-content">
                <!-- Filters: Major/Minor | Sweatbox/Live | OTS -->
                @php $postypes = ['DEL', 'GND', 'TWR', 'APP', 'CTR']; @endphp
                <div role="tabpanel" class="tab-pane active" id="all">All!</div>
                @foreach($postypes as $postype)
                    <div role="tabpanel" class="tab-pane" id="{{strtolower($postype)}}">{{$postype}}!</div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script type="text/javascript" src="/datatables/datatables.min.js"></script>
    <script>$(function () {
        $('#pos-types li a').click(function (e) {
          e.preventDefault()
          let target = $(this).data('controls')
          $('#training-content div[role="tabpanel"]#' + target).show()
          $('#training-content div[role="tabpanel"]:not(#' + target + ')').hide()
        })
      })
    </script>
@endpush