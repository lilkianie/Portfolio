<?php 
require_once('../../config.php');
if(isset($_GET['id']) && !empty($_GET['id'])){
	$qry = $conn->query("SELECT * FROM members_list where mem_id = {$_GET['id']}");
	foreach($qry->fetch_array() as $k => $v){
		if(!is_numeric($k)){
			$$k = $v;
		}
	}
}
?>
<form action="" id="member-frm">
	<div class="row">
		<div class="col-md-6">
			<div id="msg" class="form-group"></div>
			<input type="hidden" name='id' value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
			<div class="form-group">
				<label for="mem_firstname" class="control-label">First Name</label>
				<input type="text" class="form-control form-control-sm" name="mem_firstname" id="mem_firstname" value="<?php echo isset($mem_firstname) ? $mem_firstname : '' ?>" required>
			</div>
			<div class="form-group">
				<label for="mem_lastname" class="control-label">Last Name</label>
				<input type="text" class="form-control form-control-sm" name="mem_lastname" id="mem_lastname" value="<?php echo isset($mem_lastname) ? $mem_lastname : '' ?>" required>
			</div>
			<div class="form-group">
				<label for="mem_middlename" class="control-label">Middle Name</label>
				<input type="text" class="form-control form-control-sm" name="mem_middlename" id="mem_middlename" value="<?php echo isset($mem_middlename) ? $mem_middlename : '' ?>">
			</div>
			<div class="form-group">
				<label for="mem_address1" class="control-label">Address 1</label>
				<input type="text" class="form-control form-control-sm" name="mem_address1" id="mem_address1" value="<?php echo isset($mem_address1) ? $mem_address1 : '' ?>">
			</div>
			<div class="form-group">
				<label for="mem_address2" class="control-label">Address 2</label>
				<input type="text" class="form-control form-control-sm" name="mem_address2" id="mem_address2" value="<?php echo isset($mem_address2) ? $mem_address2 : '' ?>">
			</div>
			
		</div>
		<div class="col-md-6">
			<div  class="form-group"></div>
			<div class="form-group">
				<label for="mem_dob" class="control-label">Date of Birth</label>
				<input type="date" class="form-control form-control-sm" name="mem_dob" id="mem_dob" value="<?php echo isset($mem_dob) ? $mem_dob : '' ?>">
			</div>
            <div class="form-group">
                <label for="mem_gender" class="control-label">Sex Gender</label>
                <select name="mem_gender" id="mem_gender" class="custom-select custom-select-sm">
                    <option value="male" <?php echo (isset($mem_gender) && $mem_gender == 'male') ? "selected" : '' ?>>Male</option>
                    <option value="female" <?php echo (isset($mem_gender) && $mem_gender == 'female') ? "selected" : '' ?>>Female</option>
                </select>
            </div>
			<div class="form-group">
                <label for="mem_status" class="control-label">Status</label>
                <select name="mem_status" id="mem_status" class="custom-select custom-select-sm">
                    <option value="1" <?php echo (isset($mem_status) && $mem_status == 1) ? "selected" : '' ?>>Active</option>
                    <option value="0" <?php echo (isset($mem_status) && $mem_status == 0) ? "selected" : '' ?>>Inactive</option>
                </select>
            </div>
			<div class="form-group">
				<label for="" class="control-label">Image</label>
				<div class="custom-file">
		          <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
		          <label class="custom-file-label" for="customFile">Choose file</label>
		        </div>
			</div>
			<div class="form-group d-flex justify-content-center">
				<img src="<?php echo validate_image(isset($mem_avatar) ? $mem_avatar : '') ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
			</div>
			<?php if(isset($id) && ($id > 0)): ?>
            <input type="hidden"  name="mem_avatar" value="<?php echo $mem_avatar ?>">
			<div class="form-group">
				<div class="icheck-primary">
					<input type="checkbox" id="resetP" name="preset">
					<label for="resetP">
						Check to reset password
					</label>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
</form>
<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
<script>
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	$(document).ready(function(){
		$('.select2').select2();
		$('#city_id').change(function(){
			var id = $(this).val();
			console.log($('#zone_id').find("[data-city='"+id+"']").length)
			$('#zone_id').find("[data-city='"+id+"']").show()
		$('#zone_id').select2();
		})
		$('#member-frm').submit(function(e){
			e.preventDefault()
			start_loader()
			if($('.err_msg').length > 0)
				$('.err_msg').remove()
			$.ajax({
				url:_base_url_+'classes/Members.php?f=save',
				data: new FormData($(this)[0]),
			    cache: false,
			    contentType: false,
			    processData: false,
			    method: 'POST',
			    type: 'POST',
				error:err=>{
					console.log(err)

				},
				success:function(resp){
				if(resp == 1){
					location.reload();
				}else if(resp == 2){
					var _frm = $('#member-frm #msg')
					var _msg = "<div class='alert alert-danger text-white err_msg'><i class='fa fa-exclamation-triangle'></i> Username should not be empty.</div>"
					_frm.prepend(_msg)
					_frm.find('input#username').addClass('is-invalid')
					$('[name="username"]').focus()
				}else if(resp == 3){
					var _frm = $('#member-frm #msg')
					var _msg = "<div class='alert alert-danger text-white err_msg'><i class='fa fa-exclamation-triangle'></i> Person already exists.</div>"
					_frm.prepend(_msg)
					_frm.find('input#username').addClass('is-invalid')
					$('[name="code"]').focus()
				}else{
					alert_toast("An error occured.",'error');
				}
					end_loader()
				}
			})
		})
	})
</script>