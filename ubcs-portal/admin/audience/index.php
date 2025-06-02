<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary new_audience" href="javascript:void(0)"><i class="fa fa-plus"></i> Add New</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-bordered" id="list">
				<!-- <colgroup>
					<col width="5%">
					<col width="15%">
					<col width="25%">
					<col width="25%">
					<col width="15%">
					<col width="15%">
				</colgroup> -->
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Event</th>
						<th>Name</th>
						<th>Details</th>
						<th>Remarks</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					
					$qry = $conn->query("SELECT a.*,e.title FROM event_audience a inner join event_list e on e.id = a.event_id order by a.name asc  ");
					while($row= $qry->fetch_assoc()):
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo ucwords($row['title']) ?></b></td>
						<td><b><?php echo ucwords($row['name']) ?></b> <span><a href="javascript:void(0)" class="view_data" data-id="<?php echo $row['id'] ?>"><span class="fa fa-qrcode"></span></a></span></td>
						<td>
							<small><b>Email:</b> <?php echo $row['email'] ?></small><br>
							<small><b>Contact #:</b> <?php echo $row['contact'] ?></small>
						</td>
						<td><b><?php echo ($row['remarks']) ?></b></td>
						<td class="text-center">
		                    <div class="btn-group">
		                        <a href="javascript:void(0)" data-id='<?php echo $row['id'] ?>' class="btn btn-primary btn-flat manage_audience">
		                          <i class="fas fa-edit"></i>
		                        </a>
		                        <button type="button" class="btn btn-danger btn-flat delete_audience" data-id="<?php echo $row['id'] ?>">
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
		$('.new_audience').click(function(){
			uni_modal("New Audience","./audience/manage.php")
		})
		$('.manage_audience').click(function(){
			uni_modal("Manage Audience","./audience/manage.php?id="+$(this).attr('data-id'))
		})
		
		$('.view_data').click(function(){
			uni_modal("QR","./audience/view.php?id="+$(this).attr('data-id'))
		})
		
		$('.delete_audience').click(function(){
		_conf("Are you sure to delete this audience?","delete_audience",[$(this).attr('data-id')])
		})
		$('#list').dataTable()
	})
	function delete_audience($id){
		start_loader()
		$.ajax({
			url:_base_url_+'classes/Master.php?f=delete_audience',
			method:'POST',
			data:{id:$id},
			dataType:"json",
			error:err=>{
				alert_toast("An error occured");
				end_loader()
			},
			success:function(resp){
				if(resp.status=="success"){
					location.reload()
				}else{
					alert_toast("Deleting Data Failed");
				}
				end_loader()
			}
		})
	}
</script>