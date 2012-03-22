<?php
class Admin extends CI_Controller {

	private $tpl_vars = array('activePage' => false);

	function __construct()
	{
		parent::__construct();

		$this->load->library('backend');
		$this->tpl_vars = $this->backend->ready( $this->tpl_vars );
	}

	function index()
	{

		$this->tpl_vars['siteTitle'] = lang('titleHome');
		$this->backend->view('index', $this->tpl_vars);
	}

	function tab ( $tab )
	{
		$tab = str_replace('_', ' ', $tab);
		if ( empty($this->backend->tabs[$tab]) )
			$this->backend->no404 ();

		$this->tpl_vars['currentTab'] = $tab;
		$this->tpl_vars['siteTitle'] = $tab;

		$this->backend->view('tab', $this->tpl_vars, $tab);
	}

	function action ( $action )//list page
	{

		if ( empty($this->backend->source[$action]) )
		{
			$this->backend->no404 ();
		}

		if ( empty($this->backend->rules[ $action ]) && empty($this->backend->rules[ '__ALL__' ]) )
		{
			$this->session->set_flashdata('message', lang('access_denied') );
			redirect('admin');
		}

		$this->tpl_vars['action']	= $action;
		$this->tpl_vars['me']		= $me = $this->backend->source[$action];

		// set current tab
		$this->tpl_vars['currentTab'] = empty($me->tab) ? $this->tpl_vars['currentTab'] : (string) $me->tab ;

		$doWhat = $this->input->post( 'delete' ) ? 'delete' : ($this->input->post( 'active' ) ? 'active' : false) ;

		if ( /*$this->input->server('REQUEST_METHOD') == 'POST'
				&& */$this->input->post( (string) $me->pk ) !== false // check any checkbox ?
				&& $this->backend->have_access( $action, $doWhat )
				&& $this->backend_model->$doWhat( $action, $this->input->post( (string) $me->pk ) )
			)
		{
			$this->session->set_flashdata('message', lang($doWhat . '_success') );
			redirect('admin/' . ($this->tpl_vars['activePage'] ? 'active/' : 'action/' ) . $action);
		}

		// get data
		$this->tpl_vars['data'] = $this->backend_model->select_all( $action, $this->tpl_vars['activePage'] );
		$this->tpl_vars['pager']= isset($this->pagination) ? $this->pagination->create_links() : false ;

		// get record needed to active
		$this->tpl_vars['active_count'] = $me->option->attributes()->active == 0 ? false : $this->backend_model->count_need_to_active( $action ) ;


		$this->tpl_vars['siteTitle'] =  !empty($this->tpl_vars['siteTitle']) ? $this->tpl_vars['siteTitle'] : $this->tpl_vars['currentTab'] .' : ' . $me->title;
		$this->backend->view('action', $this->tpl_vars, $action);
	}

	function active ( $action )
	{

		if ( $this->backend->source[$action]->option->attributes()->active != 1 )
		{
			redirect('admin/action/' . $action);
		}

		$this->backend->have_access( $action, 'active' );

		$this->tpl_vars['activePage'] = true;
		$this->tpl_vars['siteTitle'] = $this->tpl_vars['currentTab'] .' : ' . lang('active_record'). ' ' . $this->backend->source[$action]->title;

		$this->action( $action );
	}

	function add ( $action )
	{

		if ( empty($this->backend->source[$action]) )
		{
			$this->backend->no404 ();
		}

		$this->backend->have_access( $action, 'add' );

		$this->tpl_vars['action'] = $action;
		$this->tpl_vars['me'] = $me = $this->backend->source[$action];

		if ( $me->option->attributes()->add != 1 )
		{
			redirect('admin/action/' . $action);
		}

		// get the current tab
		$this->tpl_vars['currentTab'] = empty($me->tab) ? $this->tpl_vars['currentTab'] : (string) $me->tab ;

		if ( $this->input->server('REQUEST_METHOD') == 'POST' && $this->backend_model->add( $action ) )
		{
			$this->session->set_flashdata('message', lang('add_success') );
			redirect('admin/action/' . $action);
		}

		// get record needed to active
		$this->tpl_vars['active_count'] = $me->option->attributes()->active == 0 ? false : $this->backend_model->count_need_to_active( $action ) ;

		$this->tpl_vars['siteTitle'] = $this->tpl_vars['currentTab'] .' : ' . lang('add_record'). ' ' . $me->title;
		$this->backend->view( 'add', $this->tpl_vars, $action );
	}

