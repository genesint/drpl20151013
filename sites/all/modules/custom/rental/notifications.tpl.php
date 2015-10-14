<?php
?>
<script>
    jQuery(document).ready(function () {
        jQuery('#notifications').dataTable({
            "oLanguage": {
                "sProcessing": '<i class="fa fa-2x fa-spinner fa-spin"></i>'
            },
            "lengthMenu": [
                [500, 1000],
                [500, 1000]
            ],
            "processing": true,
            "serverSide": true,
            "searching": false,
            "aoColumnDefs": [
                {"bSearchable": false,  "bVisible": false,"bSortable": false, "aTargets": [0]},
                {"bSearchable": false, "bVisible": true, "bSortable": false,"aTargets": [1]},
                {"bSearchable": false, "bSortable": false, "aTargets": [2]},
                {"bSearchable": false, "bSortable": false, "aTargets": [3]}
            ],
            "sAjaxSource": "notifications-grid"
        });
    });
</script>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-3"></div>
    <div class="col-md-3">
        <div id='rental-tool'>
            <div class="btn-group">
                <a class="btn btn-primary" href="#"><i class="fa fa-list-ul fa-fw"></i>&nbsp;Actions</a>
                <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
                    <span class="fa fa-caret-down"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="notifications" ><i class="fa fa-refresh fa-fw"></i>Refresh</a></li>
                    <li><a href="tenants"><i class="fa fa-users fa-fw"></i>&nbsp;Tenants</a></li>
                    <li><a href="units"><i class="fa fa-users fa-fw"></i>&nbsp;Units</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>

<div id="dynamic" style="width:80%;margin-left:auto;margin-right:auto">
    <table class="display  table table-bordered" id="notifications" width="100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>Tenant</th>
            <th>Unit</th>
            <th>Mobile</th>
            <th># Days</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
        <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>
                <a href="javascript:window.print()"><i class="fa fa-print fa-fw"></i></a>
            </th>
        </tr>
        </tfoot>
    </table>
</div>




