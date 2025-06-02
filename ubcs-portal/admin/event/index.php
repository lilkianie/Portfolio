<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary new_event" href="javascript:void(0)"><i class="fa fa-plus"></i> Add New</a>
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
						<th>Title</th>
						<th>Description</th>
						<th>Details</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$users = $conn->query("SELECT id,concat(firstname,' ',lastname) as name FROM users where `type` =2  ");
					$assignees = array();
					while($urow = $users->fetch_assoc()){
						$assignees[$urow['id']] = ucwords($urow['name']);
					}
					$qry = $conn->query("SELECT * FROM event_list order by title asc  ");
					while($row= $qry->fetch_assoc()):
						$assignee = isset($assignees[$row['user_id']]) ? $assignees[$row['user_id']] : "N/A";
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo ucwords($row['title']) ?></b></td>
						<td><b><?php echo $row['description'] ?></b></td>
						<td>
							<small><b>DateTime Start:</b> <?php echo date("M d Y h:i A",strtotime($row['datetime_start'])) ?></small><br>
							<small><b>DateTime End:</b> <?php echo date("M d Y h:i A",strtotime($row['datetime_end'])) ?></small><br>
							<small><b>Assigned User:</b> <?php echo $assignee ?></small>
						</td>
						<td class="text-center">
							<?php 
							if(strtotime($row['datetime_start']) > time()): ?>
								<span class="badge badge-light">Pending</span>
							<?php elseif(strtotime($row['datetime_end']) <= time()): ?>
								<span class="badge badge-success">Done</span>
							<?php elseif((strtotime($row['datetime_start']) < time()) && (strtotime($row['datetime_end']) > time())): ?>
								<span class="badge badge-primary">On-Going</span>
							<?php endif; ?>
						</td>
						<td class="text-center">
		                    <div class="btn-group">
		                        <a href="javascript:void(0)" data-id='<?php echo $row['id'] ?>' class="btn btn-primary btn-flat manage_event">
		                          <i class="fas fa-edit"></i>
		                        </a>
		                        <button type="button" class="btn btn-danger btn-flat delete_event" data-id="<?php echo $row['id'] ?>">
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
		$('.new_event').click(function(){
			uni_modal("New Event","./event/manage.php")
		})
		$('.manage_event').click(function(){
			uni_modal("Manage Event","./event/manage.php?id="+$(this).attr('data-id'))
		})
		
		$('.delete_event').click(function(){
		_conf("Are you sure to delete this Event?","delete_event",[$(this).attr('data-id')])
		})
		$('#list').dataTable()
	})
	function delete_event($id){
		start_loader()
		$.ajax({
			url:_base_url_+'classes/Master.php?f=delete_event',
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