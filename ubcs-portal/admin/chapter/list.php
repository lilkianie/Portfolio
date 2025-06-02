<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary new_chapter" href="javascript:void(0)"><i class="fa fa-plus"></i> Add New</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-bordered" id="list">
				<colgroup>
					<col width="10%">
					<col width="25%">
					<col width="25%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Name</th>
						<th>Address</th>
						<th>Contact Person</th>
						<th>Contact Number</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$qry = $conn->query("SELECT * FROM `chapter_list` where chapter_status = '1' order by chapter_name asc ");
					while($row= $qry->fetch_assoc()):
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo ucwords($row['chapter_name']) ?></b></td>
						<td><b><?php echo ucwords($row['chapter_address']) ?></b></td>
						<td><b><?php echo ucwords($row['chapter_contact']) ?></b></td>
						<td><b><?php echo $row['chapter_number'] ?></b></td>
						<td><b><?php echo ($row['chapter_status'] == 1) ? "Active" : "Inactive" ?></b></td>
						<td class="text-center">
		                    <div class="btn-group">
		                        <a href="javascript:void(0)" data-id='<?php echo $row['chapter_id'] ?>' class="btn btn-primary btn-flat manage_chapter">
		                          <i class="fas fa-edit"></i>
		                        </a>
		                        <button type="button" class="btn btn-danger btn-flat delete_chapter" data-id="<?php echo $row['chapter_id'] ?>">
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
		$('.new_chapter').click(function(){
			uni_modal("New Chapter","./chapter/manage.php")
		})
		$('.manage_chapter').click(function(){
			uni_modal("Manage Chapter","./chapter/manage.php?id="+$(this).attr('data-id'))
		})
		$('.delete_chapter').click(function(){
		_conf("Are you sure to delete this Chapter?","delete_chapter",[$(this).attr('data-id')])
		})
		$('#list').dataTable()
	})
	function delete_chapter($id){
		start_loader()
		$.ajax({
			url:_base_url_+'classes/Chapters.php?f=delete',
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