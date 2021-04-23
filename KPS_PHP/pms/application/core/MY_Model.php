<?php defined('BASEPATH') OR exit('No direct script access allowed');

abstract class MY_Model extends CI_Model {
	
    private $item_class = '';
	private $table_name = '';

  	private $fields = array();
  	private $primary_key_fields = array();

    private $caching = true;
    private $cache = array();

	private $skip_target_source = false;

	public function __construct($table_name, $item_class, $caching = true) {
	
		parent::__construct();
		
		$this->table_name = $table_name;
		$this->item_class = $item_class;

		list($this->fields, $this->primary_key_fields) = db_get_table_attributes($this->table_name);
		$this->caching = (boolean) $caching;

	}

    public function find($arguments = null) {
        
		$conditions = db_prepare_conditions((isset($arguments['conditions']) ? $arguments['conditions'] : ''), $this->getTableName(true), $this->hasTargetSource());
		if($conditions != '') $this->db->where($conditions);

		
		$joins = isset($arguments['joins']) ? $arguments['joins'] : false;
		if($joins) {

			if (isset($joins['table'])) $joins = array($joins);
			foreach ($joins as $join) {
				$this->db->join($join['table'], $join['cond'], $join['type']);
			}

			$this->db->select($this->table_name.'.*');

		}
		
		$order_by = isset($arguments['order']) ? $arguments['order'] : '';
		if($order_by != '') $this->db->order_by($order_by);

		$group_by = isset($arguments['group']) ? $arguments['group'] : '';
		if($group_by != '') $this->db->group_by($group_by);

		$having = isset($arguments['having']) ? $arguments['having'] : '';
		if($having != '') $this->db->having($having);
		
		$one        = (boolean) (isset($arguments['one']) && $arguments['one']);
		$offset     = (integer) (isset($arguments['offset']) ? $arguments['offset'] : 0);
		$limit      = (integer) (isset($arguments['limit']) ? $arguments['limit'] : 0);
		
		if($one && $offset == 0 && $limit == 1) $limit = 1;
		if($limit > 0) $this->db->limit($limit, $offset);
		
		$result = $this->db->get($this->table_name);
		
		if($result->num_rows() > 0) {

			if($one) {
				return $this->loadItem($result->row_array());
			} else {

				$objects = array();

				$rows = $result->result_array();
				foreach($rows as $row) {
	
					$object = $this->loadItem($row);
					if(!is_null($object)) $objects[] = $object;
	
				}
	
				return count($objects) ? $objects : null;
			
			}
			
		}
		
		return null;
		
    }

    public function findById($id, $force_reload = false) {

		if(!$force_reload && $this->getCaching()) {

			$item = $this->getCachedItem($id);
			if($item instanceof Model_object) return $item;
			
		}

		$conditions = db_prepare_conditions_by_primary_key($this->getPrimaryKeyFields(), $id, $this->hasTargetSource());
		$result = $this->db->where($conditions)->get($this->table_name);
		
		

		if($result->num_rows() > 0) {		
			return $this->loadItem($result->row_array());
		}

		return null;
					
    }
		
	public function paginate($arguments = null, $limit = null, $offset = null) {
		
		if(!is_array($arguments)) $arguments = array();
		$conditions = isset($arguments['conditions']) ? $arguments['conditions'] : null;
		$joins = isset($arguments['joins']) ? $arguments['joins'] : null;

		$arguments['offset']  = (integer) (isset($offset) ? $offset : 0);
		$arguments['limit']   = (integer) (isset($limit) ? $limit : 0);
		
		$total_items = $this->count($conditions, $joins);
		$current_items = $this->find($arguments);

		return array($current_items, $total_items);

	}
			
