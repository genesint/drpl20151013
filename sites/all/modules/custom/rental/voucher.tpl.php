<?php
$nid = $_GET['nid'];
$node=node_load($nid);
$date=new DateTime('@'.$node->created);
?>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <table class=" table table-bordered">
            <tr>
                <th>VOUCHER</th>
                <th colspan="2">SSEGONGA APARTMENTS</th>
            </tr>
            <tr>
                <th></th>
                <th>Date:&nbsp;<?php echo $date->format('d/m/Y');?></th>
                <th>No:&nbsp;<?php echo $node->title;?></th>
            </tr>
            <tr>
                <td>Paid to:</td>
                <td colspan="2"><?php echo node_load($node->field_account['und']['0']['target_id'])->field_account_name['und']['0']['value'];?></td>

            </tr>
            <tr>
                <td>Amount:</td>
                <td colspan="2"><?php echo number_format($node->field_amount_spent['und']['0']['value'],  2 ,  "." ,  "," );?></td>

            </tr>
            <tr>
                <td>For payment of:</td>
                <td colspan="2"><?php echo $node->field_particulars['und']['0']['value'];?></td>

            </tr>
            <tr>
                <td>Received by:</td>
                <td colspan="2"></td>

            </tr>
        </table>
    </div>
    <div class="col-md-2"><a class="btn btn-primary btn-sm" href="<?php echo $_SERVER['HTTP_REFERER']; ?>">Back</a></div>
</div>