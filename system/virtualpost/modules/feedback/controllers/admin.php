<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @copyright Copyright (c) 2012-2013 
 * @author Bui Duc Tien <tienbd@gmail.com>
 * @website http://www.flightpedia.org
 * @package Addons\Shared_addons\Modules\Feedback\Controllers\Admin
 * @created 2/19/2013
 */

class Admin extends Admin_Controller
{
	/**
	 * The current active section
	 *
	 * @var string
	 */
	protected $section = 'feedback';
	
	/**
		* Array that contains the validation rules
		*
		* @var array
		*/
		protected $validation_rules = array(
			'name' => array(
				'field' => 'Name',
				'label' => 'lang:feedback.Name_label',
				'rules' => 'trim|required|max_length[127]'
			),
			'subject' => array(
				'field' => 'Subject',
				'label' => 'lang:feedback.Subject_label',
				'rules' => 'trim|required|max_length[127]'
			),			
			'status' => array(
				'field' => 'Status',
				'label' => 'lang:feedback.Status_label',
				'rules' => 'trim|max_length[1]'
			),
			'message' => array(
				'field' => 'Message',
				'label' => 'lang:feedback.Message_label',
				'rules' => 'trim'
			),
		);
	
	/**
	 * The constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
	
		$this->load->model(array('feedback_m'));
		$this->lang->load(array('feedback'));
		
		$this->load->library(array( 'form_validation'));

		// Date ranges for select boxes
		$this->template->set('orders', array_combine($orders = range(0, 15), $orders));

	}
	
	/**
	 * Show all feedbacks
	 * @access public
	 * @return void
	 */
	public function index()
	{
		// Get input condition
        $keyword = $this->input->get_post("keyword");
        $active = $this->input->get_post("status");
		
        $array_condition = array ();
        if (! empty($keyword)) {
            $array_condition ['Feedback.Subject LIKE '] = '%' . $keyword . '%';
        }
        if (! empty($active)) {
            $array_condition ['Feedback.Status ='] = $active;
        }

        
        // If current request is ajax
        if ($this->is_ajax_request()) {
            // Get paging input
            $input_paging = $this->post_paging_input();
            // Call search method
            $query_result = $this->feedback_m->get_feedback_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);
            
            // Process output data
            $total = $query_result ['total'];
            $datas = $query_result ['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
            
            $i = 0;
            foreach ( $datas as $row ) {
                $response->rows [$i] ['id'] = $row->FeedbackID;
                $response->rows [$i] ['cell'] = array (
                        $row->FeedbackID,
                        $row->Status,
                        $row->Name,
                        $row->Subject,
                        $row->Message,
                        $row->FeedbackID
                );
                $i ++;
            }
            
            echo json_encode($response);
        } else {
            
            $this->template->build('admin/feedback/index');
        }
	}
	
	/**
	 * Add new feedback
	 * @access public
	 * @return void
	 */
	public function add(){
		// The user needs to be able to add feedback.
		//role_or_die('feedback', 'edit');
		
		$this->form_validation->set_rules($this->validation_rules);
		
		if ($this->form_validation->run())
		{
			$id = $this->feedback_m->insert(array(
				'Name'		    => $this->input->post('Name'),
				'Subject'		=> $this->input->post('Subject'),
				'Status'		=> $this->input->post('Status'),
				'Message'		=> $this->input->post('Message'),
				'CreatedOn'		=> now(),
				'UpdatedOn'		=> now(),
				'AuthorID'		=> $this->current_user->ID,
			));
			if ($id){
				$this->session->set_flashdata('success', sprintf($this->lang->line('feedback.add_success'), $this->input->post('name')));
			}
			else
			{
				$this->session->set_flashdata('error', $this->lang->line('feedback.add_error'));
			}

			// Redirect back to the form or main page
			$this->input->post('btnAction') == 'save_exit' ? redirect('admin/feedback') : redirect('admin/feedback/edit/' . $id);
		}
		else
		{
			$feedback = new stdClass();
			// Go through all the known fields and get the post values
			foreach ($this->validation_rules as $key => $field)
			{
				$feedback->$field['field'] = set_value($field['field']);
			}
			$feedback->CreatedOn = now();
		}			
		
		$this->template
			->title(sprintf(lang('feedback.add_title')))
			->set('active_section', 'feedback')
			->append_metadata($this->load->view('fragments/wysiwyg', array(), TRUE))
			->set('feedback',$feedback)
			->build('admin/feedback/form');
	}
	
