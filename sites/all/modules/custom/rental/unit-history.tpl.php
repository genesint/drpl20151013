<?php
$nid=$_GET['nid'];
$node=node_load($nid);
$title="Unit history: ".$node->field_unit_name['und']['0']['value'].": ".$node->field_unit_code['und']['0']['value'];
drupal_set_title($title);
?>
<script>
    jQuery(document).ready(function () {
        jQuery('#unit-history').dataTable({
            "oLanguage": {
                "sProcessing": '<i class="fa fa-2x fa-spinner fa-spin"></i>'
            },
            "lengthMenu": [
                [10, 25, 50, 100, 500, 1000],
                [10, 25, 50, 100, 500, 1000]
            ],
            "fnServerParams": function (aoData) {
                aoData.push({"name": "nid", "value": "<?php echo $nid;?>"});
            },
            "processing": true,
            "serverSide": true,
            "searching": false,
            "aoColumnDefs": [
                {"bSearchable": false, "bVisible": false, "aTargets": [0]},
                {"bSearchable": false, "bSortable": false, "aTargets": [1]},
                {"bSearchable": false, "bSortable": false, "aTargets": [2]},
                {"bSearchable": false, "bSortable": false, "aTargets": [3]},
                {"bSearchable": false, "bSortable": false, "aTargets": [4]},
                {"bSearchable": false, "bSortable": false, "aTargets": [5]},
                {"bSearchable": false, "bSortable": false, "aTargets": [6]},
                {"bSearchable": false, "bSortable": false, "aTargets": [7]},
                {"bSearchable": false, "bSortable": false, "aTargets": [8]},
                {"bSearchable": false, "bSortable": false, "aTargets": [9]}
            ],
            "sAjaxSource": "unit-history-grid"
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
                    <li><a href="unit-history?nid=<?php echo $nid;?>"><i class="fa fa-refresh fa-fw"></i>&nbsp;Refresh</a></li>
                    <li><a href='unit-expenses?nid=<?php echo $nid;?>'><i class='fa fa-history fa-fw'></i>&nbsp;Expense history</a></li>
                    <li><a href='unit-profit-loss?uid=<?php echo $nid;?>'><i class='fa fa-question-circle fa-fw'></i>&nbsp;Profit & Loss</a></li>
                    <li><a href="units"><i class="fa fa-building fa-fw"></i>&nbsp;Units</a></li>
                    <li><a href="tenants"><i class="fa fa-users fa-fw"></i>&nbsp;Tenants</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>

<div id="dynamic">
    <table class="display  table table-bordered" id="unit-history" width="100%">
        <thead>
        <tr>
            <th>nid</th>
            <th>Invoice #</th>
            <th>Date</th>
            <th>Account</th>
            <th>Unit name/code</th>
            <th>Start date</th>
            <th>Stop date</th>
            <th>Amount</th>
            <th><i class="fa fa-pencil-square-o fa-fw"></i></th>
            <th><i class="fa fa-print fa-fw"></i></th>
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