    public function count($conditions = null) {

		$escaped_primary_key = is_array($primary_key_fields = $this->getPrimaryKeyFields()) ? '*' : db_escape_field($primary_key_fields);
		$this->db->select($escaped_primary_key);

		$conditions = db_prepare_conditions($conditions, $this->getTableName(true), $this->hasTargetSource());
		if($conditions != "") $this->db->where($conditions);

		

		return $this->db->count_all_results($this->table_name);
    
	}

    public function delete($conditions = null) {
	
		$conditions = db_prepare_conditions($conditions, $this->getTableName(true), $this->hasTargetSource());
		if($conditions != "") $this->db->where($conditions);

		return $this->db->delete($this->table_name);
		
    }

    public function update($data, $conditions = null) {

		$conditions = db_prepare_conditions($conditions, $this->getTableName(true), $this->hasTargetSource());
		if($conditions != "") $this->db->where($conditions);

		return $this->db->update($this->table_name, $data);
      
    }

    public function loadItem($row) {
    
		if(!$this->isItemReady()) return null;
		
		$class = $this->getItemClass();
		$item = new $class();
		
		if(!$item instanceof Model_object) return null;
		
		if($item->loadItem($row) && $item->isLoaded()) {

			if($this->getCaching()) $this->setCachedItem($item);
			return $item;

		}
		
		return null;
      
    }

    private function getCachedItem($id) {
    	  
		$key_value = is_array($id) ? implode(":", $id) : $id;
		$key_checksum = md5($key_value);
		
		if(isset($this->cache[$key_checksum]) && $this->cache[$key_checksum] instanceof Model_object) {
			return $this->cache[$key_checksum];
		}
		
		return null;
      
    }
    
    private function setCachedItem($item) {
      
		if(!$item instanceof Model_object || !$item->isLoaded()) return false;
		$id = $item->getPrimaryKeyFields();
		
		if(is_array($id)) {
		
			$field_value = array();
			
			foreach($id as $id_field) {
				$field_value[] = $item->getFieldValue($id_field);
			}
			
			$key_value = implode(":", $field_value); 
					
		}else{
			$key_value = $item->getFieldValue($id);
		}

		if(isset($key_value) && $key_value != "") {

			$key_checksum = md5($key_value);
			$this->cache[$key_checksum] = $item;

		}
		
		return true;
      
    }
	    
    private function isItemReady() {
		return class_exists($this->item_class);
    }

    private function getCaching() {
		return (boolean) $this->caching;
    }
    
    public function getItemClass() {
		return $this->item_class;
    }

  	public function getTableName($escape = false) {
		return $escape ? '' . $this->table_name . '' : $this->table_name;
  	}

  	public function getFields($escape = false) {

		$esc_colms = array();
		$colms = array_keys($this->fields);
		
		foreach($colms as $colm) {
			$esc_colms[] = $escape ? db_escape_field($colm) : $colm;
		}
		
		return count($esc_colms) < 1 ? null : $esc_colms;
	
  	}
	
	public function isFieldExists($field) {
		return in_array($field, $this->getFields());
	}
	
	public function hasTargetSource() {
		return ($this->isFieldExists('target_source_id') && !$this->skip_target_source);
	}
		
  	public function getPrimaryKeyFields() {
		return count($this->primary_key_fields) == 1 ? $this->primary_key_fields[0] : $this->primary_key_fields;
  	}

	public function setSkipTargetSource($value) {
		$this->skip_target_source = (boolean) $value;
  	}

}

abstract class Model_object {
	
	const DATE_MYSQL = 'Y-m-d H:i:s';
	const EMPTY_DATETIME = '2021-01-01T00:00:00';
	const EMPTY_DATE = '2021/01/01';
	
	protected static $data_types   = array(
	'STRING' => array('char', 'varchar', 'tinytext', 'text', 'mediumtext', 'longtext', 'enum'),
	'INTEGER' => array('int', 'smallint', 'mediumint', 'bigint'),
	'FLOAT' => array('decimal', 'float', 'double', 'real'),
	'DATETIME' => array('date', 'datetime', 'time'),
	'BOOLEAN' => 'tinyint' // In system 'tinyint' will be used only for boolean
	);
	 
