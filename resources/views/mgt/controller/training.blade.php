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
            <ul class="nav nav-pills nav-stacked" role="tablist">
                <li role="presentation" class="active"><a href="#" aria-controls="all" role="tab" data-toggle="pill"><i
                            class="fa fa-list"></i> All Records</a></li>
                <li role="presentation"><a href="#" aria-controls="del" role="tab" data-toggle="pill">Clearance
                        Delivery</a></li>
                <li role="presentation"><a href="#" aria-controls="gnd" role="tab" data-toggle="pill">Ground</a></li>
                <li role="presentation"><a href="#" aria-controls="twr" role="tab" data-toggle="pill">Tower</a></li>
                <li role="presentation"><a href="#" aria-controls="app" role="tab" data-toggle="pill">Approach</a></li>
                <li role="presentation"><a href="#" aria-controls="ctr" role="tab" data-toggle="pill">Center</a></li>
            </ul>
        </div>
        <div class="col-md-8">
            <div class="tab-content">
                <!-- Filters: Major/Minor | Sweatbox/Live | OTS -->
                <div role="tabpanel" class="tab-pane active" id="all">All!</div>
                <div role="tabpanel" class="tab-pane" id="del">Delivery!</div>
            </div>
        </div>
    </div>
</div>
