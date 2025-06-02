<?php 
require_once('../../config.php');
if(isset($_GET['id']) && !empty($_GET['id'])){
	$qry = $conn->query("SELECT * FROM chapter_list where chapter_id = {$_GET['id']}");
	foreach($qry->fetch_array() as $k => $v){
		if(!is_numeric($k)){
			$$k = $v;
		}
	}
}
?>
<form action="" id="people-frm">
	<div class="row">
		<div class="col-md-12">
			<div id="msg" class="form-group"></div>
			<input type="hidden" name='id' value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
			<div class="form-group">
				<label for="chapter_name" class="control-label">Chapter Name</label>
				<input type="text" class="form-control form-control-sm" name="chapter_name" id="chapter_name" value="<?php echo isset($chapter_name) ? $chapter_name : '' ?>" required>
			</div>
			<div class="form-group">
				<label for="chapter_address" class="chapter_address-label">Address</label>
				<textarea type="text" class="form-control form-control-sm" name="chapter_address" id="chapter_address"><?php echo isset($chapter_address) ? $chapter_address : '' ?></textarea>
			</div>
			<div class="form-group">
				<label for="chapter_contact" class="control-label">Contact Person</label>
				<input type="text" class="form-control form-control-sm" name="chapter_contact" id="chapter_contact" value="<?php echo isset($chapter_contact) ? $chapter_contact : '' ?>">
			</div>
			<div class="form-group">
				<label for="chapter_number" class="control-label">Contact Number</label>
				<input type="text" class="form-control form-control-sm" name="chapter_number" id="chapter_number" value="<?php echo isset($chapter_number) ? $chapter_number : '' ?>">
			</div>
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
	
	$(document).ready(function(){
		
		$('#people-frm1').submit(function(e){
			e.preventDefault()
			start_loader()
			if($('.err_msg').length > 0)
				$('.err_msg').remove()
			$.ajax({
				url:_base_url_+'classes/Chapters.php?f=save',
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
				}else if(resp == 3){
					var _frm = $('#people-frm #msg')
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

		$('#people-frm').submit(function(e){
			e.preventDefault()
			start_loader()
			if($('.err_msg').length > 0)
				$('.err_msg').remove()
			$.ajax({
				url:_base_url_+'classes/Chapters.php?f=save_chapter',
				data: new FormData($(this)[0]),
			    cache: false,
			    contentType: false,
			    processData: false,
			    method: 'POST',
			    type: 'POST',
			    dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("an error occured","error")
					end_loader()
				},
				success:function(resp){
				if(resp.status == 'success'){
					location.reload();
				}else if(esp.status == 'duplicate'){
					var _frm = $('#people-frm #msg')
					var _msg = "<div class='alert alert-danger text-white err_msg'><i class='fa fa-exclamation-triangle'></i> Chapter already exists.</div>"
					_frm.prepend(_msg)
					_frm.find('input#chapter_name').addClass('is-invalid')
					$('[name="chapter_name"]').focus()
				}else{
					alert_toast("An error occured.",'error');
				}
					end_loader()
				}
			})
		})
	})
</script>