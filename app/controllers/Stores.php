<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stores extends MY_Controller
{

    function __construct() {
        parent::__construct();

        if (! $this->loggedIn) {
            redirect('login');
        }
        if ($register = $this->site->registerData($this->session->userdata('user_id'))) {
            $register_data = array('register_id' => $register->id, 'cash_in_hand' => $register->cash_in_hand, 'register_open_time' => $register->date, 'store_id' => $register->store_id);
            $this->session->set_userdata($register_data);
            $this->session->set_flashdata('error', lang("please_close_registger"));
            redirect('pos');
        }
        if ($this->session->userdata('has_store_id')) {
            $this->session->set_flashdata('error', lang("access_denied"));
            redirect('welcome');
        }
    }

    function index() {
        if (!$this->Settings->multi_store) {
            $this->select_store(1);
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('stores');
        $bc = array(array('link' => '#', 'page' => lang('stores')));
        $meta = array('page_title' => lang('stores'), 'bc' => $bc);
        $this->page_construct('stores', $this->data, $meta);
    }

    function get_stores() {
        $this->load->library('datatables');
        $this->datatables
        ->select("id, name, code, phone, email, address1, city")
        ->from("stores")
        ->add_column("Actions", "<div class='text-center'><a href='" . site_url('stores/select_store/$1') . "' class='tip btn btn-primary btn-xs' title='".$this->lang->line("select_store")."'><i class='fa fa-check-square-o'></i> ".$this->lang->line("select_store")."</a></div>", "id")
        ->unset_column('id');
        echo $this->datatables->generate();
    }

    function select_store($store_id) {
        $this->session->set_flashdata('message', lang("store_selected"));
        $this->session->set_userdata('store_id', $store_id);
        redirect('pos');
    }

    function deselect_store($store_id = NULL) {
        if (!$store_id) {
            $store_id = $this->session->userdata('store_id');
        }
        $this->session->set_flashdata('message', lang("store_deselected"));
        $this->session->set_userdata('store_id', NULL);
        redirect('welcome');
    }

}
