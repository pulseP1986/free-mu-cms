<form id="payssion_hosted_payment" name="payssion_hosted_payment" action="<?php echo $data['url'];?>" method="post">
    <input type="hidden" name="api_sig" value="<?php echo $data['sig'];?>">
    <input type="hidden" name="order_id" value="<?php echo $data['order_id'];?>">
    <input type="hidden" name="payer_email" value="<?php echo $data['email'];?>">
    <input type="hidden" name="description" value="<?php echo $data['description'];?>">
    <input type="hidden" name="amount" value="<?php echo $data['amount'];?>">
    <input type="hidden" name="currency" value="<?php echo $data['currency'];?>">
    <input type="hidden" name="return_url" value="<?php echo $data['return_url'];?>">
    <input type="submit" class="button" style="display:none;" value="click here to try this hosted page demo">
</form>
Redirecting...
<script>
window.onload = function() { document.getElementById("payssion_hosted_payment").submit() }
</script>	