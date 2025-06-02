<?php
require_once('../config.php');
require_once('../libs/phpqrcode/qrlib.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function save_event(){
		$data ="";
		foreach($_POST as $k =>$v){
			$_POST[$k] = addslashes($v);
		}
		extract($_POST);
		$check = $this->conn->query("SELECT * FROM event_list where title = '{$title}' ".($id > 0 ? " and id != '{$id}' " : ""))->num_rows;
		if($check > 0){
			$resp['status'] = "duplicate";
		}else{

			foreach($_POST as $k =>$v){
				if(!in_array($k,array('id'))){
					if(!empty($data)) $data .= ", ";
					$data .= " `{$k}` = '{$v}' ";
				}
			}
			if(empty($id)){
				$sql = "INSERT INTO event_list set $data";
			}else{
				$sql = "UPDATE event_list set $data where id = '{$id}'";
			}
			$save = $this->conn->query($sql);
			if($save){
				$resp['status'] = 'success';
				$this->settings->set_flashdata("success", " Event Successfully Saved.");
			}else{
				$resp['status'] = 'failed';
				$resp['err'] = $this->conn->error;
				$resp['sql'] = $sql;
			}
		}


		return json_encode($resp);
	}
	function delete_event(){
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM event_list where id = '{$id}'");
		if($delete){
			$resp['status'] = "success";
			$this->settings->set_flashdata("success", " Event Successfully Deleted.");
		}else{
			$resp['status'] = "failed";
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	public function save_audience(){
		$data ="";
		foreach($_POST as $k =>$v){
			$_POST[$k] = addslashes($v);
		}
		extract($_POST);

		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .= ", ";
				$data .= " `{$k}` = '{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO event_audience set $data";
		}else{
			$sql = "UPDATE event_audience set $data where id = '{$id}'";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			$code = empty($id) ? md5($this->conn->insert_id) : md5($id);
			if(!is_dir('../temp/')) mkdir('../temp/');
			$tempDir = '../temp/'; 
			if(!is_file('../temp/'.$code.'.png'))
			QRcode::png($code, $tempDir.''.$code.'.png', QR_ECLEVEL_L, 5);
			$this->settings->set_flashdata("success", " Event Guest Successfully Saved.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error;
			$resp['sql'] = $sql;
		}

		
		return json_encode($resp);
	}
	function delete_audience(){
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM event_audience where id = '{$id}'");
		if($delete){
			$resp['status'] = "success";
			$this->settings->set_flashdata("success", " Event Guest Successfully Deleted.");
		}else{
			$resp['status'] = "failed";
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function load_registration(){
		extract($_POST);
		$qry = $this->conn->query("SELECT a.*,r.id as rid,r.date_created as rdate FROM registration_history r inner join event_audience a on a.id =r.audience_id where r.event_id = '{$event_id}' and r.id > '{$last_id}' order by r.id asc ");
		$data=array();
		while($row=$qry->fetch_assoc()){
			$row['rdate'] = date("M d, Y h:i A",strtotime($row['rdate']));
			$data[]=$row;
		}
		return json_encode($data);
	}
	function register(){
		extract($_POST);
		$query = $this->conn->query("SELECT * FROM event_audience where md5(id) = '{$audience_id}' and md5(event_id)='{$event_id}' ");
		if($query->num_rows > 0){
			$res = $query->fetch_assoc();
			$check = $this->conn->query("SELECT * from registration_history where event_id = '{$res['event_id']}' and  audience_id = '{$res['id']}' ");
			if($check->num_rows > 0){
				$resp['status']=3;
				$resp['name']=$res['name'];
			}else{

				$insert = $this->conn->query("INSERT INTO registration_history set event_id = '{$res['event_id']}',  audience_id = '{$res['id']}',`user_id` = '{$this->settings->userdata('id')}'  ");
				if($insert){
					$resp['status']=1;
					$resp['name']=$res['name'];
				}else{
					$resp['status']=2;
					$resp['error']=$this->conn->error;
				}
			}

		}else{
			$resp['status']=2;
		}
		return json_encode($resp);
	}
}

$main = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
switch ($action) {
	case 'save_event':
		echo $main->save_event();
	break;
	case 'delete_event':
		echo $main->delete_event();
	break;
	case 'save_audience':
		echo $main->save_audience();
	break;
	case 'delete_audience':
		echo $main->delete_audience();
	break;
	case 'load_registration':
		echo $main->load_registration();
	break;
	case 'register':
		echo $main->register();
	break;
	default:
		// echo $sysset->index();
		break;
}