  	private $is_new = true;
  	private $is_deleted = false;
  	private $is_loaded = false;

  	private $field_values = array();
  	private $modified_fields = array();

  	private $table_name;
  	private $fields = array();
  	private $primary_key_fields = array();
  	private $auto_increment_field = null;
	
	private $skip_target_source = false;

  	public function __construct($table_name) {
		
		$this->table_name = $table_name;
		
		list($this->fields, $this->primary_key_fields) = db_get_table_attributes($this->table_name);
		if(count($this->primary_key_fields) == 1 && $this->primary_key_fields[0] == 'id') {
			$this->auto_increment_field = 'id';
		}

  	}
	
  	public function fieldExists($field_name) {
		return in_array($field_name, $this->getFields());
  	}
  	
  	public function isPrimaryKeyField($field) {
  	  
		$pks = $this->getPrimaryKeyFields();
  
  		if(is_array($pks)) {
			return in_array($field, $pks);
		} else {
			return $field == $pks;
		}
  	  
  	}
  	  	
  	public function getPrimaryKeyValue() {
		
		$pks = $this->getPrimaryKeyFields();
  		
  		if(is_array($pks)) {
  		
			$ret = array();
			
			foreach ($pks as $field) {
				$ret[$field] = $this->getFieldValue($field);
			}
			
			return $ret;
			
  		} else {
			return $this->getFieldValue($pks);
  		}
  	  
  	}
  	  	
  	public function getFieldValue($field_name, $default = null) {
  	  
		if(isset($this->field_values[$field_name])) return $this->field_values[$field_name];
		return $default;
  	  
  	}
  	
  	public function setFieldValue($field, $value, $is_new_set = false) {

		if(!$this->fieldExists($field) || ($this->getAutoIncrementField() == $field && !$is_new_set ) ) return false;
  		
  		$coverted_value = $this->castOut($value, $this->getFieldType($field));
  		$old_value = $this->getFieldValue($field);
  		
  		if($this->isNew() || ($old_value <> $coverted_value)) {
  		  
			$this->field_values[$field] = $coverted_value;
			$this->addModifiedField($field);
			  		  
  		}
  		
  		return true;
  		
  	}

  	public function setFromAttributes($attributes) {
		
		if(is_array($attributes)) {
			
			foreach($attributes as $k => &$v) {
				$this->setFieldValue($k, $attributes[$k]);
			}
			
		}
		
  	}

  	public function loadItem($row) {
  	  	
		if(is_array($row)) {
		
			foreach ($row as $k => $v) {
			
				if($this->fieldExists($k)) {
					$this->setFieldValue($k, $v, true);
				}
			
			}
			
			$this->setLoaded(true);
			$this->notModified();
			
			return true;
		
		}
		
		return false;
		
  	}
  	
  	public function save($auto_set_time = true) {

		if($this->isNew()) {

			if(!$this->CI_instance()->db->simple_query($this->getInsertSQL($auto_set_time))) {

				$last_db_error = $this->CI_instance()->db->error();
				throw new Exception($last_db_error['message'], $last_db_error['code']);

			}
			
			$autoincrement_field = $this->getAutoIncrementField();
			if($this->fieldExists($autoincrement_field)) {
				$this->setFieldValue($autoincrement_field, $this->CI_instance()->db->insert_id(), true);
			}
			
		} else {
		
		    $sql = $this->getUpdateSQL($auto_set_time);
		    if(is_null($sql)) return true;
		
			if(!$this->CI_instance()->db->simple_query($sql)) {

				$last_db_error = $this->CI_instance()->db->error();
				throw new Exception($last_db_error['message'], $last_db_error['code']);

			}			

		}

		$this->setLoaded(true);
		return true;
  		
  	}
  	
