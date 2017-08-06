<h2>Register</h2>

<?= form_open('auth/register') ?>
		<label for="email">E-Mail</label>
		<?= form_error('email'); ?>
		<input type="email" name="email" id="email" onchange="checkEmailnya(this)" onblur="checkEmailnya(this)" value="<?=set_value('email'); ?> ">  <br>
        <span id="emailError" style=" color:#A94442; display:none;">This email has already registered</span>

		<label for="password">password</label>
		<?= form_error('password'); ?>
		<input type="password" name="password"> <br>

		<label for="password2">password</label>
		<?= form_error('password2'); ?>
		<input type="password" name="password2"> <br>

		<input type="submit" name="submit" value="Register">

<?php echo form_close(); ?>
<script src="https://kabinet.cibfx.co.id/assets/member/global/plugins/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
function checkEmailnya(user)
{
    var res = encodeURIComponent(user.value);
    $.get("http://localhost/pastibisa/auth/isEmailExist/"+res, function(data, status){
        if(status=="success")
        {
            if(data!="NOTEXIST")
            {
                //alert( "Email " +  user + " " +data);
                $('input[type="submit"]').prop('disabled', true);
                document.getElementById("emailError").style.display = "block";

            }
            else
            {
                $('input[type="submit"]').prop('disabled', false);
                document.getElementById("emailError").style.display = "none";
                return false;
            }
            return false;
        }
        
    });
    return true;
}
</script>

<!-- <script type="text/javascript">
$(function() {
    $('input[type="submit"]').prop('disabled', true);
    $('#checkEmailnya').on('input', function(e) {
        if(this.value.length === 6) {
            $('input[type="submit"]').prop('disabled', false);
        } else {
            $('input[type="submit"]').prop('disabled', true);
        }
    });
});
</script> -->

