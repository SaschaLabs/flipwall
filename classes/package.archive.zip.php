<?php
if ( ! defined( 'FLIPWALL_VERSION' ) ) exit; // Exit if accessed directly
require_once (FLIPWALL_PLUGIN_PATH . 'classes/package.archive.php');

/**
 *  SLFW_ZIP
 *  Creates a zip file using the built in PHP ZipArchive class
 */
class SLFW_Zip  extends SLFW_Archive {
	
	//PRIVATE 
	private static $compressDir;	
	private static $countDirs  = 0;
	private static $countFiles = 0;
	private static $sqlPath;
	private static $zipPath;
	private static $zipFileSize;
	private static $zipArchive;
	
	private static $limitItems = 0;
	private static $networkFlush = false;
	private static $scanReport;
	
	/**
     *  CREATE
     *  Creates the zip file and adds the SQL file to the archive
     */
	static public function Create(SLFW_Archive $archive) {
		  try {
		    
			$timerAllStart = SLFW_Util::GetMicrotime();
			$package_zip_flush = SLFW_Settings::Get('package_zip_flush');
			
			self::$compressDir		= rtrim(SLFW_Util::SafePath($archive->PackDir), '/');
			self::$sqlPath			= SLFW_Util::SafePath("{$archive->Package->StorePath}/{$archive->Package->Database->File}");
			self::$zipPath			= SLFW_Util::SafePath("{$archive->Package->StorePath}/{$archive->File}");
			self::$zipArchive		= new ZipArchive();
			self::$networkFlush		= empty($package_zip_flush) ? false : $package_zip_flush;
			
			$filterDirs = empty($archive->FilterDirs) ? 'not set' : $archive->FilterDirs;
			$filterExts = empty($archive->FilterExts) ? 'not set' : $archive->FilterExts;
			$filterOn   = ($archive->FilterOn) ? 'ON' : 'OFF';
			
			//LOAD SCAN REPORT
			$json = file_get_contents(FLIPWALL_SSDIR_PATH_TMP . "/{$archive->Package->NameHash}_scan.json");
			self::$scanReport = json_decode($json);
			
			SLFW_Log::Info("\n********************************************************************************");
			SLFW_Log::Info("ARCHIVE (ZIP):");
			SLFW_Log::Info("********************************************************************************");
			$isZipOpen = (self::$zipArchive->open(self::$zipPath, ZIPARCHIVE::CREATE) === TRUE);
			if (! $isZipOpen){
				SLFW_Log::Error("Cannot open zip file with PHP ZipArchive.", "Path location [" . self::$zipPath . "]");
			}
            SLFW_Log::Info("ARCHIVE DIR:  " . self::$compressDir);
            SLFW_Log::Info("ARCHIVE FILE: " . basename(self::$zipPath));
			SLFW_Log::Info("FILTERS: *{$filterOn}*");
			SLFW_Log::Info("DIRS:  {$filterDirs}");
			SLFW_Log::Info("EXTS:  {$filterExts}");
			
			SLFW_Log::Info("----------------------------------------");
			SLFW_Log::Info("COMPRESSING");
			SLFW_Log::Info("SIZE:\t" . self::$scanReport->ARC->Size);
			SLFW_Log::Info("STATS:\tDirs " . self::$scanReport->ARC->DirCount . " | Files " . self::$scanReport->ARC->FileCount . " | Links " . self::$scanReport->ARC->LinkCount);
			
			//ADD SQL 
			$isSQLInZip = self::$zipArchive->addFile(self::$sqlPath, "database.sql");
			if ($isSQLInZip)  {
				SLFW_Log::Info("SQL ADDED: " . basename(self::$sqlPath));
			} else {
				SLFW_Log::Error("Unable to add database.sql to archive.", "SQL File Path [" . self::$sqlath . "]");
			}
			self::$zipArchive->close();
			self::$zipArchive->open(self::$zipPath, ZipArchive::CREATE);
			
			//ZIP DIRECTORIES
			foreach(self::$scanReport->ARC->Dirs as $dir){
				if (self::$zipArchive->addEmptyDir(ltrim(str_replace(self::$compressDir, '', $dir), '/'))) {
					self::$countDirs++;
				} else {
					SLFW_Log::Info("WARNING: Unable to zip directory: '{$dir}'");
				}
			}
		
			/* ZIP FILES: Network Flush
			*  This allows the process to not timeout on fcgi 
			*  setups that need a response every X seconds */
			if (self::$networkFlush) {
				foreach(self::$scanReport->ARC->Files as $file) {
					if (self::$zipArchive->addFile($file, ltrim(str_replace(self::$compressDir, '', $file), '/'))) {
						self::$limitItems++;
						self::$countFiles++;
					} else {
						SLFW_Log::Info("WARNING: Unable to zip file: {$file}");
					}
					//Trigger a flush to the web server after so many files have been loaded.
					if(self::$limitItems > FLIPWALL_ZIP_FLUSH_TRIGGER) {
						$sumItems = (self::$countDirs + self::$countFiles);
						self::$zipArchive->close();
						self::$zipArchive->open(self::$zipPath);
						self::$limitItems = 0;
						SLFW_Util::FcgiFlush();
						SLFW_Log::Info("Items archived [{$sumItems}] flushing response.");
					}
				}
			//Normal
			} else {
				foreach(self::$scanReport->ARC->Files as $file) {
					if (self::$zipArchive->addFile($file, ltrim(str_replace(self::$compressDir, '', $file), '/'))) {
						self::$countFiles++;
					} else {
						SLFW_Log::Info("WARNING: Unable to zip file: {$file}");
					}
				}
			}
			
			SLFW_Log::Info(print_r(self::$zipArchive, true));

			//--------------------------------
			//LOG FINAL RESULTS
			SLFW_Util::FcgiFlush();
            $zipCloseResult = self::$zipArchive->close();
			($zipCloseResult) 
				? SLFW_Log::Info("COMPRESSION RESULT: '{$zipCloseResult}'")
				: SLFW_Log::Error("ZipArchive close failure.", "This hosted server may have a disk quota limit.\nCheck to make sure this archive file can be stored.");
		
            $timerAllEnd = SLFW_Util::GetMicrotime();
            $timerAllSum = SLFW_Util::ElapsedTime($timerAllEnd, $timerAllStart);
			
			self::$zipFileSize = @filesize(self::$zipPath);
			SLFW_Log::Info("COMPRESSED SIZE: " . SLFW_Util::ByteSize(self::$zipFileSize));
            SLFW_Log::Info("ARCHIVE RUNTIME: {$timerAllSum}");
        } 
        catch (Exception $e) {
			SLFW_Log::Error("Runtime error in package.archive.zip.php constructor.", "Exception: {$e}");
        }
	}
	
}
?>