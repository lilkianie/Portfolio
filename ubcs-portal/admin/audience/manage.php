<?php 
require_once('../../config.php');
if(isset($_GET['id']) && !empty($_GET['id'])){
	$qry = $conn->query("SELECT * FROM event_audience where id = {$_GET['id']}");
	foreach($qry->fetch_array() as $k => $v){
		if(!is_numeric($k)){
			$$k = $v;
		}
	}
}
?>
<form action="" id="audience-frm">
	<div id="msg" class="form-group"></div>
	<input type="hidden" name='id' value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
	<div class="form-group">
		<label for="name" class="control-label">Fullname</label>
		<input type="text" class="form-control form-control-sm" name="name" id="name" value="<?php echo isset($name) ? $name : '' ?>" required>
	</div>
	<div class="form-group">
		<label for="email" class="control-label">Email</label>
		<input type="email" class="form-control form-control-sm" name="email" id="email" value="<?php echo isset($email) ? $email : '' ?>" required>
	</div>
	<div class="form-group">
		<label for="contact" class="control-label">Contact</label>
		<input type="text" class="form-control form-control-sm" name="contact" id="contact" value="<?php echo isset($contact) ? $contact : '' ?>" required>
	</div>
	<div class="form-group">
		<label for="remarks" class="control-label">Remarks</label>
		<textarea type="text" class="form-control form-control-sm" name="remarks" id="remarks" required ><?php echo isset($remarks) ? $remarks : '' ?></textarea>
	</div>
	<div class="form-group">
		<label for="event_id" class="control-label">Event</label>
		<select name="event_id" id="event_id" class="custom-select select2" required>
			<option></option>
			<?php 
				$qry = $conn->query("SELECT id,title FROM event_list order by concat(title) asc ");
				while($row = $qry->fetch_assoc()):
			?>
				<option value="<?php echo $row['id'] ?>" <?php echo isset($event_id) && $event_id == $row['id'] ? "selected" : '' ?>><?php echo ucwords($row['title']) ?></option>
			<?php endwhile; ?>
		</select>
	</div>
	
</form>
<script>
	
	$(document).ready(function(){
		$('.select2').select2();
	
		$('#audience-frm').submit(function(e){
			e.preventDefault()
			start_loader()
			if($('.err_msg').length > 0)
				$('.err_msg').remove()
			$.ajax({
				url:_base_url_+'classes/Master.php?f=save_audience',
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
				}else{
					alert_toast("An error occured.",'error');
				}
					end_loader()
				}
			})
		})
	})
</script>