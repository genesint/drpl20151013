<?php
$o_output=array();
exec('converter.exe 0',$o_output);
$o_output=explode(" ",$o_output[0]);
$app_id = $o_output[0];
?>
<script>

</script>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-3"></div>
    <div class="col-md-3"></div>
    <div class="col-md-3"></div>
</div>
<form action="starter-license" method="get" class="row">
    <div class="col-md-1"></div>
    <div class="col-md-2">Please enter license key:</div>
    <div class="col-md-3"><input type="text" size="30" name="license"/></div>
    <div class="col-md-3"><input type="submit" value="Save"></div>
    <div class="col-md-3"></div>
</form>
<br>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-10">Application ID: <?php echo $app_id; ?></div>
    <div class="col-md-2"></div>
</div>

