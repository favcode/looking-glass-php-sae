<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dblib Class
 * 数据库操作
 *
 * @package		LookingGlassSAE
 * @subpackage	Libraries
 * @category	Library
 * @author		WDLTH
 * @link		http://www.wdlth.com/
 */

class Dblib {
	
	public function getChartPoint($source, $destination)
	{
		$mysql = new SaeMysql();
	
		$sql = "SELECT * FROM `ping` WHERE `source`='" . $mysql->escape($source) . "' AND `destination`='" . $mysql->escape($destination) . "' ORDER BY `time` DESC LIMIT 0,12";
	
		$resultArray = $mysql -> getData($sql);
	
		if( $mysql->errno() != 0 )
		{
			log_message('error', "MySQL Error: " . $mysql->errmsg());
			//die( "Error: " . $mysql->errmsg() );
			die($sql);
		}
	
		$mysql->closeDb();
	
		return $resultArray;
	}
	
	public function getChartDestination()
	{
		$mysql = new SaeMysql();
	
		$sql = "SELECT `destination` FROM `ping` GROUP BY `destination`";
	
		$resultArray = $mysql -> getData($sql);
	
		if( $mysql->errno() != 0 )
		{
			log_message('error', "MySQL Error: " . $mysql->errmsg());
			//die( "Error: " . $mysql->errmsg() );
			die($sql);
		}
	
		$mysql->closeDb();
	
		return $resultArray;
	}
	
	public function getChartSource()
	{
		$CI =& get_instance();
		$CI->load->driver('cache');
		
		$resultArray = array();
		
		if($CI->cache->memcached->get('ChartSource') == "")
		{
			$mysql = new SaeMysql();
			
			$sql = "SELECT `source` FROM `ping` GROUP BY `source`";
			
			$resultArray = $mysql -> getData($sql);
			
			if( $mysql->errno() != 0 )
			{
				log_message('error', "MySQL Error: " . $mysql->errmsg());
				//die( "Error: " . $mysql->errmsg() );
				die($sql);
			}
			
			$CI->cache->memcached->save('ChartSource', $resultArray, 1500);
			log_message('notice', "Saved \"ChartSource\" to memcached");
			$mysql->closeDb();
		}else{
			$resultArray = $CI->cache->memcached->get('ChartSource');
			log_message('notice', "Loaded \"ChartSource\" from memcached");
		}
		
		return $resultArray;
	}
	
	public function getHomepageChart($destination)
	{
		$mysql = new SaeMysql();
	
		$sql = "SELECT * FROM (SELECT `source`,`avg`,`time` FROM `ping` WHERE `destination`='" . $mysql->escape($destination) . "' ORDER BY `time` DESC LIMIT 0,60) AS P GROUP BY `source` ORDER BY `time` DESC";
	
		$resultArray = $mysql -> getData($sql);
	
		if( $mysql->errno() != 0 )
		{
			log_message('error', "MySQL Error: " . $mysql->errmsg());
			//die( "Error: " . $mysql->errmsg() );
			die($sql);
		}
	
		$mysql->closeDb();
	
		return $resultArray;
	}
	
	public function gethomepageping($destination)
	{
		$CI =& get_instance();
		$CI->load->driver('cache');
		
		$resultArray = array();
		
		if($CI->cache->memcached->get('HomepagePing_' . $destination) == "")
		{
			$mysql = new SaeMysql();
			
			$sql = "SELECT * FROM (SELECT * FROM `ping` WHERE `destination`='" . $mysql->escape($destination) . "' ORDER BY `time` DESC LIMIT 0,60) AS P GROUP BY `source` ORDER BY `time` DESC";
			
			$resultArray = $mysql -> getData($sql);
			
			if( $mysql->errno() != 0 )
			{
				log_message('error', "MySQL Error: " . $mysql->errmsg());
				//die( "Error: " . $mysql->errmsg() );
				die($sql);
			}
			
			foreach ($resultArray as &$resultItem)
			{
				switch($resultItem["source"])
				{
					case "Krypt(VPLS)":
						$resultItem["source"] = "美国洛杉矶、圣安娜Krypt机房(VPLS)";
						break;
					case "Sakura":
						$resultItem["source"] = "日本东京、大阪樱花机房(Sakura)";
						break;
					case "VR HK":
						$resultItem["source"] = "香港新世界机房(VR HK)";
						break;
					case "PCCW HK":
						$resultItem["source"] = "香港电讯盈科机房(PCCW HK)";
						break;
					case "EGI":
						$resultItem["source"] = "美国圣何塞EGIHosting机房";
						break;
					case "Peer1":
						$resultItem["source"] = "美国洛杉矶Peer1机房(电信直连)";
						break;
					case "ServerCentral Tokyo":
						$resultItem["source"] = "日本东京Equinix机房ServerCentral(VPS.Net, VULTR)";
						break;
					case "OneAsiaHost":
						$resultItem["source"] = "新加坡OneAsiaHost机房";
						break;
					case "MultaCOM":
						$resultItem["source"] = "美国洛杉矶MultaCOM机房";
						break;
					case "SunnyVision":
						$resultItem["source"] = "香港新力讯机房(SunnyVision)";
						break;
					case "KDDI Tokyo":
						$resultItem["source"] = "日本东京KDDI机房(Linode)";
						break;
					case "QuadraNet":
						$resultItem["source"] = "美国洛杉矶QuadraNet机房";
						break;
					case "ColoCrossing":
						$resultItem["source"] = "美国洛杉矶ColoCrossing机房";
						break;
					case "Fiberhub":
						$resultItem["source"] = "美国拉斯维加斯FiberHub机房（Versaweb）";
						break;
				}
			}
			unset($resultItem);
			
			$CI->cache->memcached->save('HomepagePing_' . $destination, $resultArray, 600);
			log_message('notice', "Saved HomepagePing_" . $destination . " to memcached");
			
			$mysql->closeDb();
			
		}else{
			$resultArray = $CI->cache->memcached->get('HomepagePing_' . $destination);
			log_message('notice', "Loaded HomepagePing_" . $destination . " from memcached");
		}

		
		return $resultArray;
	}
	
	public function getStaticPagePingByDate($source, $destination, $starttimestamp, $endtimestamp)
	{
		$mysql = new SaeMysql();
	
		$sql = "SELECT * FROM `ping` WHERE `source`='" . $mysql->escape($source) . "' AND `destination`='" . $mysql->escape($destination) . "' AND unix_timestamp(time) BETWEEN " . $starttimestamp . " AND " . $endtimestamp . " ORDER BY `time` DESC LIMIT 0,60";
	
		$resultArray = $mysql -> getData($sql);
	
		if( $mysql->errno() != 0 )
		{
			log_message('error', "MySQL Error: " . $mysql->errmsg());
			//die( "Error: " . $mysql->errmsg() );
			die($sql);
		}
	
		$mysql->closeDb();
	
		return $resultArray;
	}
	
	public function saveping($source, $destination, $min, $avg, $max, $loss)
	{
		$mysql = new SaeMysql();
		
		$sql = "INSERT INTO `ping` (`source`, `destination`, `min`, `avg`, `max`, `loss`, `time`)" .
				" VALUES ('" . $mysql->escape($source) . "', '" . $mysql->escape($destination) ."', " .
				$min . ", " . $avg . ", " . $max . ", " . $loss . ", NOW())";
		
		$mysql -> runSql($sql);
		if( $mysql->errno() != 0 )
		{
			log_message('error', "MySQL Error: " . $mysql->errmsg());
			//die( "Error: " . $mysql->errmsg() );
			die($sql);
		}
		
		$mysql->closeDb();
	}
	
}