	function edit ( $action, $pk )
	{
		if ( empty($this->backend->source[$action]) )
		{
			$this->backend->no404 ();
		}

		$this->backend->have_access( $action, 'edit' );

		$this->tpl_vars['action'] = $action;
		$this->tpl_vars['me'] = $me = $this->backend->source[$action];

		if ( $me->option->attributes()->edit != 1 )
		{
			redirect('admin/action/' . $action);
		}

		// get the current tab
		$this->tpl_vars['currentTab'] = empty($me->tab) ? $this->tpl_vars['currentTab'] : (string) $me->tab ;

		if ( $this->input->server('REQUEST_METHOD') == 'POST' && $this->backend_model->edit( $action, $pk ) )
		{
			$this->session->set_flashdata('message', lang('edit_success') );
			redirect('admin/action/' . $action);
		}

		// get data
		$this->tpl_vars['data'] = $this->backend_model->select_one( $action, $pk, 'edit' );

		// get record needed to active
		$this->tpl_vars['active_count'] = $me->option->attributes()->active == 0 ? false : $this->backend_model->count_need_to_active( $action ) ;

		$this->tpl_vars['siteTitle'] = $this->tpl_vars['currentTab'] .' : ' . lang('edit_record'). ' (' . $this->tpl_vars['data'][(string)$me->main] . ') ' . lang('in') . ' ' . $me->title;
		$this->backend->view( 'edit', $this->tpl_vars, $action );
	}

	function view ( $action, $pk )
	{

		if ( empty($this->backend->source[$action]) )
		{
			$this->backend->no404 ();
		}

		$this->tpl_vars['action'] = $action;
		$this->tpl_vars['me'] = $me = $this->backend->source[$action];

		if ( $me->option->attributes()->view != 1 )
		{
			redirect('admin/action/' . $action);
		}

		// get the current tab
		$this->tpl_vars['currentTab'] = empty($me->tab) ? $this->tpl_vars['currentTab'] : (string) $me->tab ;

		// get data
		$this->tpl_vars['data'] = $this->backend_model->select_one( $action, $pk );

		// get record needed to active
		$this->tpl_vars['active_count'] = $me->option->attributes()->active == 0 ? false : $this->backend_model->count_need_to_active( $action ) ;


		$this->tpl_vars['siteTitle'] = $this->tpl_vars['currentTab'] .' : ' . lang('view_record'). ' (' . $this->tpl_vars['data'][(string)$me->main] . ') ' . lang('in') . ' ' . $me->title;
		$this->backend->view( 'view', $this->tpl_vars, $action );
	}

	function login ()
	{
		$db = $this->backend->source['backend-modrator-settings'];
		if ( $this->input->post('username') && $this->input->post('password') )
		{
			$this->db->select( $db->id );
			$this->db->where( (string) $db->username, $this->input->post('username') );
			$this->db->where( (string) $db->password, md5( $this->input->post('password') ) );
			$query = $this->db->get( $db->table );
			if ( $query->num_rows > 0 )
			{
				$mod = $query->row_array();
				$user_id = $mod[ (string) $db->id ];

				$this->db->select("method , add , edit , delete , active");
				$this->db->where( 'modrate_id', $user_id );
				$query = $this->db->get( 'rules' );

				if ( $query->num_rows > 0 )
				{
					$rules = array();
					$rules_ = $query->result_array();
					foreach ( $rules_ as $v )
					{
						$rules[ $v['method'] ]['add']		= $v['add'];
						$rules[ $v['method'] ]['edit']		= $v['edit'];
						$rules[ $v['method'] ]['delete']	= $v['delete'];
						$rules[ $v['method'] ]['active']	= $v['active'];
					}

					$this->session->set_userdata('backend_modrator', $user_id );
					$this->session->set_userdata('backend_rules', $rules );
					$this->session->set_flashdata('message', lang('login_success') );
					redirect('admin');
				}
				else
				{
					$this->session->set_flashdata('message', lang('login_error_pr') );
					redirect('admin/login');
				}
			}
			else
			{
				$this->session->set_flashdata('message', lang('login_error') );
				redirect('admin/login');
			}

		}

		$this->tpl_vars['siteTitle'] = lang('titleLogin');
		$this->backend->view('login', $this->tpl_vars);
	}

