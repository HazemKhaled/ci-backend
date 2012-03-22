<?php
class Arabtest extends CI_Controller {

	function index() {
		echo "hi";
		$params = array();
		$this -> load -> library('arabic', $params);

		$this -> arabic -> load('Date');
		$this -> arabic -> setMode(1);
		$hdate = $this -> arabic -> date('l dS F Y h:i:s A', time());

		$this -> arabic -> load('Transliteration');
		$translit = $this -> arabic -> en2ar('Just Some Name');

		$data['hdate'] = $hdate;
		$data['translit'] = $translit;

		$this -> load -> view('arabic_view', $data);
	}

}
