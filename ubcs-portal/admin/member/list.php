<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary new_member" href="javascript:void(0)"><i class="fa fa-plus"></i> Add New</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="10%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Image</th>
						<th>Lastname</th>
						<th>First Name</th>
						<th>Middle Name</th>
						<th>DOB</th>
						<th>Gender</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$qry = $conn->query("SELECT * FROM `members_list` order by mem_lastname asc, mem_firstname asc ");
					while($row= $qry->fetch_assoc()):
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td class="text-center"><img class="direct-chat-img border" src="<?php echo validate_image($row['mem_avatar']) ?>" alt="Image" style="float:unset;"></td>
						<td><b><?php echo ucwords($row['mem_lastname']) ?></b></td>
						<td><b><?php echo ucwords($row['mem_firstname']) ?></b></td>
						<td><b><?php echo ucwords($row['mem_middlename']) ?></b></td>
						<td><b><?php echo ucwords($row['mem_dob']) ?></b></td>
						<td><b><?php echo ucwords($row['mem_gender']) ?></b></td>
						<td><b><?php echo ($row['mem_status'] == 1) ? "Active" : "Inactive" ?></b></td>
						<td class="text-center">
		                    <div class="btn-group">
		                        <a href="javascript:void(0)" data-id='<?php echo $row['mem_id'] ?>' class="btn btn-primary btn-flat manage_member">
		                          <i class="fas fa-edit"></i>
		                        </a>
		                        <button type="button" class="btn btn-danger btn-flat delete_member" data-id="<?php echo $row['mem_id'] ?>">
		                          <i class="fas fa-trash"></i>
		                        </button>
	                      </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>

	$(document).ready(function(){
		$('.new_member').click(function(){
			uni_modal("New Member","./member/manage.php",'mid-large')
		})
		$('.manage_member').click(function(){
			uni_modal("Manage Member","./member/manage.php?id="+$(this).attr('data-id'),'mid-large')
		})
		$('.delete_member').click(function(){
		_conf("Are you sure to delete this Member?","delete_member",[$(this).attr('data-id')])
		})
		$('#list').dataTable()
	})
	function delete_member($id){
		start_loader()
		$.ajax({
			url:_base_url_+'classes/Members.php?f=delete',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					location.reload()
				}
			}
		})
	}
</script>