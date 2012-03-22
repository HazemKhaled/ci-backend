<?php
class backend_main_helper
{
	public $CI;
	public $result;
	public $input;
	public $action;
	public $me;

	function __construct( $field, $action, $input=array() )
	{
		$this->CI =& get_instance();
		$this->action = $action;
		$this->me = $this->CI->backend->source[ $action ];

		if ( empty( $input[ $field ] ) && @$input[ $field ] != '0' && empty( $this->me->fields->$field->default ) )
		{
			$this->input = array_merge( array( $field => '' ), $input );
		}
		else if ( !empty( $this->me->fields->$field->default ) )
		{
			$this->input = array_merge( array( $field => (string) $this->me->fields->$field->default ), $input );
		}
		else
		{
			$this->input = $input;
		}
		$this->field = $field;
	}

}

class backend_view extends backend_main_helper
{
	function __construct( $field, $action, $input=array() )
	{
		parent::__construct( $field, $action, $input );

		$view = (string) $this->me->fields->$field->view;
		if ( empty($view) )
		{
			$this->result = $this->input[ $field ];
		}
		else
		{
			$this->result = method_exists($this, $view) ? $this->$view() : $this->input[ $field ] ;
		}

		if ( property_exists( $this->me->fields->$field, 'search' ) )
		{
			$this->result = $this->search();
		}
	}

	function select()
	{
		$field = $this->field;

		if ( !empty( $this->me->fields->$field->values->attributes()->table ) )
		{
			return $result = $this->CI->backend_model->view_select( $this->me->fields->$field->values, $this->input[ $this->field ], $this->me->fields->$field->default );
		}

		foreach ( $this->me->fields->$field->values->attributes() as $k => $d )
		{
			if ( str_replace('hknumber', '', $k) == $this->input[ $field ] )
			{
				return $d;
			}
		}

		return $this->input[ $field ];
	}

	function search()
	{
		$field = $this->field;

		$text = empty( $this->result ) ? $this->me->fields->$field->default : $this->result ;

		$link = 'admin/action/' . $this->me->fields->$field->search->attributes()->action .
					'/s/'	. $this->input[ (string)$this->me->fields->$field->search->attributes()->search ] .
					'/op/'	. array_search( $this->me->fields->$field->search->attributes()->operator, $this->CI->backend->op ) .
					'/in/'	. $this->me->fields->$field->search->attributes()->in ;

		return anchor( $link , $text);
	}

	function mailto()
	{
		return mailto( $this->input[ $this->field ] );
	}

	function timestamp()
	{
		return date('g:i a j-n-Y', strtotime( $this->input[ $this->field ] ) );
	}

	function image()
	{
		$field = $this->field;

		$img = '<img src="' . base_url().'/'.$this->input[ $field ] . '"';

		if ( $this->me->fields->$field->view->attributes()->width )
		$img.= ' width="' . $this->me->fields->$field->view->attributes()->width . '"';

		$img.= '/>';

		return '<a href="' . base_url().'/'. $this->input[ $field ] . '" target="_blank">' . $img . '</a>';
	}

	function nl2br()
	{
		return nl2br($this->input[ $this->field ]);
	}

	function backend_methods()
	{
		if ( $this->input[ $this->field ] == '__ALL__' )
			return lang('all_methods');
		return (string) $this->CI->backend->source[ $this->input[ $this->field ] ]->title;
	}

	function backend_modrators()
	{
		$db = $this->CI->backend->source['backend-modrator-settings'];

		$this->CI->db->select( $db->username);
		$this->CI->db->where( (string) $db->id, $this->input[ $this->field ] );
		$query = $this->CI->db->get( $db->table );
		$admin = $query->row_array();
		return $admin[ (string) $db->username ] ;
	}

}

class backend_input extends backend_main_helper
{
	function __construct( $field, $action, $input=array() )
	{
		parent::__construct( $field, $action, $input );


		$input = (string) $this->me->fields->$field->input;
		if ( empty($input) )
		{
			$this->result = $this->input[ $field ];
		}
		else
		{
			$this->CI->load->helper( 'form' );
			$this->result = method_exists($this, $input) ? $this->$input() : $this->text() ;
		}
	}

	function select()
	{
		return form_dropdown($this->field, $this->array_results(), $this->input[ $this->field ], 'id="' . $this->field . '"');
	}

	function select_pop()
	{
		return $this->select();
	}

	function radio()
	{
		$html = '<div class="radio_holder">';
		$results = $this->array_results();

		foreach ( $results as $k => $v )
		{
			$html .= form_radio($this->field, $k, $this->input[ $this->field ] == $k ? true : false , 'id="' . $k . '" class="inline"').
			form_label($v, $k, array('class' => 'inline') );
		}


		return $html.'</div>';
	}

