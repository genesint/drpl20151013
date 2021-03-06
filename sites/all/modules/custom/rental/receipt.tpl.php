<?php
$nid = $_GET['nid'];
$node=node_load($nid);
#$date=new DateTime('@'.$node->created);
$date=new DateTime($node->field_payment_date['und']['0']['value']);
# Get tenant's transactions
$aid=$node->field_account['und']['0']['target_id'];
$query = "select sum(b.field_amount_value) as oamount from
            field_data_field_account a join field_data_field_amount b
            on a.entity_id=b.entity_id
            where
            a.bundle='transaction' and
            b.bundle='transaction' and
            a.field_account_target_id=$aid";
$records = db_query($query);
$oamount=$records->fetchAll()[0]->oamount;
?>
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-1">
        <!--<a class="btn btn-primary btn-sm" href="<?php echo $_SERVER['HTTP_REFERER']; ?>"><i class="fa fa-hand-o-left fa-fw"></i></a>--></div>
    <div class="col-md-8">
        <table class=" table table-bordered">
            <tr>
                <th>RECEIPT</th>
                <th colspan="2" align="right">
                    <div style="float:right">
                        <?php echo variable_get('site_name', '');?>
                        <br>P. O. Box 696
                        <br>Jinja-Uganda
                        <br>+256 701460096
                        <br>+256 772826636
                        <br>+256 755826636
                    </div>
                </th>
            </tr>
            <tr>
                <!--<th>Print Date:&nbsp;<?php $current=new DateTime();echo $current->format('d/m/Y');?></th>-->
                <th>Date:&nbsp;<?php echo $date->format('d/m/Y');?></th>
                <th>No:&nbsp;<?php echo $node->title;?></th>
                <th></th>
            </tr>
            <tr>
                <td>Received from:</td>
                <td colspan="2"><?php echo node_load($node->field_account['und']['0']['target_id'])->field_account_name['und']['0']['value'];?></td>

            </tr>
            <tr>
                <td>Amount paid:</td>
                <td colspan="2" align="right">UGX <?php echo number_format($node->field_amount_paid['und']['0']['value'],  2 ,  "." ,  "," );?></td>

            </tr><tr>
                <td>Amount Outstanding:</td>
                <td colspan="2" align="right">UGX <?php echo number_format($oamount,  2 ,  "." ,  "," );?></td>

            </tr>
            <tr>
                <td>For payment of:</td>
                <td colspan="2"><?php echo $node->field_particulars['und']['0']['value'];?></td>

            </tr>
            <tr>
                <td>Received by:</td>
                <td colspan="2"><?php echo variable_get('site_name', '');?></td>

            </tr>
        </table>
    </div>
    <div class="col-md-2"><a href="javascript:window.print()"><i class="fa fa-print fa-fw"></i></a></div>
</div>