<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function updateSetting($data = array()) {

        if($this->db->update('settings', $data, array('setting_id' => 1))) {
            return true;
        }
        return false;
    }

    public function getStoreByID($id) {
        $q = $this->db->get_where('stores', array('id' => $id), 1);
        if( $q->num_rows() > 0 ) {
            return $q->row();
        }
        return FALSE;
    }

    public function addStore($data = array()) {
        if($this->db->insert('stores', $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function updateStore($id, $data = array()) {
        if($this->db->update('stores', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deleteStore($id) {
        if($this->db->delete('stores', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function addPrinter($data = array()) {
        if($this->db->insert('printers', $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function updatePrinter($id, $data = array()) {
        if($this->db->update('printers', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deletePrinter($id) {
        if($this->db->delete('printers', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

}