	/**
	 * Edit feedback with $id
	 * @access public
	 * @param int $id the ID of the feedback to edit
	 * @return void
	 */
	public function edit($id = 0) {
		// We are lost without an id. Redirect to the pages index.
		$id OR redirect('admin/feedback');

		// The user needs to be able to edit pages.
		//role_or_die('feedback', 'edit');

		// Retrieve the page data along with its chunk data as an array.
		$feedback = $this->feedback_m->get($id);

		// Got page?
		if ( ! $feedback OR empty($feedback))
		{
			// Maybe you would like to create one?
			$this->session->set_flashdata('error', lang('feedback.not_found_error'));
			redirect('admin/feedback');
		}
		
		// Validate the results
		$this->form_validation->set_rules($this->validation_rules);
		if ($this->form_validation->run())
		{
			unset($feedback->FeedbackID);
			$feedback->Name			= $this->input->post('Name');
			$feedback->Subject		= $this->input->post('Subject');
			$feedback->Message		= $this->input->post('Message');
			$feedback->AuthorID		= $this->current_user->ID;
			$feedback->UpdatedOn	= now();

			// Update the comment
			$this->feedback_m->update($id, $feedback)
				? $this->session->set_flashdata('success', lang('feedback.edit_success'))
				: $this->session->set_flashdata('error', lang('feedback.edit_error'));

			redirect('admin/feedback');
		}

		// Loop through each rule
		foreach ($this->validation_rules as $rule)
		{
			if ($this->input->post($rule['field']) !== FALSE)
			{
				$feedback->{$rule['field']} = $this->input->post($rule['field']);
			}
		}
		$this->template
			->title(sprintf(lang('feedback.edit_title'), $feedback->FeedbackID))
			->append_metadata($this->load->view('fragments/wysiwyg', array(), TRUE))
			->set('feedback', $feedback)
			->build('admin/feedback/form');
	}
	/**
	 * Helper method to determine what to do with selected items from form post
	 * @access public
	 * @return void
	 */
	public function action()
	{
		switch ($this->input->post('btnAction'))
		{
			case 'publish':
				$this->publish();
			break;
			
			case 'delete':
				$this->delete();
			break;
			
			default:
				redirect('admin/feedback');
			break;
		}
	}
	
	/**
	 * Publish feedback
	 * @access public
	 * @param int $id the ID of the feedback to make public
	 * @return void
	 */
	public function publish($id = 0)
	{
		// We are lost without an id. Redirect to the pages index.
		$id OR redirect('admin/feedback');
		
		role_or_die('feedback', 'publish');
		
		$feedback = $this->feedback_m->get($id);
		
		if ( ! $feedback OR empty($feedback))
		{
			// Maybe you would like to create one?
			$this->session->set_flashdata('error', lang('feedback.not_found_error'));
			redirect('admin/feedback');
		}
		$this->feedback_m->publish($id);

		// Wipe cache for this model, the content has changed
		$this->pyrocache->delete('feedback_m');
		// Some posts have been published
		$this->session->set_flashdata('success', sprintf($this->lang->line('feedback.publish_success'), $feedback->name));

		redirect('admin/feedback');
	}
	/**
	 * For user have role delete feedback
	 * @param int $id the ID of the feedback to delete
	 * @return bool
	 */
	public function delete($id=0)
	{
		$id OR redirect('admin/feedback');
		
		//role_or_die('feedback', 'delete');
		
		if( $this->feedback_m->delete($id) )
		{
		    $message = sprintf(lang('feedback.delete_success'), $id);
            $this->success_output($message);
            return;
		}
		else {
		    $message = sprintf(lang('feedback.delete_error'), $id);
            $this->success_output($message);
            return;
		}

	}
}

