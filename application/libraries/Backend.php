<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Shopping Cart Class
 *
 * @package		CI-Backend
 * @subpackage	Libraries
 * @category	Backend
 * @author		Hazem Khaled <hazemkhaled@gmail.com> http://HazemKhaled.com
 * @link		http://ci-backend.HazemKhaled.com
 */
class CI_Backend {

	private $CI;
	public $rules = array();
	public $conf;
	public $style;
	public $num_rows = 0;
	public $source = array();
	public $tabs = array();
	public $op = array('equal'				=> '=',
						'not_equal'			=> '!=',
						'acres'				=> '>',
						'less'				=> '<',
						'acres_or_equal'	=> '>=',
						'less_or_equal'		=> '<=',
						'like_text'			=> '%');

	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->library('session');
		$this->CI->load->helper(array('backend', 'language', 'url', 'form'));
		$this->CI->load->model('backend_model');
		$this->CI->config->load('backend');
		$this->CI->load->language('backend', $this->CI->config->item('language'));

		$this->conf = $this->CI->config->item('backend');

		$this->parseFiles();
		$this->parseTabs();
		$this->style();
	}


	function parseFiles ()
	{
		// read data from files
		$path = APPPATH . $this->conf['source_dir'] . '/' ;
		$sourceDir = dir( $path );
		while (false !== ($entry = $sourceDir->read()))
		{
			if (!is_dir($path . $entry) && stristr($entry, '.xml') !== false && !in_array($entry, array('.', '..', 'index.html')) )
				$this->source[ str_ireplace('.xml', '', $entry) ] = new SimpleXMLElement( file_get_contents( $path . $entry ) );
		}
		$sourceDir->close();
		return $this->source;
	}

	function parseTabs ()
	{

		// make tabs and blocks
		foreach ( $this->source as $k => $v )
		{
			if ( !empty($v->tab) )
			{
				//$this->tabs[ (string) $v->tab ]['count']++;
				if ( !empty($v->block) )
				{
					$this->tabs[ (string) $v->tab ][ (string) $v->block ][$k] = (string) $v->title;
				}
				else
				{
					$this->tabs[ (string) $v->tab ][ $this->CI->lang->line('mainBlock') ][$k] = (string) $v->title;
				}
			}
			else
			{
				if ( !empty($v->block) )
				{
					$this->tabs[ $this->CI->lang->line('mainTab') ][ (string) $v->block ][$k] = (string) $v->title;
				}
				else
				{
					$this->tabs[ $this->CI->lang->line('mainTab') ][ $this->CI->lang->line('mainBlock') ][$k] = (string) $v->title;
				}
			}
		}
		ksort($this->tabs);
		return $this->tabs;
	}

	function view ( $type, $tpl_vars = array(), $data = null )
	{
		// if we have special template for this page
		if ( $data != null && file_exists( APPPATH . 'views/' . $this->style . $data . '_' . $type . '.php' ) )
		{
			$this->CI->load->view($this->style . $data . '_' . $type, $tpl_vars);
		}
		else
		{
			$this->CI->load->view($this->style . $type, $tpl_vars);
		}
	}

	function style()
	{

		$stylesDir = dir( APPPATH.'modules/admin/views/backend' );
		while (false !== ($entry = $stylesDir->read())) {
			if (is_dir( APPPATH.'modules/admin/views/backend' ) && $entry != '.' && $entry != '..' && $entry != '.svn')
				$this->tpl_vars['styles'][] = $entry;
		}
		$stylesDir->close();
		$this->style = 'backend/' . $this->conf['default_style'] . '/';
	}

	function is_search()
	{
		//TODO: check is this field available to serche in it
		//TODO: display search value in search form
		//TODO: show search form when display search results

		$__GET = $this->CI->uri->uri_to_assoc(4);
		if ( isset($__GET['s']) && !empty($__GET['op']) && !empty($__GET['in']) && !empty( $this->op[ $__GET['op'] ]  ) )
		{
			return $__GET;
		}
		else
		{
			return false;
		}
	}

	function have_access( $action, $do_what, $redirect = true )
	{
		if ( (!empty( $this->rules[ $action ][$do_what] ) && $this->rules[ $action ][$do_what] == 1)
			|| (!empty( $this->rules[ '__ALL__' ][$do_what] ) && $this->rules[ '__ALL__' ][$do_what] == 1)
			)
		{
			return true;
		}
		elseif ( $redirect == true )
		{
			$this->CI->session->set_flashdata('message', lang('access_denied') );
			redirect('admin');
		}
		else
		{
			return false;
		}
	}

	function block_visable( $block, $links )
	{
		if ( $block == 'backend-menu' )
		{
			return false;
		}


		if ( !empty( $this->rules['__ALL__'] ) )
		{
			return true;
		}


		$flag = false;
		foreach ( $this->rules as $key => $val )
		{
			if ( !empty( $links[ $key ] ) )
			{
				$flag = true;
			}
		}
		return $flag;
	}

	function link_visable( $link )
	{
		if ( !empty( $this->rules[ $link ] ) || !empty( $this->rules['__ALL__'] ) )
		{
			return true;
		}
	}

	function tab_visable( $tab )
	{

		if (lang('mainTab') == $tab)
		{
			return false;
		}

		return true;
	}

	function select_all_num_rows ( $data )
	{
		if ( count($data) > $this->num_rows )
		{
			$this->num_rows = count($data);
		}

		return $this->num_rows . lang('num_rows');
	}

	function no404 ()
	{
		die("We Haven't this Page !!");
	}

	function ready( $tpl_vars=array() )
	{

		if ( $this->CI->uri->segment(2) != 'login' && $this->CI->session->userdata('backend_modrator') == false )
		{
			$this->CI->session->set_flashdata('message', ( $this->CI->uri->segment(2) ? lang('access_denied') : '' ) );
			redirect('admin/login');
		}
		else if ( $this->CI->uri->segment(2) != 'login' )
		{
			$this->rules = $this->CI->session->userdata('backend_rules');
		}


		$tpl_vars['source'] = $this->source;
		$tpl_vars['tabs'] = $this->tabs;

		// defaulte tab
		$tpl_vars['currentTab'] = lang('mainTab');
		return $tpl_vars;
	}

}