<?php
$uid = $_GET['uid'];
# get account nid
$query = "select nid from node where type='account' and title='$uid'";
$records = db_query($query);
$nid = $records->fetchAll()[0]->nid;


$title="Unit profit/loss: ".node_load($uid)->field_unit_name['und']['0']['value']."/".node_load($uid)->field_unit_code['und']['0']['value'];
drupal_set_title($title);
$date = new DateTime();
$start_date = isset($_GET['start-date']) ? $_GET['start-date'] . " 00:00:00" : $date->format('Y-m-01 00:00:00');
$end_date = isset($_GET['end-date']) ? $_GET['end-date'] . " 23:59:59" : $date->format('Y-m-d 23:59:59');
?>
<script>
    jQuery(document).ready(function () {
        jQuery("#start-date").datepicker({ dateFormat: 'yy-mm-dd' });
        jQuery("#end-date").datepicker({ dateFormat: 'yy-mm-dd' });
        jQuery('#unit-profit-loss').dataTable({
            "oLanguage": {
                "sProcessing": '<i class="fa fa-2x fa-spinner fa-spin"></i>'
            },
            "bLengthChange": false,
            "fnServerParams": function (aoData) {
                aoData.push({"name": "nid", "value": <?php echo $nid;?>});
                aoData.push({"name": "uid", "value": <?php echo $uid;?>});
                aoData.push({"name": "start-date", "value": "<?php echo $start_date;?>"});
                aoData.push({"name": "end-date", "value": "<?php echo $end_date;?>"});
            },
            "processing": true,
            "serverSide": true,
            "searching": false,
            "bInfo": false,
            "aoColumnDefs": [
                {"bSearchable": false, "bVisible": false, "aTargets": [0]},
                {"bSearchable": false, "bVisible": false, "aTargets": [1]},
                {"bSearchable": false, "bSortable": false, "aTargets": [2]},
                {"bSearchable": false, "bSortable": false, "aTargets": [3]},
                {"bSearchable": false, "bSortable": false, "aTargets": [4]},
                {"bSearchable": false, "bSortable": false, "aTargets": [5]},
                {"bSearchable": false, "bSortable": false, "aTargets": [6]}
            ],
            "sAjaxSource": "unit-profit-loss-grid"
        });
    });
    function refresh_click() {
        var start_date = jQuery("#start-date").val();
        var end_date = jQuery("#end-date").val();
        if (start_date != "" && end_date != "") {
            var url = "unit-profit-loss?uid=<?php echo $uid;?>&start-date=" + start_date + "&end-date=" + end_date;
            window.location = url;
        }
        else {
            if(start_date==''){
                jQuery("#start-date").attr('placeholder', '');
                alert("Start date is empty");

            }
            if(end_date==''){
                jQuery("#end-date").attr('placeholder', '');
                alert("End date is empty");

            }
        }
    }
</script>
<form role="form" class="row form-inline">
    <div class="col-md-3">
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="start-date">From&nbsp;</label>
            <input type="text" class="form-control" id="start-date"
                   placeholder="<?php echo isset($_GET['start-date']) ? $_GET['start-date'] : $date->format('Y-m-01'); ?>">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="end-date">to&nbsp;</label>
            <input type="text" class="form-control" id="end-date"
                   placeholder="<?php echo isset($_GET['end-date']) ? $_GET['end-date'] : $date->format('Y-m-d'); ?>">
        </div>
    </div>
    <div class="col-md-3">
        <div id='rental-tool'>
            <div class="btn-group">
                <a class="btn btn-primary" href="#"><i class="fa fa-tasks fa-fw"></i>&nbsp;Actions</a>
                <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
                    <span class="fa fa-caret-down"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="#" onclick="refresh_click();"><i class="fa fa-refresh fa-fw"></i>Refresh</a></li>
                    <li><a href='unit-history?nid=<?php echo $uid;?>'><i class='fa fa-history fa-fw'></i>&nbsp;Rental history</a></li>
                    <li><a href='unit-expenses?nid=<?php echo $uid;?>'><i class='fa fa-history fa-fw'></i>&nbsp;Expense history</a></li>
                    <li><a href="tenants"><i class="fa fa-users fa-fw"></i>&nbsp;Tenants</a></li>
                    <li><a href="units"><i class="fa fa-home fa-fw"></i>&nbsp;Units</a></li>
                </ul>
            </div>
        </div>
    </div>
</form>
<br>
<div id="dynamic">
    <table class="display  table table-bordered" id="unit-profit-loss" width="100%">
        <thead>
        <tr>
            <th>nid</th>
            <th>title</th>
            <th>Date</th>
            <th>Reference</th>
            <th>Particulars</th>
            <th>Account name (Account type)</th>
            <th>Amount</th>
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