  	public function delete() {

		if($this->isNew() || $this->isDeleted()) {
			return false;
  		}
		
		$delete_sql = "DELETE FROM " . $this->getTableName(true) . " WHERE " . 
		db_prepare_conditions_by_primary_key($this->getPrimaryKeyFields(), $this->getPrimaryKeyValue(), $this->hasTargetSource());
		
		if($this->CI_instance()->db->simple_query($delete_sql)) {
		
			$this->setDeleted(true);
			$this->setLoaded(false);
  		  
			return true;

  		} else {

			$last_db_error = $this->CI_instance()->db->error();
			throw new Exception($last_db_error['message'], $last_db_error['code']);

  		}

  	}
  	
  	private function getInsertSQL($auto_set_time = false) {
  	
  		$fields = array();
  		$values = array();
  		
  		foreach ($this->getFields() as $field) {
						  
			if($this->isFieldModified($field)) {
			
				$fields[] = db_escape_field($field);
				$values[] = db_escape_value($this->castIn($this->getFieldValue($field), $this->getFieldType($field)));
								
			}elseif(($field == 'created_at' || $field == 'updated_at') && $auto_set_time) {

				$timestamp = time();
				$this->setFieldValue($field, $timestamp);

				$fields[] = db_escape_field($field);
				$values[] = db_escape_value($this->castIn($timestamp, $this->getFieldType($field)));

			} elseif ($field == 'target_source_id' && $this->hasTargetSource()) {

				$fields[] = db_escape_field($field);
				$values[] = db_escape_value($this->castIn(get_target_source_id(), $this->getFieldType($field)));

			} 
			  			
		}
		  
		return sprintf("INSERT INTO %s (%s) VALUES (%s)", 
			$this->getTableName(true), 
			implode(', ', $fields), 
			implode(', ', $values)
		);
  		
  	}
  	
  	private function getUpdateSQL($auto_set_time = false) {
  	
  		$fields = array();

  		if(!$this->isItemModified()) return null;
  		
  		foreach ($this->getFields() as $field) {
  			
  			if($this->isFieldModified($field)) {
				$fields[] = sprintf('%s = %s', db_escape_field($field), db_escape_value($this->castIn($this->getFieldValue($field), $this->getFieldType($field))));
			}elseif($field == 'updated_at' && $auto_set_time) {
				
				$timestamp = time();
				$this->setFieldValue($field, $timestamp);
				
				$fields[] = sprintf('%s = %s', db_escape_field($field), db_escape_value($this->castIn($timestamp, $this->getFieldType($field))));
		
			}
  		  
  		}
  		
  		return sprintf("UPDATE %s SET %s WHERE %s", $this->getTableName(true), implode(', ', $fields), db_prepare_conditions_by_primary_key($this->getPrimaryKeyFields(), $this->getPrimaryKeyValue(), $this->hasTargetSource()) );
  		
  	}

  	public function castOut($value, $type) {
  	  
		if(is_null($value)) return null;

		if(in_array($type, self::$data_types['INTEGER'])) {
  	  		return intval($value);
		}elseif(in_array($type, self::$data_types['FLOAT'])) {
			return floatval($value);
		}elseif(in_array($type, self::$data_types['STRING'])) {
			return strval($value);
		}elseif(in_array($type, self::$data_types['DATETIME'])) {

			if(empty($value) || $value == self::EMPTY_DATETIME 
			|| $value == self::EMPTY_DATE) return null;
			
			
			if(is_numeric($value)) {
				return $value;
			}else{
			 	
				$timestamp = strtotime($value);
				return ($timestamp === false) || ($timestamp === -1) ? null : $timestamp;
			
			}

		}elseif($type == self::$data_types['BOOLEAN']) {
			return (boolean) $value;
		}		

		return (string) $value;
		  	  
  	}
  	
