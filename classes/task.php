<?php
if ( ! defined( 'FLIPWALL_VERSION' ) ) exit; // Exit if accessed directly

class SLFW_Task {
		

	public $Package;
	public $MsgRunning;

	function __construct() {

	}
	
	public function Create() {
		
		$Package = new SLFW_Package();
		$Package = $Package->GetActive();
		$this->Package = $Package->Build();
		$this->moveFromTmp();

	}
	

	private function moveFromTmp() {
		
		$files   = glob(FLIPWALL_SSDIR_PATH_TMP . "/{*.zip,*.sql,*.php}", GLOB_BRACE);
		$newPath = FLIPWALL_SSDIR_PATH;
		
		if (function_exists('rename')) {
			foreach($files as $file){
				$name = basename($file);
				rename($file,"{$newPath}/{$name}");
			}
		} else {
			foreach($files as $file){
				$name = basename($file);
				copy($file,"{$newPath}/{$name}");
				unlink($file);
			}
		}
	}

}
?>