<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author Hazem Khaled <Hazem.Khaled@gmail.com>
 * 			HazemKhaled.com
 */



$config['backend']['source_dir']	= 'back-end';


$config['backend']['default_style']	= 'bloganje';
$config['backend']['multi_style']	= true; // allow modratrs to use another style

// tables
$config['backend']['tables']['users']	= 'modrators';
$config['backend']['tables']['rules']	= 'rules';
$config['backend']['tables']['log']		= 'log';