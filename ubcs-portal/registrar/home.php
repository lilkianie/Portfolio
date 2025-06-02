<div class="h-100  pt-2">
	<form action="" class="h-100">
		<div class="w-100 d-flex justify-content-center">
			<div class="input-group col-md-5">
				<input type="text" class='form-control' name="search" value="<?php echo isset($_GET['search']) ? $_GET['search'] : "" ?>" placeholder="Search Event">
				<div class="input-group-append">
				<button type="submit" class="btn btn-light border">
					<i class="fas fa-search text-muted"></i>
				</button>
				</div>
			</div>
		</div>
	</form>
	<hr>
	<div class="col-md-12">
		<div class="row row-cols-lg-3 row-cols-sm-2 row-cols-1 row-cols-xs-1">
			<?php
			$where = "";
			if($_settings->userdata('type') != 1)
			$where = " where user_id = '{$_settings->userdata('id')}' ";
			if(isset($_GET['search'])){
				if(empty($where))
					$where = " where ";
				else
					$where .= " and ";
				$where .= " title LIKE '%".$_GET['search']."%' or description LIKE '%".$_GET['search']."%' ";
			}
			$qry = $conn->query("SELECT * FROM event_list {$where}");
			while($row = $qry->fetch_assoc()):
			?>
			<a href="./?page=registration&e=<?php echo md5($row['id']) ?>" class="col m-2">
				<div class="callout callout-info m-2 col event_item text-dark">
					<dl>
						<dt><b><?php echo $row['title'] ?></b></dt>
						<dd><?php echo $row['description'] ?></dd>
					<dl>
					<div class="w-100 d-flex justify-content-end">
					<?php 
					if(strtotime($row['datetime_start']) > time()): ?>
						<span class="badge badge-light">Pending</span>
					<?php elseif(strtotime($row['datetime_end']) <= time()): ?>
						<span class="badge badge-success">Done</span>
					<?php elseif((strtotime($row['datetime_start']) < time()) && (strtotime($row['datetime_end']) > time())): ?>
						<span class="badge badge-primary">On-Going</span>
					<?php endif; ?>
					</div>
				</div>
			</a>
			<?php endwhile; ?>
		</div>
	</div>
</div>