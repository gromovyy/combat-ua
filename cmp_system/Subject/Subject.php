<?php
	// ����� ����� ��� ����������, ������� ������� �� ������ ��������.
	// ������������ ���� ����������� ���� ��, ��� �� �������������� ������� ��  
	// ����������� �������������� ������������ ��������, � ������� ��� ��������
	class Subject extends Contenter {
	
	// �������������� ������� �s_update �������� ������ Base 

	function is_update($id_owner = null, $id_row = null, $column = null, $table = null) {
		// ���� ������� ������������ ����� ���������������� ����� �� ����������, ������������� ���������
		if ($this->getR("update")==1) return true;
		// ���� �� ����� ������������� ����, ������������� ���������
		if (empty($id_row)) return false;
		// ���� �� ����� ������������� ������������, �������� ���.
		if (empty($id_owner))
			   $id_owner = $this->get_owner($id_row, $table);
		$row = $this->get_row($id_row, $table);
		// ���� ���������� �� �������, ������������� ���������
		if (empty($row)) return false;
		
		$component = $row["component"];
		// ���� ������ �� �������� �� � ������ ����������, ��������� ����� ��� ��� ������� �����������
		if (empty($component) and ($this->getR("update")==2) and ($this->User->getId() == $id_owner)) return true;
		
		// ���� ������������ ��������� �����, ��������, ����� �� ��� �������������. 
		$table = $this->$component->getTableByObject($row["object"]);
		if (($this->getR("update")==2) and 
		    ($this->User->getId() == $id_owner) and 
			 $this->$component->is_update($id_owner, $row["id_object"], null, $table))
			return true;
		return false;
	}

// �������������� ������� �s_delete �������� ������ Base 
// � ���� ������ ������� ����� ������������ �������� ����������� ���������� �� ����������� ����.

	public function is_delete($id_owner=null, $id_row=null, $table = null) {
		// ���� ������� ������������ ����� ���������������� ����� �� ����������, ������������� ���������
		if ($this->getR("delete")==1) return true;
		// ���� �� ����� ������������� ����, ������������� ���������
		if (empty($id_row)) return false;
		// ���� �� ����� ������������� ������������, �������� ���.
		if (empty($id_owner))
			   $id_owner = $this->get_owner($id_row, $table);
		$row = $this->get_row($id_row, $table);
		// ���� ���������� �� �������, ������������� ���������
		if (empty($row)) return false;
		
		$component = $row["component"];
		// ���� ������ �� �������� �� � ������ ������������� ����������, ��������� ����� ��� ��� ������� �����������
		if (empty($component) and ($this->getR("delete")==2) and ($this->User->getId() == $id_owner)) return true;
		
		// ���� ������������ ��������� �����, ��������, ����� �� ��� �������������. 
		//echo $component;
		$table = $this->$component->getTableByObject($row["object"]);
		if (($this->getR("delete")==2) and 
		    ($this->User->getId() == $id_owner) and 
			 $this->$component->is_update($id_owner, $row["id_object"], null, $table))
			return true;
		return false;
	}
	
	}
?>