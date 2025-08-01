<?php
require_once('../config.php');
Class Members extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function save_members(){
		extract($_POST);
		$data = '';
		
		//$check = $this->conn->query("SELECT * FROM members where username = '$username' ".($id > 0 ? " and id != '{$id}' " : ""));
		//if($check->num_rows > 0)	return 3;
		foreach($_POST as $k => $v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=" , ";
				$data .= " {$k} = '{$v}' ";
			}
		}
		

		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
				$fname = 'uploads/'.strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
				$move = move_uploaded_file($_FILES['img']['tmp_name'],'../'. $fname);
				if($move){
					$data .=" , mem_avatar = '{$fname}' ";
					if(isset($_SESSION['userdata']['avatar']) && is_file('../'.$_SESSION['userdata']['avatar']) && $this->settings->userdata('id') == $id)
						unlink('../'.$_SESSION['userdata']['avatar']);
					if(isset($mem_avatar) && is_file('../'.$mem_avatar))
						unlink('../'.$mem_avatar);
				}
			}
		if(empty($id)){
			$qry = $this->conn->query("INSERT INTO members_list set {$data}");
			if($qry){
				$this->settings->set_flashdata('success','Member Details successfully saved.');
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
				return "INSERT INTO members_list set {$data}";
			}

		}else{
			$qry = $this->conn->query("UPDATE members_list set $data where mem_id = {$id}");
			if($qry){
				$this->settings->set_flashdata('success','Members Details successfully updated.');
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
				return "UPDATE members_list set $data where mem_id = {$id}";
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
			//if(!empty($data)) $data .=" , ";
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
			$qry = $this->conn->query("UPDATE users SET {$data} WHERE id = '{$id}'");
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
				return "UPDATE users SET {$data} WHERE id = '{$id}'";
			}
			
		}
	}
	public function delete_members(){
		extract($_POST);
		$qry = $this->conn->query("DELETE FROM members_list where mem_id = {$id}");
		if($qry){
			$this->settings->set_flashdata('success','Member Details successfully deleted.');
			return 1;
		}else{
			return false;
		}
	}
}

$members = new Members();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
switch ($action) {
	case 'save':
		echo $members->save_members();
	break;
	case 'delete':
		echo $members->delete_members();
	break;
	case 'psave':
		echo $members->save_puser();
	break;
	default:
		// echo $sysset->index();
		break;
}