  	public function castIn($value, $type) {

		if(in_array($type, self::$data_types['INTEGER'])) {
  	  		return intval($value);
		}elseif(in_array($type, self::$data_types['FLOAT'])) {
			return floatval($value);
		}elseif(in_array($type, self::$data_types['STRING'])) {
			return strval($value);
		}elseif(in_array($type, self::$data_types['DATETIME'])) {

			if(empty($value)) return self::EMPTY_DATETIME;

			if(is_numeric($value)) {
				return date(self::DATE_MYSQL, $value);
			} else {
				return self::EMPTY_DATETIME;
			}

		}elseif($type == self::$data_types['BOOLEAN']) {
			return (boolean) $value ? 1 : 0;
		}		

		return (string) $value;
  	  
  	}
  	  	
  	public function isNew() {
		return (boolean) $this->is_new;
  	}

  	public function setNew($value) {
		$this->is_new = (boolean) $value;
  	}
  	
  	public function isDeleted() {
		return (boolean) $this->is_deleted;
  	}
  	
  	public function setDeleted($value) {
		$this->is_deleted = (boolean) $value;
  	}
  	
  	public function isLoaded() {
		return (boolean) $this->is_loaded;
  	}

  	public function setLoaded($value) {

		$this->is_loaded = (boolean) $value;
		$this->setNew(!$this->is_loaded);

  	}

  	public function isItemModified() {
		return (boolean) count($this->modified_fields);
  	}
  	
  	public function isFieldModified($field_name) {
		return in_array($field_name, $this->modified_fields);
  	}
  	
  	protected function addModifiedField($field_name) {
		if(!in_array($field_name, $this->modified_fields)) $this->modified_fields[] = $field_name;
  	}
  	
  	public function notModified() {
		$this->modified_fields = array();
  	}
  	  		
	public function __call($name, $arguments) {
		
		$init_part = substr($name, 0, 3);
		
		if ($init_part == 'get') {
			return $this->getFieldValue(underscore_string(substr($name, 3)));
		}elseif($init_part == 'set') { 	
			return $this->setFieldValue(underscore_string(substr($name, 3)), $arguments[0]);		
		}
	
	}

	public function CI_instance() {
		
		$CI =& get_instance();
		return $CI;
		
	}

	public function getModelName() {
		return camelize_string($this->getTableName());
	}	
			
  	public function getTableName($escape = false) {
		return $escape ? '' . $this->table_name . '' : $this->table_name;
  	}

  	public function getFields() {
		return array_keys($this->fields);
  	}

  	public function getFieldType($field_name) {
		return $this->fields[$field_name];
  	}

  	public function getPrimaryKeyFields() {
		return count($this->primary_key_fields) == 1 ? $this->primary_key_fields[0] : $this->primary_key_fields;
  	}

  	public function getAutoIncrementField() {
		return $this->auto_increment_field;
  	}

	public function setSkipTargetSource($value) {
		$this->skip_target_source = (boolean) $value;
	}
	
	public function hasTargetSource() {
		return ($this->fieldExists('target_source_id') && !$this->skip_target_source);
	}
	
}

/* Application Model and Object */

abstract class Application_model extends MY_Model {

	public function __construct($table_name, $item_class, $caching = true) {
		parent::__construct($table_name, $item_class, $caching);		
	}	

}

abstract class Application_object extends Model_object {
	
  	public function __construct($table_name) {
		parent::__construct($table_name);
	}	

	public function getTypeName() {
		return strtolower(get_class($this));
	}	
	
	public function isTrashable() {
		return $this->fieldExists("is_trashed");
	}
	
	public function isProjectRelated() {
		return $this->fieldExists("project_id");
	}

	public function isObjectOwner(User $user) {

		if($this->fieldExists("created_by_id") && $user->getId() == $this->getCreatedById()) {
			return true;
		}
				
		return false;
	}

	public function hasParent() {
		return  $this->fieldExists("parent_type") && $this->fieldExists("parent_id");
	}

	public function getParent() {
		
		if($this->hasParent()) {

			$parent_model = $this->getParentType();
			if(isset($parent_model)) {
				return $this->CI_instance()->$parent_model->findById($this->getParentId());
			}
			
		}

		return false;

	}

