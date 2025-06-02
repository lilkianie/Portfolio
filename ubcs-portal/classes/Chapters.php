<?php
require_once('../config.php');
Class Chapters extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function save_users(){
		extract($_POST);
		$data = '';
		$check = $this->conn->query("SELECT * FROM chapter_list where username = '$username' ".($id > 0 ? " and id != '{$id}' " : ""));
		if($check->num_rows > 0)
			return 3;
		foreach($_POST as $k => $v){
			if(!in_array($k,array('id','password','preset'))){
				if(!empty($data)) $data .=" , ";
				$data .= " {$k} = '{$v}' ";
			}
		}
		if(isset($password) && !empty($password) && !empty($id)){
			$password = md5($password);
			if(!empty($data)) $data .=" , ";
			$data .= ", `password` = '{$password}' ";
		}elseif((!isset($password) && empty($id)) || (!isset($password) && !empty($id) && isset($preset) && $preset =='on')){
			$pwd = md5(strtolower(substr($firstname,0,1).$lastname));
			$data .= ", `password` = '{$pwd}' ";
		}

		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
				$fname = 'uploads/'.strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
				$move = move_uploaded_file($_FILES['img']['tmp_name'],'../'. $fname);
				if($move){
					$data .=" , avatar = '{$fname}' ";
					if(isset($_SESSION['userdata']['avatar']) && is_file('../'.$_SESSION['userdata']['avatar']) && $this->settings->userdata('id') == $id)
						unlink('../'.$_SESSION['userdata']['avatar']);
					if(isset($avatar) && is_file('../'.$avatar))
						unlink('../'.$avatar);
				}
			}
		if(empty($id)){
			$qry = $this->conn->query("INSERT INTO users set {$data}");
			if($qry){
				$this->settings->set_flashdata('success','User Details successfully saved.');
				foreach($_POST as $k => $v){
					if($k != 'id'){
						if(!empty($data)) $data .=" , ";
						$this->settings->set_userdata($k,$v);
					}
				}
				if(isset($move) && $move && $this->settings->userdata('id') == $id){
					$this->settings->set_userdata('avatar',$fname);
				}
				return 1;
			}else{
				return "INSERT INTO users set {$data}";
			}

		}else{
			$qry = $this->conn->query("UPDATE users set $data where id = {$id}");
			if($qry){
				$this->settings->set_flashdata('success','User Details successfully updated.');
				foreach($_POST as $k => $v){
					if($k != 'id'){
						if(!empty($data)) $data .=" , ";
						$this->settings->set_userdata($k,$v);
					}
				}
				if(isset($move) && $move && $this->settings->userdata('id') == $id){
					$this->settings->set_userdata('avatar',$fname);
				}
				return 1;
			}else{
				return "UPDATE users set $data where id = {$id}";
			}
			
		}
	}
	public function save_puser(){
		extract($_POST);
		$data = '';
		$check = $this->conn->query("SELECT * FROM users where id = '{$id}' ");
		if($check->num_rows <= 0) return 3;
		foreach($_POST as $k => $v){
			if(!in_array($k,array('id','password','preset'))){
				if(!empty($data)) $data .=" , ";
				$data .= " {$k} = '{$v}' ";
			}
		}
		if(isset($password) && !empty($password)){
			$password = md5($password);
			if(!empty($data)) $data .=" , ";
			$data .= ", `password` = '{$password}' ";
		}/*elseif((!isset($password) && empty($id)) || (!isset($password) && !empty($id) && isset($preset) && $preset =='on')){
			$pwd = md5(strtolower(substr($firstname,0,1).$lastname));
			$data .= ", `password` = '{$pwd}' ";
		}*/

		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
				$fname = 'uploads/'.strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
				$move = move_uploaded_file($_FILES['img']['tmp_name'],'../'. $fname);
				if($move){
					$data .=" , avatar = '{$fname}' ";
					if(isset($_SESSION['userdata']['avatar']) && is_file('../'.$_SESSION['userdata']['avatar']) && $this->settings->userdata('id') == $id)
						unlink('../'.$_SESSION['userdata']['avatar']);
					if(isset($avatar) && is_file('../'.$avatar))
						unlink('../'.$avatar);
				}
			}
		if(empty($id)){
			$qry = $this->conn->query("INSERT INTO users set {$data}");
			if($qry){
				$this->settings->set_flashdata('success','User Details successfully saved.');
				foreach($_POST as $k => $v){
					if($k != 'id'){
						if(!empty($data)) $data .=" , ";
						$this->settings->set_userdata($k,$v);
					}
				}
				if(isset($move) && $move && $this->settings->userdata('id') == $id){
					$this->settings->set_userdata('avatar',$fname);
				}
				return 1;
			}else{
				return "INSERT INTO users set {$data}";
			}

		}else{
			$qry = $this->conn->query("UPDATE users set {$data} where id = {$id}");
			if($qry){
				$this->settings->set_flashdata('success','User Details successfully updated.');
				foreach($_POST as $k => $v){
					if($k != 'id'){
						if(!empty($data)) $data .=" , ";
						$this->settings->set_userdata($k,$v);
					}
				}
				if(isset($move) && $move && $this->settings->userdata('id') == $id){
					$this->settings->set_userdata('avatar',$fname);
				}
				return 1;
			}else{
				return "UPDATE users set {$data} where id = '{$id}'";
			}
			
		}
	}
	public function delete_chapters(){
		extract($_POST);
		$qry = $this->conn->query("DELETE FROM chapter_list where chapter_id = $id");
		if($qry){
			$this->settings->set_flashdata('success','Chapter Details successfully deleted.');
			return 1;
		}else{
			return false;
		}
	}
	public function save_chapters(){
		$data ="";
		foreach($_POST as $k =>$v){
			$_POST[$k] = addslashes($v);
		}
		extract($_POST);
		$check = $this->conn->query("SELECT * FROM chapter_list where chapter_name = '{$chapter_name}' ".($id > 0 ? " and chapter_id != '{$id}' " : ""))->num_rows;
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
				$sql = "INSERT INTO chapter_list set $data";
			}else{
				$sql = "UPDATE chapter_list set $data where chapter_id = '{$id}'";
			}
			$save = $this->conn->query($sql);
			if($save){
				$resp['status'] = 'success';
				$this->settings->set_flashdata("success", " Chapter Successfully Saved.");
			}else{
				$resp['status'] = 'failed';
				$resp['err'] = $this->conn->error;
				$resp['sql'] = $sql;
			}
		}


		return json_encode($resp);
	}
}

$chapters = new chapters();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
switch ($action) {
	case 'save_chapter':
		echo $chapters->save_chapters();
	break;
	case 'delete':
		echo $chapters->delete_chapters();
	break;
	case 'psave':
		echo $chapters->save_puser();
	break;
	default:
		// echo $sysset->index();
		break;
}