<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Db_update extends CI_Model
{

    public function __construct() {
        parent::__construct();
        $this->load->dbforge();
    }

    public function update() {
        if ($this->Settings->version == '4.0.12') {
            $this->update4013();
            redirect();
        }
    }

    public function update4013() {
        $settings = array(
            'rtl' => array('type' => 'TINYINT', 'constraint' => '1', 'default' => 0, 'null' => TRUE),
            'print_img' => array('type' => 'TINYINT', 'constraint' => '1', 'default' => 0, 'null' => TRUE),
        );
        $this->dbforge->add_column('settings', $settings);
        return $this->db->update('settings', ['version' => '4.0.13']);
    }

}