	function logout ()
	{
		$this->session->sess_destroy();
		redirect('admin/login');
	}

	function upload ($action, $field)
	{
		$fobject = $this->backend->source[$action]->fields->$field;

		$config['upload_path']	= empty($fobject->input->attributes()->path) ?			'uploads/'		: $fobject->input->attributes()->path ;
		$config['allowed_types']= empty($fobject->input->attributes()->allowed_types) ?	'gif|jpg|png'	: $fobject->input->attributes()->allowed_types ;
		$config['max_size']		= empty($fobject->input->attributes()->max_size) ?		0				: $fobject->input->attributes()->max_size ;
		$config['encrypt_name'] = true;

		if ( $this->input->server('REQUEST_METHOD') == 'POST' )
		{
			$this->load->library('upload', $config);

			if ( ! $this->upload->do_upload())
			{
			 
				$this->tpl_vars['upload_error'] = $this->upload->display_errors();
			}
			else
			{
				$this->tpl_vars['upload'] = $this->upload->data();
                
                        $this->load->library('image_lib');
                        $image_data = $this->upload->data();
                    $configwatermark = array();
                    $configwatermark["source_image"] = "uploads/".$image_data['file_name'];
                    $configwatermark["image_library"] = "gd2";
                    
                    
                    if ( $fobject->input->attributes()->thumb == 'thumb' ) {
                        $configthumb = array(
                            'source_image' => $image_data['full_path'],
                            'new_image' => 'thumb/',
                            'master_dim' => 'auto',
                            'maintain_ratio' => true,
                            'width' => 200,
                            'height' => 200
                        );
                        $this->load->library('image_lib');
                        $this->image_lib->initialize($configthumb);
                        $this->image_lib->resize();        
                        $this->image_lib->clear();
                        }
                    
                    if ( $fobject->input->attributes()->watermark == 'text' ) {
                      $configwatermark['wm_text'] = $fobject->input->attributes()->watermarktext;
                      $configwatermark['wm_type'] = 'text';
                      $configwatermark['wm_font_path'] = './system/fonts/texb.ttf';
                      $configwatermark['wm_font_size']    = '40';
                      $configwatermark['wm_font_color'] = '000';
                      $configwatermark['wm_shadow_color'] = '000';
                      $configwatermark['wm_shadow_distance'] = '3';
                      $configwatermark['wm_vrt_alignment'] = 'top';
                      $configwatermark['wm_hor_alignment'] = 'left';
                      $configwatermark['wm_padding'] = '20';
                    } else {
                      $configwatermark["wm_type"] = "overlay";
                      $configwatermark["wm_overlay_path"] = "uploads/".$fobject->input->attributes()->watermarkimg;
                      $configwatermark["wm_vrt_alignment"] = "bottom";
                      $configwatermark["wm_hor_alignment"] = "center";
                      $configwatermark["wm_vrt_offset"] = 20;
                    }
                    
                    $this->image_lib->initialize($configwatermark);
                    $this->image_lib->watermark();
                    $this->image_lib->clear();                        
                        
                        
                        
			}
		}

		$this->tpl_vars['config']		= $config;
		$this->tpl_vars['action']		= $action;
		$this->tpl_vars['field']		= $field;
		$this->tpl_vars['siteTitle']	= lang('titleUpload');
		$this->backend->view('upload', $this->tpl_vars);
	}

}