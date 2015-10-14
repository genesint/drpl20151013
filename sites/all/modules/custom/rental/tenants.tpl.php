<?php
module_load_include('inc', 'rental', 'common');
# create accounts
$query = "
select a.nid, b.title from
(select nid from node where type='tenant') a
left join
(select title from node where type='account') b
on
a.nid=b.title
where b.title is null;
";
$records = db_query($query);
foreach ($records as $record) {
    $node=node_load($record->nid);
    #$title = $node->field_full_name['und'][0]['value']."(".$record->nid.")";
    $title=$record->nid;
    $type = 'account';
    $values=array(
        'field_account_name'=>array('value',$node->field_full_name['und'][0]['value']),
        'field_account_type'=>array('target_id',222),
        'field_particulars'=>array('value','Account details for '.$node->field_full_name['und'][0]['value'])
    );
    createNode($title,$type,$values);
}
# Delete transactions which have been removed
$query = "select a.nid, a.title from node a left join node b on a.title=b.nid where a.type='transaction' and b.nid is null";
$records = db_query($query);
foreach ($records as $record) {
    $nid=$record->nid;
    node_delete($nid);
}
#income transactions
$query = "
select a.nid, b.title from
(select nid from node where type='income') a
left join
(select title from node where type='transaction') b
on
a.nid=b.title
where b.title is null
";
$records = db_query($query);
foreach ($records as $record) {
    $node=node_load($record->nid);
    $title=$record->nid;
    $type = 'transaction';
    $values=array(
        'field_account'=>array('target_id',$node->field_account['und']['0']['target_id']),
        'field_particulars'=>array('value',$node->field_particulars['und']['0']['value'].', REF:'.$node->field_reference['und']['0']['value']),

        'field_transaction_reference' => array('value', 'I'.$record->nid),
        'field_amount'=>array('value',$node->field_amount_paid['und']['0']['value']*-1)
    );
    createNode($title,$type,$values);
}
#billing transactions
$query = "
select a.nid, b.title from
(select nid from node where type='billing') a
left join
(select title from node where type='transaction') b
on
a.nid=b.title
where b.title is null
";
$records = db_query($query);
foreach ($records as $record) {
    $node = node_load($record->nid);
    $title = $record->nid;
    $type = 'transaction';
    $particulars = 'Rent for ' . node_load($node->field_unit['und']['0']['target_id'])->field_unit_name['und']['0']['value'];
    $particulars .=": ".node_load($node->field_unit['und']['0']['target_id'])->field_unit_code['und']['0']['value'];
    $date0 = new DateTime($node->field_start_date['und']['0']['value']);
    $date1 = new DateTime($node->field_stop_date['und']['0']['value']);
    $particulars .= ' from ' . $date0->format('d/m/Y') . ' to ' . $date1->format('d/m/Y');
    $values = array(
        'field_account' => array('target_id', $node->field_account['und']['0']['target_id']),
        'field_particulars' => array('value', $particulars),
        'field_transaction_reference' => array('value', 'B'.$record->nid),
        'field_amount' => array('value', $node->field_amount_due['und']['0']['value'])
    );
    createNode($title, $type, $values);
}

# generate invoice #
$query = "
select nid from node where type='billing' and title=''
";
$records = db_query($query);
foreach ($records as $record) {
    $node = node_load($record->nid);
    $node->title='B'.$record->nid;
    node_save($node);
}
# generate receipt #
$query = "
select nid from node where type='income' and title=''
";
$records = db_query($query);
foreach ($records as $record) {
    $node = node_load($record->nid);
    $node->title='I'.$record->nid;
    node_save($node);
}
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
                {"bSearchable": false, "bSortable": false, "aTargets": [7]},
                {"bSearchable": false, "bSortable": false, "aTargets": [8]}
            ],
            "sAjaxSource": "tenants-grid"
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
                    <li><a href="#overlay=node/add/tenant" ><i class="fa fa-plus fa-fw"></i>&nbsp;Add tenant</a></li>
                    <li><a href="tenants" ><i class="fa fa-refresh fa-fw"></i>&nbsp;Refresh</a></li>
                    <li><a href="export-income?grid=income"><i class="fa fa-download fa-fw"></i>&nbsp;Payments</a></li>
                    <li><a href="export-rent?grid=billing"><i class="fa fa-download fa-fw"></i>&nbsp;Rent</a></li>
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
            <th>History</th>
            <th>Actions</th>
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
            <th>
                <a href="javascript:window.print()"><i class="fa fa-print fa-fw"></i></a>
            </th>
        </tr>
        </tfoot>
    </table>
</div>