	public function getCreatedBy() {
		
		if($this->fieldExists("created_by_id")) {
			return $this->CI_instance()->Users->findById($this->getCreatedById());
		}
		
		return false;
	
	}

	public function getTrashedBy() {
		
		if($this->fieldExists("trashed_by_id")) {
			return $this->CI_instance()->Users->findById($this->getTrashedById());
		}
		
		return false;
	
	}

	public function getProject() {
		
		if($this->fieldExists("project_id")) {
			return $this->CI_instance()->Projects->findById($this->getProjectId());
		}
		
		return false;
	
	}

	public function getCreatedByName($with_object_url = false) {

		$created_by = $this->getCreatedBy();
		return ($created_by && $created_by->getIsActive() && !$created_by->getIsTrashed()) ? ($with_object_url && logged_user()->isOwner() ? '<a href="'.get_page_base_url($created_by->getObjectURL()).'">' . $created_by->getName() . '</a>' : $created_by->getName()) : 'n/a';

	}
	
	public function getCompany() {
		
		if($this->fieldExists("company_id")) {
			return $this->CI_instance()->Companies->findById($this->getCompanyId());
		}
		
		return false;
	
	}

	public function save($auto_set_time = true) {
		
		$result = parent::save($auto_set_time);
		if($this->isSearchable()) {

			$this->clearSearchIndex();
			$project = $this->getProject();

			foreach($this->getSearchableFields() as $field_name) {
				$content = $this->getSearchableFieldContent($field_name);
				if(trim($content) <> '') {
					
					$searchable_object = new SearchableObject();

					$searchable_object->setModel($this->getModelName());
					$searchable_object->setObjectId($this->getId());
					$searchable_object->setFieldName($field_name);
					$searchable_object->setFieldContent($content);
					if($project instanceof Project) $searchable_object->setProjectId($project->getId());
					$searchable_object->setIsPrivate($this->getIsPrivate());

					$searchable_object->save();
					
				}
			}

		}
		
		return $result;
	
	}

	function getSearchableFieldContent($field_name) {
		if(!$this->fieldExists($field_name)) {
			throw new Exception("Object field '$field_name' does not exist");
		}
		return (string) $this->getFieldValue($field_name);
	}

	public function isSearchable() {
		return isset($this->is_searchable) && is_array($this->searchable_fields) && count($this->searchable_fields);
	} 

	public function getSearchableFields() {
		if(!$this->isSearchable()) return null;
		return $this->searchable_fields;
	}

	function clearSearchIndex() {
		$this->CI_instance()->SearchableObjects->clearByObject($this);
	}

	public function delete() {
		if($this->isSearchable()) {
			$this->clearSearchIndex();
		}
		return parent::delete(); 
	}

}

/* Helpers */

if (!function_exists('db_escape_field')) {

	function db_escape_field($field) {
		return '' . trim($field) . '';
	}

}

if (!function_exists('db_escape_value')) {

	function db_escape_value($unescaped) {

		$CI = &get_instance();
	
		if(is_null($unescaped)) {
			return 'NULL';
		}
		
		if(is_bool($unescaped)) {
			return $unescaped ? "'1'" : "'0'";
		}
		
		if(is_array($unescaped)) {
		
			$escaped_array = array();
			foreach($unescaped as $unescaped_value) $escaped_array[] = db_escape_value($unescaped_value);
		
			return implode(', ', $escaped_array);
		
		}

		return "'" . $CI->db->escape_str(html_escape($unescaped)) . "'";
	
	}

}

