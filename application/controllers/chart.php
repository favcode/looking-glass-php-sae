<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * Chart Class
 * 生成图表数据
 *
 * @package LookingGlassSAE
 * @subpackage Controllers
 * @category Controller
 * @author WDLTH
 * @link http://www.wdlth.com/
 */
class Chart extends CI_Controller {
	function __construct() {
		parent::__construct ();
	}
	public function index() {
		exit ( '403 Forbidden.' );
	}
	public function telecom() {
		
		$CI =& get_instance();
		$CI->load->driver('cache');
		
		$xml = NULL;
		$formattedXML = NULL;
		
		if($CI->cache->memcached->get('TelecomChartData') == "")
		{
			$timelinearr = array ();
			$n = 1;
			if (intval ( date ( "i" ) ) > 35 || intval ( date ( "i" ) ) <= 15) {
				while ( $n <= 12 ) {
					$timelinearr [$n] = date ( "H", strtotime ( "-" . 30 * $n . " minutes" ) ) . ":30";
					$timelinearr [$n + 1] = date ( "H", strtotime ( "-" . 30 * $n . " minutes" ) ) . ":00";
					$n = $n + 2;
				}
			} else {
				while ( $n <= 12 ) {
					$timelinearr [$n] = date ( "H", strtotime ( "-" . 30 * $n . " minutes" ) ) . ":00";
					$timelinearr [$n + 1] = date ( "H", strtotime ( "-" . 30 * $n . " minutes" ) ) . ":30";
					$n = $n + 2;
				}
			}
			$timelinearr = array_reverse ( $timelinearr );
			
			$xml = new SimpleXMLElement ( '<chart/>' );
			$categories = $xml->addChild ( 'categories' );
			for($j = 0; $j < count ( $timelinearr ); $j ++) {
				$categories->addChild ( 'item', $timelinearr [$j] );
			}
			
			$this->load->library ( 'Dblib' );
			$sourcearr = $this->dblib->getChartSource ();
			foreach ( $sourcearr as $source ) {
				$series = $xml->addChild ( 'series' );
				$series->addChild ( 'name', $source ["source"] );
				$data = $series->addChild ( 'data' );
				$pointarr = $this->dblib->getChartPoint ( $source ["source"], 'ChinaTelecom' );
				for($l = 0; $l < 12; $l ++) {
					$data->addChild ( 'point', intval ( $pointarr [$l] ["avg"] ) );
				}
				for($l = 0; $l < 12; $l ++) {
					$data->addChild ( 'loss', intval ( $pointarr [$l] ["loss"] ) );
				}
			}
			
			$CI->cache->memcached->save('TelecomChartData', $xml->asXML(), 1500);
			log_message('notice', "Saved \"TelecomChartData\" to memcached");
			
			$dom = new DOMDocument ();
			$dom->loadXML ( $xml->asXML() );
			$dom->encoding = "utf-8";
			$dom->formatOutput = TRUE;
			$formattedXML = $dom->saveXML ();
		}else{
			$xml = $CI->cache->memcached->get('TelecomChartData');
			log_message('notice', "Loaded \"TelecomChartData\" from memcached");
			
			$dom = new DOMDocument ();
			$dom->loadXML ( $xml );
			$dom->encoding = "utf-8";
			$dom->formatOutput = TRUE;
			$formattedXML = $dom->saveXML ();			
		}
		
		header ( 'Content-type: text/xml' );
		echo $formattedXML;
	}
	
	public function unicom() {
		
		$CI =& get_instance();
		$CI->load->driver('cache');
		
		$xml = NULL;
		$formattedXML = NULL;
		
		if($CI->cache->memcached->get('UnicomChartData') == "")
		{
			$timelinearr = array ();
			$n = 1;
			if (intval ( date ( "i" ) ) > 35 || intval ( date ( "i" ) ) <= 15) {
				while ( $n <= 12 ) {
					$timelinearr [$n] = date ( "H", strtotime ( "-" . 30 * $n . " minutes" ) ) . ":30";
					$timelinearr [$n + 1] = date ( "H", strtotime ( "-" . 30 * $n . " minutes" ) ) . ":00";
					$n = $n + 2;
				}
			} else {
				while ( $n <= 12 ) {
					$timelinearr [$n] = date ( "H", strtotime ( "-" . 30 * $n . " minutes" ) ) . ":00";
					$timelinearr [$n + 1] = date ( "H", strtotime ( "-" . 30 * $n . " minutes" ) ) . ":30";
					$n = $n + 2;
				}
			}
			$timelinearr = array_reverse ( $timelinearr );
			
			$xml = new SimpleXMLElement ( '<chart/>' );
			$categories = $xml->addChild ( 'categories' );
			for($j = 0; $j < count ( $timelinearr ); $j ++) {
				$categories->addChild ( 'item', $timelinearr [$j] );
			}
			
			$this->load->library ( 'Dblib' );
			$sourcearr = $this->dblib->getChartSource ();
			foreach ( $sourcearr as $source ) {
				$series = $xml->addChild ( 'series' );
				$series->addChild ( 'name', $source ["source"] );
				$data = $series->addChild ( 'data' );
				$pointarr = $this->dblib->getChartPoint ( $source ["source"], 'ChinaUnicom' );
				for($l = 0; $l < 12; $l ++) {
					$data->addChild ( 'point', intval ( $pointarr [$l] ["avg"] ) );
				}
				for($l = 0; $l < 12; $l ++) {
					$data->addChild ( 'loss', intval ( $pointarr [$l] ["loss"] ) );
				}
			}
			
			$CI->cache->memcached->save('UnicomChartData', $xml->asXML(), 1500);
			log_message('notice', "Saved \"UnicomChartData\" to memcached");
				
			$dom = new DOMDocument ();
			$dom->loadXML ( $xml->asXML() );
			$dom->encoding = "utf-8";
			$dom->formatOutput = TRUE;
			$formattedXML = $dom->saveXML ();
		}else{
			$xml = $CI->cache->memcached->get('UnicomChartData');
			log_message('notice', "Loaded \"UnicomChartData\" from memcached");
			
			$dom = new DOMDocument ();
			$dom->loadXML ( $xml );
			$dom->encoding = "utf-8";
			$dom->formatOutput = TRUE;
			$formattedXML = $dom->saveXML ();			
		}
		
		header ( 'Content-type: text/xml' );
		echo $formattedXML;
	}
	
