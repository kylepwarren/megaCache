<?php
//needs to be able to write to nodes directory
define("MEGA_CACHE_NODES", 10000);
define("MEGA_CACHE_SUBDIRS", 10);// = 10000 * 10 cache files + 10 subfolders

class MegaCache{

  function cache_put_contents($path,$contents){
		$zip = new ZipArchive();		
		$nodePath = $this->getNodePath($path);
		if(!file_exists($nodePath)){
			$zip->open($nodePath, ZIPARCHIVE::CREATE);			
		}
		else{
			$zip->open($nodePath);			
		}
		$packageWrap = array('archiveTime' => time(),'contents' => $contents,'hits' => 1);
		$zip->addFromString($path, serialize($packageWrap));
		$zip->close();
		return true;
	}

	function cache_get_contents($path,$maxAgeSeconds=false){
		$zip = new ZipArchive();		
		$nodePath = $this->getNodePath($path);
		if(!file_exists($nodePath)){
			return false;			
		}

		$zip->open($nodePath);			
		$packageWrap = $zip->getFromName($path);
		$package = array();
		if($packageWrap != false){
			$package = unserialize($packageWrap);
			//$archiveTime = $package['archiveTime'];
			//$packageContents = $package['contents'];
			$ageSeconds = time() - $package['archiveTime'];
			if($maxAgeSeconds == false || $maxAgeSeconds <= 0 || $ageSeconds < $maxAgeSeconds){
				//$contents = $package['contents'];
				$packageHits=1;
				if(isset($package['hits']) && $package['hits'] > 0){
					$packageHits = $package['hits'];
				}
				$packageHits++;
				$package['hits'] = $packageHits;
				//print_r(debug_backtrace());
				$zip->deleteName($path);//adding this for some reason fixed a memory usage problem
				$zip->addFromString($path, serialize($package));
			}
			else{
				//remove it? diskspace unlmited, but still performance
				//wait wait wait wait, will almost definitely be updated.. hmm...
				$zip->deleteName($path);
				//screw it
			}
		}
		else{
			$package['contents'] = false;
		}		
		$zip->close();
		return $package['contents'];	
	}
	
	function getNodePath($path){
		$md1 = md5($path);
		$cacheSubFolder = dirname(__file__).'/nodes/f_'.hexdec(substr(md5($md1),0,5)) % MEGA_CACHE_SUBDIRS;
		if(!is_dir($cacheSubFolder)){
			mkdir($cacheSubFolder);
		}
		$nodeId = 'node_'.hexdec(substr($md1,0,5)) % MEGA_CACHE_NODES;
		return $cacheSubFolder.'/'.$nodeId;
	}
	
	
}

?>
