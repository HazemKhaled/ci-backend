<?php
class Backend_model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function select_all( $action, $not_active_only = false )
	{
		$me = $this->backend->source[$action];
		$query = $this->_select_all($action, $not_active_only);
		if ( $me->limit && $query->num_rows > intval($me->limit) )
		{

			$this->backend->num_rows = $query->num_rows;
			$this->load->library('pagination');
			// support pages in search
			$config['base_url']		= site_url('/admin/action/' . $action . '/page');
			$config['total_rows']	= $query->num_rows;
			$config['per_page']		= (int)$me->limit;
			$config['uri_segment']	= 5;
			$config['next_link']	= '&lt;';
			$config['prev_link']	= '&gt;';
			$config['first_link']	= lang( 'first_link' );
			$config['last_link']	= lang( 'last_link' );

			$this->pagination->initialize($config);

			$this->db->limit( $config['per_page'], $this->uri->segment(5) );
			$query = $this->_select_all($action, $not_active_only);
		}


		return $query->result_array();
	}

	private function _select_all( $action, $not_active_only = false )
	{
		$me = $this->backend->source[$action];

		$select = array( );
		foreach ( $me->fields->children() as $k => $f)
		{
			if ( $f->option->attributes()->list == 1 && $f->type != '0')
			{
				$select[] = $k;
			}
		}

		//add Primary key
		if ( !in_array((string) $me->pk, $select) )
			$select[] = (string) $me->pk;

		//add main field
		if ( !empty($me->main) && !in_array((string) $me->main, $select) )
			$select[] = (string) $me->main;

		$this->db->select( implode(',', $select) );

		if ( !empty($me->table) )
			$this->db->from( (string) $me->table );
		else
			$this->db->from( $action );

		if ( !empty( $me->where ) )
			$this->db->where( (string) $me->where );

		if ( $not_active_only == true )
			$this->db->where( (string) $me->active . ' !=', "1");

		if ( !empty( $me->order ) )
			$this->db->order_by( $me->order );


		if ( $s = $this->backend->is_search() )
		{
			if ( $s['op'] == 'like_text' )
			{
				$this->db->where($s['in'] . ' like', '%' . $s['s'] . '%');
			}
			else
			{
				$this->db->where($s['in'] . ' ' . $this->backend->op[ $s['op'] ], $s['s']);
			}
		}


		return $this->db->get();
	}

	function select_one( $action, $pk, $type="view" )
	{
		$me = $this->backend->source[$action];

		$select = array( );
		foreach ( $me->fields->children() as $k => $f)
		{
			if ( $f->option->attributes()->$type == 1 && $f->type != 'linkOnly' )
			{
				$select[] = $k;
			}
		}

		//add Primary key
		if ( !in_array((string) $me->pk, $select) )
			$select[] = (string) $me->pk;

		//add main field
		if ( !empty($me->main) && !in_array((string) $me->main, $select) )
			$select[] = (string) $me->main;

		$this->db->select( implode(',', $select) );

		if ( !empty($me->table) )
			$this->db->from( (string) $me->table );
		else
			$this->db->from( $action );

		if ( !empty( $me->where ) )
			$this->db->where( (string) $me->where );

		if ( !empty( $me->pk ) )
			$this->db->where( (string) $me->pk, $pk);

		$query = $this->db->get();
		return $query->row_array();
	}

	function add ( $action )
	{
		$me = $this->backend->source[$action];

		foreach ( $me->fields->children() as $k => $f)
		{
			if ( $f->option->attributes()->add == 1 && $f->input == 'password' && @$f->input->attributes()->md5 == '1' && $this->input->post( $k )/* && $this->input->post( $k . '_old' )*/ )
			{
				$this->db->set( $k, md5( $this->input->post( $k ) ) );
			}
			elseif ( $f->option->attributes()->add == 1 && $f->type != 'linkOnly' )
			{
				$this->db->set( $k,  $this->input->post( $k ) );
			}
		}

		if ( !empty($me->table) )
			$this->db->insert( (string) $me->table );
		else
			$this->db->insert( $action );

		return true;
	}

	function edit ( $action, $pk )
	{
		$me = $this->backend->source[$action];

		foreach ( $me->fields->children() as $k => $f)
		{
			if ( $f->option->attributes()->edit == 1 && $f->input == 'password' && @$f->input->attributes()->md5 == '1' && $this->input->post( $k )/* && $this->input->post( $k . '_old' )*/ )
			{
				$this->db->set( $k, md5( $this->input->post( $k ) ) );
			}
			else if ( $f->option->attributes()->edit == 1 && $f->type != 'linkOnly' )
			{
				$this->db->set( $k,  $this->input->post( $k ) );
			}
		}

		if ( !empty( $me->pk ) )
			$this->db->where( (string) $me->pk, $pk);

		if ( !empty($me->table) )
			$this->db->update( (string) $me->table );
		else
			$this->db->update( $action );


		return true;
	}

	function delete ( $action, $pk )
	{
		$me = $this->backend->source[$action];

		$this->db->where_in( (string) $me->pk, $pk);
		if ( !empty($me->table) )
			$this->db->delete( (string) $me->table );
		else
			$this->db->delete( $action );

		return true;
	}

	function active ( $action, $pk )
	{
		$me = $this->backend->source[$action];

		$this->db->set( (string) $me->active,  '1' );
		$this->db->where_in( (string) $me->pk, $pk);
		if ( !empty($me->table) )
			$this->db->update( (string) $me->table );
		else
			$this->db->update( $action );

		return true;
	}

	function count_need_to_active( $action )
	{
		$me = $this->backend->source[$action];

		$this->db->where( (string) $me->active . ' !=', "1");
		if ( !empty($me->table) )
			return $this->db->count_all_results( (string) $me->table );
		else
			return $this->db->count_all_results( $action );
	}

	function view_select( $values, $data, $default )
	{
		$select = array( );

		if ( !empty( $values->attributes()->value ) && false )
			$select[] = (string) $values->attributes()->value;

		if ( !empty( $values->attributes()->output ) )
			$select = (string) $values->attributes()->output;

		$this->db->select( $select );
		$this->db->from( (string) $values->attributes()->table );

		if ( !empty( $values->attributes()->where ) )
			$this->db->where( (string) $values->attributes()->where );

		if ( isset( $data ) )
			$this->db->where( (string) $values->attributes()->value, $data);

		$query = $this->db->get();
		$result = $query->row_array();
		return empty( $result[$select] ) ? $default : $result[$select] ;
	}

	function input_select( $values )
	{
		$select = array( );

		if ( !empty( $values->attributes()->value ) )
			$select[] = (string) $values->attributes()->value;

		if ( !empty( $values->attributes()->output ) )
			$select[] = (string) $values->attributes()->output;

		$this->db->select( implode(',', $select) );
		$this->db->from( (string) $values->attributes()->table );

		if ( !empty( $values->attributes()->where ) )
			$this->db->where( (string) $values->attributes()->where );

		if ( !empty( $values->attributes()->orderby ) )
			$this->db->order_by( (string) $values->attributes()->orderby );

		$query = $this->db->get();
		return $query->result_array();
	}
}