	public function mobile() {
		
		$CI =& get_instance();
		$CI->load->driver('cache');
		
		$xml = NULL;
		$formattedXML = NULL;
		
		if($CI->cache->memcached->get('MobileChartData') == "")
		{
			$timelinearr = array ();
			$n = 1;
			if (intval ( date ( "i" ) ) > 35 || intval ( date ( "i" ) ) <= 15) {
				while ( $n <= 12 ) {
					$timelinearr [$n] = date ( "H", strtotime ( "-" . 30 * $n . " minutes" ) ) . ":30";
					$timelinearr [$n + 1] = date ( "H", strtotime ( "-" . 30 * $n . " minutes" ) ) . ":00";
					$n = $n + 2;
				}
			} else {
				while ( $n <= 12 ) {
					$timelinearr [$n] = date ( "H", strtotime ( "-" . 30 * $n . " minutes" ) ) . ":00";
					$timelinearr [$n + 1] = date ( "H", strtotime ( "-" . 30 * $n . " minutes" ) ) . ":30";
					$n = $n + 2;
				}
			}
			$timelinearr = array_reverse ( $timelinearr );
			
			$xml = new SimpleXMLElement ( '<chart/>' );
			$categories = $xml->addChild ( 'categories' );
			for($j = 0; $j < count ( $timelinearr ); $j ++) {
				$categories->addChild ( 'item', $timelinearr [$j] );
			}
			
			$this->load->library ( 'Dblib' );
			$sourcearr = $this->dblib->getChartSource ();
			foreach ( $sourcearr as $source ) {
				$series = $xml->addChild ( 'series' );
				$series->addChild ( 'name', $source ["source"] );
				$data = $series->addChild ( 'data' );
				$pointarr = $this->dblib->getChartPoint ( $source ["source"], 'ChinaMobile' );
				for($l = 0; $l < 12; $l ++) {
					$data->addChild ( 'point', intval ( $pointarr [$l] ["avg"] ) );
				}
				for($l = 0; $l < 12; $l ++) {
					$data->addChild ( 'loss', intval ( $pointarr [$l] ["loss"] ) );
				}
			}
			
			$CI->cache->memcached->save('MobileChartData', $xml->asXML(), 1500);
			log_message('notice', "Saved \"MobileChartData\" to memcached");
			
			$dom = new DOMDocument ();
			$dom->loadXML ( $xml->asXML() );
			$dom->encoding = "utf-8";
			$dom->formatOutput = TRUE;
			$formattedXML = $dom->saveXML ();
		}else{
			$xml = $CI->cache->memcached->get('MobileChartData');
			log_message('notice', "Loaded \"MobileChartData\" from memcached");
			
			$dom = new DOMDocument ();
			$dom->loadXML ( $xml );
			$dom->encoding = "utf-8";
			$dom->formatOutput = TRUE;
			$formattedXML = $dom->saveXML ();			
		}
		
		header ( 'Content-type: text/xml' );
		echo $formattedXML;
	}
	public function point() {
		$this->load->library ( 'Dblib' );
		$sourcearr = $this->dblib->getChartSource ();
		$xml = new SimpleXMLElement ( '<chart/>' );
		foreach ( $sourcearr as $source ) {
			$series = $xml->addChild ( 'series' );
			$series->addChild ( 'name', $source ["source"] );
			$data = $series->addChild ( 'data' );
			$pointarr = $this->dblib->getChartPoint ( $source ["source"], 'ChinaTelecom' );
			for($l = 0; $l < 12; $l ++) {
				$data->addChild ( 'point', intval ( $pointarr [$l] ["avg"] ) );
			}
		}
		header ( 'Content-type: text/xml' );
		echo $xml->asXML ();
	}
	public function timeline() {
		$timelinearr = array ();
		$n = 1;
		if (intval ( date ( "i" ) ) > 35 || intval ( date ( "i" ) ) <= 15) {
			while ( $n <= 12 ) {
				$timelinearr [$n] = date ( "H", strtotime ( "-" . 30 * $n . " minutes" ) ) . ":30";
				$timelinearr [$n + 1] = date ( "H", strtotime ( "-" . 30 * $n . " minutes" ) ) . ":00";
				$n = $n + 2;
			}
		} else {
			while ( $n <= 12 ) {
				$timelinearr [$n] = date ( "H", strtotime ( "-" . 30 * $n . " minutes" ) ) . ":00";
				$timelinearr [$n + 1] = date ( "H", strtotime ( "-" . 30 * $n . " minutes" ) ) . ":30";
				$n = $n + 2;
			}
		}
		$timelinearr = array_reverse ( $timelinearr );
		
		$xml = new SimpleXMLElement ( '<chart/>' );
		$categories = $xml->addChild ( 'categories' );
		for($j = 0; $j < count ( $timelinearr ); $j ++) {
			$categories->addChild ( 'item', $timelinearr [$j] );
		}
		header ( 'Content-type: text/xml' );
		echo $xml->asXML ();
	}
}