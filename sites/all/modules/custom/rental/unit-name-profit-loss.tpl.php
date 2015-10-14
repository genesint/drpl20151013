<?php
$unit_group_id=$_GET['unit-group-id'];

# get account nid
$aid="";
if(!empty($_GET['unit-group-id'])){
    $query="select nid from node where type='account' and title='$unit_group_id'";
    $records = db_query($query);
    $aid=$records->fetchAll()[0]->nid;
}
$title="Profit and loss: ".node_load($unit_group_id)->title;
drupal_set_title($title);
$date = new DateTime();
$start_date = isset($_GET['start-date']) ? $_GET['start-date'] . " 00:00:00" : $date->format('Y-m-01 00:00:00');
$end_date = isset($_GET['end-date']) ? $_GET['end-date'] . " 23:59:59" : $date->format('Y-m-d 23:59:59');
$query = "select nid,title from node where type='unit_group' ";
$records = db_query($query);
?>
<script>
    jQuery(document).ready(function () {
        jQuery("#start-date").datepicker({ dateFormat: 'yy-mm-dd' });
        jQuery("#end-date").datepicker({ dateFormat: 'yy-mm-dd' });
        jQuery('#unit-name-profit-loss').dataTable({
            "oLanguage": {
                "sProcessing": '<i class="fa fa-2x fa-spinner fa-spin"></i>'
            },
            "bLengthChange": false,
            "fnServerParams": function (aoData) {
                aoData.push({"name": "unit-group-aid", "value": "<?php echo $aid;?>"});
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
            "sAjaxSource": "unit-name-profit-loss-grid"
        });
    });
</script>
<form role="form" class="row form-inline" action="unit-name-profit-loss" method="GET">
    <div class="col-md-3">
        <div class="form-group">
            <label for="start-date">Unit name:&nbsp;</label>
            <select class="form-control" name="unit-group-id" id="unit-group-id">
                <?php
                foreach ($records as $record) {
                    ?>
                    <option value="<?php echo $record->nid; ?>"><?php echo $record->title; ?></option>
                <?php
                }
                ?>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="start-date">From&nbsp;</label>
            <input type="text" class="form-control" id="start-date" name="start-date"
                   placeholder="<?php echo isset($_GET['start-date']) ? $_GET['start-date'] : $date->format('Y-m-01'); ?>">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="end-date">to&nbsp;</label>
            <input type="text" class="form-control" id="end-date" name="end-date"
                   placeholder="<?php echo isset($_GET['end-date']) ? $_GET['end-date'] : $date->format('Y-m-d'); ?>">
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <button type="submit" class="btn btn-default">Go</button>
        </div>
    </div>
</form>
<br>
<div id="dynamic">
    <table class="display  table table-bordered" id="unit-name-profit-loss" width="100%">
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
<div class="row">
    <div class="col-md-11"></div>
    <div class="col-md-1"><a href="javascript:window.print()"><i class="fa fa-print fa-fw"></i></a></div>
</div>