if (!function_exists('db_prepare_conditions')) {

	function db_prepare_conditions($conditions, $table = null, $include_target_source = false) {
	
		if(is_array($conditions)) {
	
			$sql = array_shift($conditions);
			$arguments = count($conditions) ? $conditions : null;
			
			$prepared_conditions = db_prepare_string($sql, $arguments);
	
		} else {
			$prepared_conditions = $conditions;
		}
		
		if($include_target_source && isset($table)) {
			$prepared_conditions .= ($prepared_conditions != "" ? " AND " : " ").$table.".target_source_id = ".get_target_source_id();
		}
		
		// for mssql
		$prepared_conditions = str_replace("", "", $prepared_conditions);

		if (strpos($prepared_conditions, 'NULL')) {
			var_dump($prepared_conditions);
		}
		return $prepared_conditions;
	
	}

}

if (!function_exists('db_prepare_string')) {

	function db_prepare_string($sql, $arguments = null) {
	
		if(is_array($arguments) && count($arguments)) {
	
			foreach($arguments as $argument) {
				$sql = string_replace_first('?', db_escape_value($argument), $sql);
			}
	
		}
		
		return $sql;
		
	}

}

if (!function_exists('db_prepare_conditions_by_primary_key')) {

	function db_prepare_conditions_by_primary_key($pks, $id, $include_target_source = false) {
			
		if(is_array($pks)) {
		
			$where = array();

			foreach($pks as $field) {
				if(isset($id[$field])) {
					$where[] = sprintf('%s = %s', db_escape_field($field), db_escape_value($id[$field]));
				}
			}

			if(is_array($where) && count($where)) {
				$prepared_conditions = (count($where) > 1 ? implode(' AND ', $where) : $where[0]);
			} else {
				$prepared_conditions = "";
			}
		
		} else {
			$prepared_conditions = (sprintf('%s = %s', db_escape_field($pks), db_escape_value($id)));
		}

		if($include_target_source) {
			$prepared_conditions .= ($prepared_conditions != "" ? " AND " : " ") . 
				"target_source_id = ".get_target_source_id();
		}
		
		return $prepared_conditions;
		
	}

}

if (!function_exists('db_get_table_attributes')) {
	
	function db_get_table_attributes($table_name) {
		
		$primary_key_fields = array();
		$fields_data = array();
		
		$CI = &get_instance();
		if ($CI->db->table_exists($table_name)) {
			
			$fields = $CI->db->field_data($table_name);
			foreach ($fields as $field) {
				$fields_data[$field->name] = $field->type;
				// for mssql
				// if ($field->primary_key) $primary_key_fields[] = $field->name;
			}
			
		}
	
		$query = $CI->db->query("select C.COLUMN_NAME FROM  
		INFORMATION_SCHEMA.TABLE_CONSTRAINTS T  
		JOIN INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE C  
		ON C.CONSTRAINT_NAME=T.CONSTRAINT_NAME  
		WHERE  
		C.TABLE_NAME='".$table_name."'  
		and T.CONSTRAINT_TYPE='PRIMARY KEY' ");
		$row = $query->row();
		
		if(isset($row)) {		
			$primary_key_fields[] = $row->COLUMN_NAME;
		}
		
		return array($fields_data, $primary_key_fields);
		
	}

}

if (!function_exists('string_replace_first')) {

	function string_replace_first($search_value, $replace_value, $string) {
	
		$position = strpos($string, $search_value);
		
		if($position === false) {
		  return $string;
		}else{
		  return substr($string, 0, $position).$replace_value.substr($string, $position + strlen($search_value), strlen($string));
		}
	
	}

}

if (!function_exists('underscore_string')) {

   function underscore_string($camel_cased_word) {
	
		$camel_cased_word = preg_replace('/([A-Z]+)([A-Z])/','\1_\2', $camel_cased_word);
		return strtolower(preg_replace('/([a-z])([A-Z])/','\1_\2', $camel_cased_word));
    
	}
	
}

if (!function_exists('camelize_string')) {

	function camelize_string($lower_case_and_underscored_word) {
		return str_replace(" ","",ucwords(str_replace("_"," ",$lower_case_and_underscored_word)));
	}

}