	function text()
	{
		$field = $this->field;
		$output = form_input( $field, $this->input[ $field ], 'id="' . $field . '"' );

		if ( $this->me->fields->$field->input->attributes()->date != '' )
		{
			if ( $this->CI->config->item('jQueryUITheme_loaded') != true )
			{
				$output.= '<link rel="stylesheet" type="text/css" href="' . $this->CI->config->item('base_url') . 'assets/js/ui-theme/jquery-ui.css" />';
				$this->CI->config->set_item('jQueryUITheme_loaded', true);
			}
			if ( $this->CI->config->item('datepicker_loaded') != true )
			{
				$output.= '<script type="text/javascript" src="' . $this->CI->config->item('base_url') . 'assets/js/jquery-ui-1.8.1.custom.min.js"></script>';
				$this->CI->config->set_item('datepicker_loaded', true);

				if ( $this->CI->config->item('language') == 'arabic' )
				{
					$output.= '<script type="text/javascript" src="' . $this->CI->config->item('base_url') . 'assets/js/jquery.ui.datepicker-ar.js"></script>';
				}
			}

			$format = (string)$this->me->fields->$field->input->attributes()->date;
			$format = empty( $format ) ? 'yy-mm-dd' : $format ;

			$other = '';
			if ( $this->me->fields->$field->input->attributes()->minDate )
			{
				$other.= "minDate: '" . $this->me->fields->$field->input->attributes()->minDate . "',\n";
			}
			if ( $this->me->fields->$field->input->attributes()->maxDate )
			{
				$other.= "maxDate: '" . $this->me->fields->$field->input->attributes()->maxDate . "',\n";
			}

			$lang = $this->CI->config->item('language') == 'arabic' ? ", $.datepicker.regional['ar']" : '' ;

			$output.= <<<EOF
				<script type="text/javascript">
					$(function() {
						$('#{$field}').datepicker({
							{$other}
							dateFormat: '{$format}',
							showAnim: 'fadeIn',
							changeMonth: true,
							changeYear: true
						});
					}{$lang});
				</script>
EOF;
		}

		if ( $this->me->fields->$field->input->attributes()->upload != '' )
		{
			$output.= anchor_popup('admin/upload/' . $this->action . '/' . $field, lang('upload'), array('width' => 300,'height' => 200, 'class' => 'upload_link'));
		}

		return $output;
	}
    
    function modratorid() {
        
        return form_hidden($this->field, $this->CI->session->userdata('backend_modrator'));
    }

	function textarea()
	{
		$field = $this->field;
		$output = form_textarea( $field, $this->input[ $field ], 'id="' . $field . '"' );

		if ( $this->me->fields->$field->input->attributes()->wysiwyg != 1 )
		{
			return $output;
		}

		if ( $this->CI->config->item('tinymce_loaded') != true )
		{
			$output.= '<script type="text/javascript" src="' . $this->CI->config->item('base_url') . 'assets/js/tinymce/jquery.tinymce.js"></script>';
			$this->CI->config->set_item('tinymce_loaded', true);
		}

		$output.= <<<EOF
				<script type="text/javascript">
					$(function() {
						$('#{$field}').tinymce({
EOF;
		$output.= 'theme_advanced_toolbar_align : "' . ($this->CI->config->item('language') == 'arabic' ? 'right' : 'left') . '",
					language : "' . ($this->CI->config->item('language') == 'arabic' ? 'ar' : 'en') . '",
					theme : "' . ( $this->me->fields->$field->input->attributes()->simple != 1 ? 'advanced' : 'simple' ) . '",';
		$output.= <<<EOF
							// Location of TinyMCE script
							script_url : '{$this->CI->config->item('base_url')}assets/js/tinymce/tiny_mce.js',

							// Theme options
							plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
							theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
							theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
							theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
							theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
							theme_advanced_statusbar_location : "bottom",
							theme_advanced_toolbar_location : "top",
							theme_advanced_resizing : true
						});
					});
				</script>
EOF;
		return $output;
	}

	function backend_methods()
	{
		$output = '<select name="' . $this->field . '" id="' . $this->field . '">';

		$output .= '<option value="__ALL__">' . lang('all_methods') . '</option>';

		foreach ( $this->CI->backend->tabs as $k => $v)
		{
			$output .= '<optgroup label="' . $k . '" class="tab"></optgroup>';
			foreach ( $v as $k1 => $v1)
			{
				$output .= '<optgroup label="' . $k1 . '" class="block">';
				foreach ( $v1 as $k2 => $v2)
				{
					$output .= '<option value="' . $k2 . '">' . $v2 . '</option>';
				}
				$output .= '</optgroup>';
			}
		}
		$output .= '</select>';
		return $output;
	}

	function backend_modrators()
	{
		$db = $this->CI->backend->source['backend-modrator-settings'];

		$this->CI->db->select( $db->id.', '.$db->username);
		$query = $this->CI->db->get( $db->table );
		$admins_ = $query->result_array();
		$admins = array();

		foreach ($admins_ as $v)
		{
			$admins[ $v[ (string) $db->id ] ] = $v[ (string) $db->username ];
		}

		return form_dropdown($this->field, $admins, $this->input[ $this->field ], 'id="' . $this->field . '"');
	}

	function timestamp()
	{
		return '<span class="input">' . date("g:i a j-n-Y") . '</span>' . form_hidden( $this->field, empty($this->input[ $this->field ]) ? time() : $this->input[ $this->field ] , 'id="' . $this->field . '"' );
	}

	function password()
	{
		//$output = form_hidden( $this->field . '_old', $this->input[ $this->field ], 'id="' . $this->field . '_old' . '"' );
		return form_password( $this->field, '', 'id="' . $this->field . '"' );
	}

	private function array_results ()
	{

		$field = $this->field;

		if ( !empty( $this->me->fields->$field->values->attributes()->table ) )
		{
			$result = $this->CI->backend_model->input_select( $this->me->fields->$field->values );

			foreach ( $result as $r )
			{
				$results[ $r[ (string) $this->me->fields->$field->values->attributes()->value ] ] = $r[ (string) $this->me->fields->$field->values->attributes()->output ];
			}
		}
		else
		{
			foreach ( $this->me->fields->$field->values->attributes() as $k => $d )
			{
				$results[ str_replace('hknumber', '', $k) ] = (string) $d;
			}
		}

		if ( !empty( $this->me->fields->$field->default ) && !in_array( (string)$this->me->fields->$field->default, array_keys($results) ) ) // add default value in dropdown menu
		{
			$results_ = $results;
			$results = array( '' => (string) $this->me->fields->$field->default );

			foreach ($results_ as $k => $v)
			{
				$results[$k] = $v;
			}
		}

		return $results;
	}
}