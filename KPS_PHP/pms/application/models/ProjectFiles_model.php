<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ProjectFiles Model */

class ProjectFiles_model extends Application_model {

    function __construct() {
        parent::__construct('project_files', 'ProjectFile');
    }

	public function getByAccessKey($access_key) {
		return  $this->find(array('conditions' => array('access_key = ?', $access_key), 'one' => true));
	}

	public function getByParent(Application_object $parent_object, $include_private = false, $include_trashed = false) {

		return $this->find(array('conditions' => array('is_trashed <= ? AND is_private <= ? AND parent_type = ? AND parent_id = ?', 
		$include_trashed, $include_private, $parent_object->getModelName(), $parent_object->getId()), 'order' => 'created_at DESC'));

	}

	public function getByProject(Project $project, $include_trashed = false, $include_private = false, $limit = null, $offset = null) {
		
		return $this->find(array('conditions' => array('is_trashed <= ? AND is_private <= ? AND project_id = ?', 
		$include_trashed, $include_private, $project->getId()), 
		'order' => 'created_at DESC',
		'limit' => $limit,
        'offset' => $offset
		));
						
	}

	public function getByTrashed() {
		return $this->find(array('conditions' => array('is_trashed = ?', true), 'order' => 'file_name'));
	}

	public function allStorageSize() {

		$project_files_table = $this->getTableName(true);		
		
		$query = $this->db->query("SELECT SUM(file_size) as storage_size FROM ".$project_files_table." 
		WHERE target_source_id = ".get_target_source_id());

		return $query->result();

	}

	public function updateStorageCache($target_source) {

		$all_storage_size = $this->allStorageSize();
		if(isset($all_storage_size) && is_array($all_storage_size) && count($all_storage_size) == 1) {
			$total_storage_used = $all_storage_size[0]->storage_size;
		} else {
			$total_storage_used = 0;
		}
		
		$target_source->setStorageUsed($total_storage_used);
		$target_source->save();

	}

	public function saveFile($attach_file, $can_download, Application_object $attach_to){

		$target_source = logged_user()->getTargetSource();
		$storage_limit_in_bytes = ($target_source->getStorageLimit() * 1073741824);

		if($target_source->getStorageUsed() > $storage_limit_in_bytes) {
			return false;
		} else {

			if(is_array($attach_file) && $attach_file['name'] != "") {
				
				$file_name = basename($attach_file['name']);
				$file_extension = get_file_extension($file_name);
				$file_type = $attach_file['type'];
				$file_size = $attach_file['size'];
				$file_source = $attach_file['tmp_name'];
				
				$file_repository = new FileRepository();
				if($repo_id = $file_repository->updateFileContent($file_source)) {

					try{

						$project_file = new ProjectFile();
						
						if($attach_to instanceof Project) {
							$project_file->setProjectId($attach_to->getId());
						} else {

							$project_file->setProjectId($attach_to->getProjectId());
							$project_file->setParentType($attach_to->getModelName());
							$project_file->setParentId($attach_to->getId());

						}

						$project_file->setAccessKey(sha1(uniqid(rand(), true).$attach_to->getId().$file_source.time()));
						$project_file->setCanDownload($can_download);
						$project_file->setFileName($file_name);
						$project_file->setFileRepositoryId($repo_id);
						$project_file->setFileExtension($file_extension);
						$project_file->setFileTypeString($file_type);
						$project_file->setFileSize($file_size);
						
						$project_file->setCreatedById(logged_user()->getId());
						$project_file->save();
	
						$this->updateStorageCache($target_source);

						return $project_file;
						
					} catch(Exception $e) {
									
						if(isset($repo_id)) {
							
							if(isset($file_repository) && $file_repository instanceof FileRepository) {
								$file_repository->deleteFile($repo_id);
							}
							
						}

						return false;
											
					}
									
				}
				
			}

			return true;
		}

	}

}

/* ProjectFile Object */

class ProjectFile extends Application_object {

	protected $is_searchable = true;
	protected $searchable_fields = array('file_name');

	function __construct() {
		parent::__construct('project_files');
	}

	public function getName() {
		return $this->getFileName();
	}

	public function moveToTrash() {
		
		$this->setIsTrashed(true);
		$this->save();
			
	}

	public function restoreFromTrash() {
		
		$this->setIsTrashed(false);
		$this->save();

	}
	
	function delete(){

		$file_repository = new FileRepository();
		$file_repository->deleteFile($this->getFileRepositoryId());

		$deleted = parent::delete();

		$CI =& get_instance();
		$target_source = logged_user()->getTargetSource();
		$CI->ProjectFiles->updateStorageCache($target_source);

		return $deleted;

	}

	/* Static URLs */

    function getShowFileURL() {
		return 'files/show/'.$this->getId();
	}

    function getHideFileURL() {
		return 'files/hide/'.$this->getId();
	}

    function getObjectURL() {
		return 'files/download/'.$this->getAccessKey();
	}

}