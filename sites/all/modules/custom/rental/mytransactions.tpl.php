<?php
module_load_include('inc', 'rental', 'common');
$nid=empty($_GET['nid'])?"":$_GET['nid'];
$tnid=empty($_GET['tnid'])?"":$_GET['tnid'];
# get account nid
if(!empty($_GET['tnid'])){
    $query="select nid from node where type='account' and title='$tnid'";
    $records = db_query($query);
    $nid=$records->fetchAll()[0]->nid;
}
$title="Transactions: ".node_load($nid)->field_account_name['und']['0']['value'];
drupal_set_title($title);
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
# Delete transactions which have been removed
$query = "select a.nid, a.title from node a left join node b on a.title=b.nid where a.type='transaction' and b.nid is null";
$records = db_query($query);
foreach ($records as $record) {
    $nid1=$record->nid;
    node_delete($nid1);
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
?>
<script>
    jQuery(document).ready(function () {
        jQuery('#mytransactions').dataTable({
            "oLanguage": {
                "sProcessing": '<i class="fa fa-2x fa-spinner fa-spin"></i>'
            },
            "lengthMenu": [
                [10, 25, 50, 100, 500, 1000],
                [10, 25, 50, 100, 500, 1000]
            ],
            "fnServerParams": function (aoData) {
                aoData.push({"name": "nid", "value": <?php echo $nid;?>});
            },
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
            "sAjaxSource": "mytransactions-grid"
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
                    <li><a href="#overlay=node/add/income"><i class="fa fa-plus fa-fw"></i>Payment</a></li>
                    <li><a href="mytransactions?nid=<?php echo $nid;?>"><i class="fa fa-refresh fa-fw"></i>Refresh</a></li>
                    <li><a href='mybilling?nid=<?php echo node_load($nid)->title;?>'><i class='fa fa-level-up fa-fw'></i>&nbsp;Rent</a></li>
                    <li><a href='mypayments?nid=<?php echo node_load($nid)->title;?>'><i class='fa fa-level-down fa-fw'></i>&nbsp;Payment</a></li>
                    <li><a href="tenants"><i class="fa fa-users fa-fw"></i>&nbsp;Tenants</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>

<div id="dynamic">
    <table class="display  table table-bordered" id="mytransactions" width="100%">
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
            <th><i class="fa fa-pencil-square-o fa-fw"></i></th>
            <th><i class="fa fa-trash fa-fw"></i></th>
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
            <th></th>
            <th>
                <a href="javascript:window.print()"><i class="fa fa-print fa-fw"></i></a>
            </th>
        </tr>
        </tfoot>
    </table>
</div>

