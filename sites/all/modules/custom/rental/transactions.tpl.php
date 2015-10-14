<?php
#update transactions for changed bills
$query = "
          select
          a.nid, a.title, b.field_amount_value, c.field_amount_due_value from node a
          join field_data_field_amount b on a.nid=b.entity_id
          join field_data_field_amount_due c on a.title=c.entity_id
          where abs(cast(b.field_amount_value as signed integer) - cast(c.field_amount_due_value as signed integer))>0
";
$records = db_query($query);
foreach ($records as $record) {
    $trn = node_load($record->nid);
    $bln = node_load($record->title);
    $trn->field_amount['und']['0']['value'] = $bln->field_amount_due['und']['0']['value'];
    $particulars = 'Rent for ' . node_load($bln->field_unit['und']['0']['target_id'])->field_unit_name['und']['0']['value'];
    $particulars .= ": " . node_load($bln->field_unit['und']['0']['target_id'])->field_unit_code['und']['0']['value'];
    $date0 = new DateTime($bln->field_start_date['und']['0']['value']);
    $date1 = new DateTime($bln->field_stop_date['und']['0']['value']);
    $particulars .= ' from ' . $date0->format('d/m/Y') . ' to ' . $date1->format('d/m/Y');
    $trn->field_particulars['und']['0']['value'] = $particulars;
    node_save($trn);
}

#update transactions for changed incomes
$query = "
          select
          a.nid, a.title, b.field_amount_value, c.field_amount_paid_value from node a
          join field_data_field_amount b on a.nid=b.entity_id
          join field_data_field_amount_paid c on a.title=c.entity_id
          where abs(b.field_amount_value  + c.field_amount_paid_value)>0
";
$records = db_query($query);
foreach ($records as $record) {
    $trn = node_load($record->nid);
    $bln = node_load($record->title);
    $trn->field_amount['und']['0']['value'] = -$bln->field_amount_paid['und']['0']['value'];
    $particulars = $bln->field_particulars['und']['0']['value'] . ', REF:' . $bln->field_reference['und']['0']['value'];
    $trn->field_particulars['und']['0']['value'] = $particulars;
    node_save($trn);
}
#update transactions for changed expenses
$query = "
          select
          a.nid, a.title, b.field_amount_value, c.field_amount_spent_value from node a
          join field_data_field_amount b on a.nid=b.entity_id
          join field_data_field_amount_spent c on a.title=c.entity_id
          where abs(b.field_amount_value  - c.field_amount_spent_value)>0
";
$records = db_query($query);
foreach ($records as $record) {
    $trn = node_load($record->nid);
    $bln = node_load($record->title);
    $trn->field_amount['und']['0']['value'] = $bln->field_amount_spent['und']['0']['value'];
    $particulars = $bln->field_particulars['und']['0']['value'] . ', REF:' . $bln->field_reference['und']['0']['value'];
    $trn->field_particulars['und']['0']['value'] = $particulars;
    node_save($trn);
}
# Delete transactions which have been removed
$query = "select a.nid, a.title from node a left join node b on a.title=b.nid where a.type='transaction' and b.nid is null";
$records = db_query($query);
foreach ($records as $record) {
    $nid=$record->nid;
    node_delete($nid);
}
?>
<script>
    jQuery(document).ready(function () {
        jQuery('#transactions').dataTable({
            "oLanguage": {
                "sProcessing": '<i class="fa fa-2x fa-spinner fa-spin"></i>'
            },
            "lengthMenu": [
                [10, 25, 50, 100, 500, 1000],
                [10, 25, 50, 100, 500, 1000]
            ],
            "processing": true,
            "serverSide": true,
            "searching": false,
            "aoColumnDefs": [
                {"bSearchable": false, "bVisible": false, "aTargets": [0]},
                {"bSearchable": false, "bVisible": false, "aTargets": [1]},
                {"bSearchable": false, "bSortable": false, "aTargets": [2]},
                {"bSearchable": false, "bSortable": false, "aTargets": [3]},
                {"bSearchable": false, "bSortable": false, "aTargets": [4]},
                {"bSearchable": false, "bSortable": false, "aTargets": [5]},
                {"bSearchable": false, "bSortable": false, "aTargets": [6]},
                {"bSearchable": false, "bSortable": false, "aTargets": [7]}
            ],
            "sAjaxSource": "transactions-grid"
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
                    <li><a href="transactions"><i class="fa fa-refresh fa-fw"></i>Refresh</a></li>
                    <li><a href=""><i class="fa fa-file-excel-o fa-fw"></i>Export</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>

<div id="dynamic">
    <table class="display  table table-bordered" id="transactions" width="100%">
        <thead>
        <tr>
            <th>nid</th>
            <th>title</th>
            <th>Date</th>
            <th>Reference</th>
            <th>Account name</th>
            <th>Account type</th>
            <th>Particulars</th>
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
            <th></th>
            <th>
                <a href="javascript:window.print()"><i class="fa fa-print fa-fw"></i></a>
            </th>
        </tr>
        </tfoot>
    </table>
</div>




