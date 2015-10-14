<?php
module_load_include('inc', 'rental', 'common');

?>
<script>
    jQuery(document).ready(function () {
        jQuery('#tenants').dataTable({
            "oLanguage": {
                "sProcessing": '<i class="fa fa-2x fa-spinner fa-spin"></i>'
            },
            "lengthMenu": [
                [10, 25, 50, 100, 500, 1000],
                [10, 25, 50, 100, 500, 1000]
            ],
            "processing": true,
            "serverSide": true,
            "searching": true,
            "aoColumnDefs": [
                {"bSearchable": false, "bSortable": false, "aTargets": [0]},
                {"bSearchable": false, "bVisible": false, "aTargets": [1]},
                {"bSearchable": false, "bSortable": false, "aTargets": [2]},
                {"bSearchable": false, "bSortable": false, "aTargets": [3]},
                {"bSearchable": false, "bSortable": false, "aTargets": [4]},
                {"bSearchable": false, "bSortable": false, "aTargets": [5]},
                {"bSearchable": false, "bSortable": false, "aTargets": [6]},
            ],
            "sAjaxSource": "debtors-grid"
        });
    });
</script>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-3"></div>
    <div class="col-md-3">
        <div id='rental-tool'>
            <div class="btn-group">
                <a class="btn btn-primary" href="#"><i class="fa fa-tasks fa-fw"></i>&nbsp;Actions</a>
                <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
                    <span class="fa fa-caret-down"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="debtors" ><i class="fa fa-refresh fa-fw"></i>&nbsp;Refresh</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-3">

    </div>
</div>

<div id="dynamic">
    <table class="display  table table-bordered" id="tenants" width="100%">
        <thead>
        <tr>
            <th>Tenant ID</th>
            <th>Title</th>
            <th>Full name</th>
            <th>Origin</th>
            <th>Mobile number</th>
            <th># members</th>
            <th>Outstanding amount</th>
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
            <th></th>
            <th></th>
            <th>
                <a href="javascript:window.print()"><i class="fa fa-print fa-fw"></i></a>
            </th>
        </tr>
        </tfoot>
    </table>
</div>




