<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends MY_Controller
{

    function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            redirect('login');
        }

        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }

        $this->load->library('form_validation');
        $this->load->model('settings_model');

    }


    function index() {

        $this->form_validation->set_rules('site_name', lang('site_name'), 'required');
        $this->form_validation->set_rules('tel', lang('tel'), 'required');
        $this->form_validation->set_rules('language', lang('language'), 'required');
        $this->form_validation->set_rules('currency_prefix', lang('currency_code'), 'required|max_length[3]|min_length[3]');
        $this->form_validation->set_rules('default_discount', lang('default_discount'), 'required');
        $this->form_validation->set_rules('tax_rate', lang('default_tax_rate'), 'required');
        $this->form_validation->set_rules('rows_per_page', lang('rows_per_page'), 'required');
        $this->form_validation->set_rules('display_product', lang('display_product'), 'required');
        $this->form_validation->set_rules('pro_limit', lang('pro_limit'), 'required');
        $this->form_validation->set_rules('display_kb', lang('display_kb'), 'required');
        $this->form_validation->set_rules('default_customer', lang('default_customer'), 'required');
        $this->form_validation->set_rules('dateformat', lang('date_format'), 'required');
        $this->form_validation->set_rules('timeformat', lang('time_format'), 'required');
        $this->form_validation->set_rules('item_addition', lang('item_addition'), 'required');
        if ($this->input->post('protocol') == 'smtp') {
            $this->form_validation->set_rules('smtp_host', lang('smtp_host'), 'required');
            $this->form_validation->set_rules('smtp_user', lang('smtp_user'), 'required');
            $this->form_validation->set_rules('smtp_pass', lang('smtp_pass'), 'required');
            $this->form_validation->set_rules('smtp_port', lang('smtp_port'), 'required');
        }
        if ($this->input->post('stripe')) {
            $this->form_validation->set_rules('stripe_secret_key', lang('stripe_secret_key'), 'required');
            $this->form_validation->set_rules('stripe_publishable_key', lang('stripe_publishable_key'), 'required');
        }
        // $this->form_validation->set_rules('bill_header', lang('bill_header'), 'required');
        // $this->form_validation->set_rules('bill_footer', lang('bill_footer'), 'required');

        if ($this->form_validation->run() == true) {

            $data = array(
                'site_name' => DEMO ? 'SimplePOS' : $this->input->post('site_name'),
                'language' => $this->input->post('language'),
                'tel' => $this->input->post('tel'),
                'currency_prefix' => DEMO ? 'USD' : strtoupper($this->input->post('currency_prefix')),
                'default_tax_rate' => $this->input->post('tax_rate'),
                'default_discount' => $this->input->post('default_discount'),
                'rows_per_page' => $this->input->post('rows_per_page'),
                'bsty' => $this->input->post('display_product'),
                'pro_limit' => $this->input->post('pro_limit'),
                'display_kb' => $this->input->post('display_kb'),
                'default_category' => $this->input->post('default_category'),
                'default_customer' => $this->input->post('default_customer'),
                'barcode_symbology' => $this->input->post('barcode_symbology'),
                'dateformat' => DEMO ? 'jS F Y' : $this->input->post('dateformat'),
                'timeformat' => DEMO ? 'h:i A' : $this->input->post('timeformat'),
                'header' => $this->input->post('bill_header'),
                'footer' => $this->input->post('bill_footer'),
                'default_email' => DEMO ? 'noreply@spos.tecdiary.my' : $this->input->post('default_email'),
                'protocol' => $this->input->post('protocol'),
                'smtp_host' => $this->input->post('smtp_host'),
                'smtp_user' => $this->input->post('smtp_user'),
                'smtp_port' => $this->input->post('smtp_port'),
                'smtp_crypto' => $this->input->post('smtp_crypto'),
                'pin_code' => $this->input->post('pin_code') ? $this->input->post('pin_code') : NULL,
                // 'receipt_printer' => $this->input->post('receipt_printer'),
                // 'cash_drawer_codes' => $this->input->post('cash_drawer_codes'),
                'focus_add_item' => $this->input->post('focus_add_item'),
                'add_customer' => $this->input->post('add_customer'),
                'toggle_category_slider' => $this->input->post('toggle_category_slider'),
                'cancel_sale' => $this->input->post('cancel_sale'),
                'suspend_sale' => $this->input->post('suspend_sale'),
                'print_order' => $this->input->post('print_order'),
                'print_bill' => $this->input->post('print_bill'),
                'finalize_sale' => $this->input->post('finalize_sale'),
                'today_sale' => $this->input->post('today_sale'),
                'open_hold_bills' => $this->input->post('open_hold_bills'),
                'close_register' => $this->input->post('close_register'),
                // 'pos_printers' => $this->input->post('pos_printers'),
                // 'java_applet' => DEMO ? '0' : $this->input->post('enable_java_applet'),
                'rounding' => $this->input->post('rounding'),
                'item_addition' => $this->input->post('item_addition'),
                'stripe' => $this->input->post('stripe'),
                'stripe_secret_key' => $this->input->post('stripe_secret_key'),
                'stripe_publishable_key' => $this->input->post('stripe_publishable_key'),
                'theme' => $this->input->post('theme'),
                'theme_style' => $this->input->post('theme_style'),
                'after_sale_page' => $this->input->post('after_sale_page'),
                'multi_store' => $this->input->post('multi_store'),
                'overselling' => $this->input->post('overselling'),
                'decimals' => $this->input->post('decimals'),
                'decimals_sep' => $this->input->post('decimals_sep'),
                'thousands_sep' => $this->input->post('thousands_sep'),
                'sac' => $this->input->post('sac'),
                'qty_decimals' => $this->input->post('qty_decimals'),
                'display_symbol' => $this->input->post('display_symbol'),
                'symbol' => $this->input->post('symbol'),
                'printer' => $this->input->post('receipt_printer'),
                'order_printers' => json_encode($this->input->post('order_printers')),
                'auto_print' => $this->input->post('auto_print'),
                'remote_printing' => DEMO ? 1 : $this->input->post('remote_printing'),
                'local_printers' => $this->input->post('local_printers'),
                'rtl' => $this->input->post('rtl'),
                'print_img' => $this->input->post('print_img'),
            );
            if ($this->input->post('smtp_pass')) {
                $data['smtp_pass'] = $this->input->post('smtp_pass');
            }

            if (DEMO) {
                $data['site_name'] = 'SimplePOS';
            } else {
                if (TIMEZONE != $this->input->post('timezone')) {
                    $this->write_index($this->input->post('timezone'));
                }

                if ($_FILES['userfile']['size'] > 0) {
                    $this->load->library('upload');
                    $config['upload_path'] = 'uploads/';
                    $config['allowed_types'] = 'gif|jpg|png';
                    $config['max_size'] = '300';
                    $config['max_width'] = '300';
                    $config['max_height'] = '80';
                    $config['overwrite'] = FALSE;
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload()) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('message', $error);
                        redirect('settings');
                    }

                    $photo = $this->upload->file_name;
                }
            }
            if(isset($photo)) { $data['logo'] = $photo; }
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateSetting($data)) {

            $this->session->set_flashdata('message', lang('setting_updated'));
            if (TIMEZONE != $this->input->post('timezone')) {
                redirect('/');
            } else {
                redirect('settings');
            }

        } else {

            $this->load->helper('timezone');
            $this->data['timezones'] = get_timezones();
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['settings'] = $this->site->getSettings();
            $this->data['customers'] = $this->site->getAllCustomers();
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['printers'] = $this->site->getAllPrinters();
            $this->data['page_title'] = lang('settings');
            $bc = array(array('link' => '#', 'page' => lang('settings')));
            $meta = array('page_title' => lang('settings'), 'bc' => $bc);
            $this->page_construct('settings/index', $this->data, $meta);

        }
    }

    function updates() {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        $this->form_validation->set_rules('purchase_code', lang("purchase_code"), 'required');
        $this->form_validation->set_rules('envato_username', lang("envato_username"), 'required');
        if ($this->form_validation->run() == true) {
            $this->db->update('settings', array('purchase_code' => $this->input->post('purchase_code', TRUE), 'envato_username' => $this->input->post('envato_username', TRUE)), array('setting_id' => 1));
            redirect('settings/updates');
        } else {
            $fields = array('version' => $this->Settings->version, 'code' => $this->Settings->purchase_code, 'username' => $this->Settings->envato_username, 'site' => base_url());
            $this->load->helper('update');
            $protocol = is_https() ? 'https://' : 'http://';
            $updates = get_remote_contents($protocol.'tecdiary.com/api/v1/update/', $fields);
            $this->data['updates'] = json_decode($updates);
            $bc = array(array('link' => site_url('settings'), 'page' => lang('settings')), array('link' => '#', 'page' => lang('updates')));
            $meta = array('page_title' => lang('updates'), 'bc' => $bc);
            $this->page_construct('settings/updates', $this->data, $meta);
        }
    }

    function install_update($file, $m_version, $version) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        $this->load->helper('update');
        save_remote_file($file . '.zip');
        $this->tec->unzip('./files/updates/' . $file . '.zip');
        if ($m_version) {
            $this->load->library('migration');
            if (!$this->migration->latest()) {
                $this->session->set_flashdata('error', $this->migration->error_string());
                redirect("settings/updates");
            }
        }
        $this->db->update('settings', array('version' => $version, 'update' => 0), array('setting_id' => 1));
        unlink('./files/updates/' . $file . '.zip');
        $this->session->set_flashdata('success', lang('update_done'));
        redirect("settings/updates");
    }

    function backups() {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        $this->data['files'] = glob('./files/backups/*.zip', GLOB_BRACE);
        $this->data['dbs'] = glob('./files/backups/*.txt', GLOB_BRACE);
        krsort($this->data['files']); krsort($this->data['dbs']);
        $bc = array(array('link' => site_url('settings'), 'page' => lang('settings')), array('link' => '#', 'page' => lang('backups')));
        $meta = array('page_title' => lang('backups'), 'bc' => $bc);
        $this->page_construct('settings/backups', $this->data, $meta);
    }

    function backup_database() {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        $this->load->dbutil();
        $prefs = array(
            'format' => 'txt',
            'filename' => 'spos_db_backup.sql'
        );
        $back = $this->dbutil->backup($prefs);
        $backup =& $back;
        $db_name = 'db-backup-on-' . date("Y-m-d-H-i-s") . '.txt';
        $save = './files/backups/' . $db_name;
        $this->load->helper('file');
        write_file($save, $backup);
        $this->session->set_flashdata('messgae', lang('db_saved'));
        redirect("settings/backups");
    }

    function backup_files() {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        $name = 'file-backup-' . date("Y-m-d-H-i-s");
        set_time_limit(300);
        $this->tec->zip("./", './files/backups/', $name);
        $this->session->set_flashdata('messgae', lang('backup_saved'));
        redirect("settings/backups");
        exit();
    }

    function restore_database($dbfile) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        $file = file_get_contents('./files/backups/' . $dbfile . '.txt');
        $this->db->conn_id->multi_query($file);
        $this->db->conn_id->close();
        redirect('logout/db');
    }

    function download_database($dbfile) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        $this->load->library('zip');
        $this->zip->read_file('./files/backups/' . $dbfile . '.txt');
        $name = 'db_backup_' . date('Y_m_d_H_i_s') . '.zip';
        $this->zip->download($name);
        exit();
    }

    function download_backup($zipfile) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        $this->load->helper('download');
        force_download('./files/backups/' . $zipfile . '.zip', NULL);
        exit();
    }

    function restore_backup($zipfile) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        $file = './files/backups/' . $zipfile . '.zip';
        $this->tec->unzip($file, './');
        $this->session->set_flashdata('success', lang('files_restored'));
        redirect("settings/backups");
        exit();
    }

    function delete_database($dbfile) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        unlink('./files/backups/' . $dbfile . '.txt');
        $this->session->set_flashdata('messgae', lang('db_deleted'));
        redirect("settings/backups");
    }

    function delete_backup($zipfile) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("welcome");
        }
        unlink('./files/backups/' . $zipfile . '.zip');
        $this->session->set_flashdata('messgae', lang('backup_deleted'));
        redirect("settings/backups");
    }

    function stores() {

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('stores');
        $bc = array(array('link' => '#', 'page' => lang('stores')));
        $meta = array('page_title' => lang('stores'), 'bc' => $bc);
        $this->page_construct('settings/stores', $this->data, $meta);
    }

    function get_stores() {

        $this->load->library('datatables');
        $this->datatables
        ->select("id, name, code, phone, email, address1, city")
        ->from("stores")
        ->add_column("Actions", "<div class='text-center'><a href='" . site_url('settings/edit_store/$1') . "' class='tip' title='".$this->lang->line("edit_store")."'><i class='fa fa-edit'></i></a></div>", "id")
        ->unset_column('id');
        // <a href='" . site_url('settings/delete_store/$1') . "' onClick=\"return confirm('". $this->lang->line('alert_x_store') ."')\" class='tip btn btn-danger btn-xs' title='".$this->lang->line("delete_store")."'><i class='fa fa-trash-o'></i></a>
        echo $this->datatables->generate();

    }

    function add_store() {

        $this->form_validation->set_rules('name', $this->lang->line("name"), 'required');
        $this->form_validation->set_rules('email', $this->lang->line("email_address"), 'valid_email');
        $this->form_validation->set_rules('code', $this->lang->line("code"), 'required|is_unique[stores.code]|min_length[2]|max_length[20]');
        $this->form_validation->set_rules('phone', $this->lang->line("phone"), 'required');

        if ($this->form_validation->run() == true) {

            $data = array('name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'address1' => $this->input->post('address1'),
                'address2' => $this->input->post('address2'),
                'city' => $this->input->post('city'),
                'state' => $this->input->post('state'),
                'postal_code' => $this->input->post('postal_code'),
                'country' => $this->input->post('country'),
                'receipt_header' => $this->input->post('receipt_header'),
                'receipt_footer' => $this->input->post('receipt_footer'),
            );

            if ($_FILES['userfile']['size'] > 0) {

                $this->load->library('upload');

                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size'] = '500';
                $config['max_width'] = '300';
                $config['max_height'] = '100';
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("settings/add_store");
                }

                $photo = $this->upload->file_name;
                $data['logo'] = $photo;

            }

        }

        if ( $this->form_validation->run() == true && $cid = $this->settings_model->addStore($data)) {

            $this->session->set_flashdata('message', $this->lang->line("store_added"));
            redirect("settings/stores");

        } else {
            if($this->input->is_ajax_request()) {
                echo json_encode(array('status' => 'failed', 'msg' => validation_errors())); die();
            }

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['page_title'] = lang('add_store');
            $bc = array(array('link' => site_url('settings'), 'page' => lang('settings')), array('link' => site_url('settings/stores'), 'page' => lang('stores')), array('link' => '#', 'page' => lang('add_store')));
            $meta = array('page_title' => lang('add_store'), 'bc' => $bc);
            $this->page_construct('settings/add_store', $this->data, $meta);

        }
    }

    function edit_store($id = NULL) {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', $this->lang->line('access_denied'));
            redirect('pos');
        }
        if($this->input->get('id')) { $id = $this->input->get('id', TRUE); }

        $store = $this->settings_model->getStoreByID($id);
        if ($this->input->post('code') != $store->code) {
            $this->form_validation->set_rules('code', $this->lang->line("code"), 'is_unique[stores.code]');
        }
        $this->form_validation->set_rules('name', $this->lang->line("name"), 'required');
        $this->form_validation->set_rules('email', $this->lang->line("email_address"), 'valid_email');
        $this->form_validation->set_rules('code', $this->lang->line("code"), 'required|min_length[2]|max_length[20]');
        $this->form_validation->set_rules('phone', $this->lang->line("phone"), 'required');

        if ($this->form_validation->run() == true) {

            $data = array('name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'address1' => $this->input->post('address1'),
                'address2' => $this->input->post('address2'),
                'city' => $this->input->post('city'),
                'state' => $this->input->post('state'),
                'postal_code' => $this->input->post('postal_code'),
                'country' => $this->input->post('country'),
                'receipt_header' => $this->input->post('receipt_header'),
                'receipt_footer' => $this->input->post('receipt_footer'),
            );

            if ($_FILES['userfile']['size'] > 0) {

                $this->load->library('upload');

                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size'] = '500';
                $config['max_width'] = '300';
                $config['max_height'] = '100';
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("settings/add_store");
                }

                $photo = $this->upload->file_name;
                $data['logo'] = $photo;

            }

        }

        if ( $this->form_validation->run() == true && $this->settings_model->updateStore($id, $data)) {

            $this->session->set_flashdata('message', $this->lang->line("store_updated"));
            redirect("settings/stores");

        } else {

            $this->data['store'] = $store;
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['page_title'] = lang('edit_store');
            $bc = array(array('link' => site_url('settings'), 'page' => lang('settings')), array('link' => site_url('settings/stores'), 'page' => lang('stores')), array('link' => '#', 'page' => lang('edit_store')));
            $meta = array('page_title' => lang('edit_store'), 'bc' => $bc);
            $this->page_construct('settings/edit_store', $this->data, $meta);

        }
    }

    function delete_store($id = NULL) {
        // if (DEMO) {
        //     $this->session->set_flashdata('error', $this->lang->line("disabled_in_demo"));
        //     redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        // }

        // if ($this->input->get('id')) { $id = $this->input->get('id', TRUE); }

        // if ($id == 1) {
        //     $this->session->set_flashdata('error', lang("x_delete_1st_store"));
        //     redirect("settings/stores");
        // }

        // if ($this->settings_model->deleteStore($id)) {
            // $this->session->set_flashdata('message', lang("store_deleted"));
            redirect("settings/stores");
        // }

    }

    function printers() {
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('printers');
        $bc = array(array('link' => '#', 'page' => lang('printers')));
        $meta = array('page_title' => lang('printers'), 'bc' => $bc);
        $this->page_construct('settings/printers', $this->data, $meta);
    }

    function get_printers() {

        $this->load->library('datatables');
        $this->datatables
        ->select("id, title, type, profile, path, ip_address, port")
        ->from("printers")
        ->add_column("Actions", "<div class='text-center'><a href='" . site_url('settings/edit_printer/$1') . "' class='tip btn btn-warning btn-xs' title='".$this->lang->line("edit_printer")."'><i class='fa fa-edit'></i></a> <a href='" . site_url('settings/delete_printer/$1') . "' onClick=\"return confirm('". $this->lang->line('alert_x_printer') ."')\" class='tip btn btn-danger btn-xs' title='".$this->lang->line("delete_printer")."'><i class='fa fa-trash-o'></i></a></div>", "id")
        ->unset_column('id');
        echo $this->datatables->generate();

    }

    function add_printer() {

        $this->form_validation->set_rules('title', $this->lang->line("title"), 'required');
        $this->form_validation->set_rules('type', $this->lang->line("type"), 'required');
        $this->form_validation->set_rules('profile', $this->lang->line("profile"), 'required');
        $this->form_validation->set_rules('char_per_line', $this->lang->line("char_per_line"), 'required');
        if ($this->input->post('type') == 'network') {
            $this->form_validation->set_rules('ip_address', $this->lang->line("ip_address"), 'required|is_unique[printers.ip_address]');
            $this->form_validation->set_rules('port', $this->lang->line("port"), 'required');
        } else {
            $this->form_validation->set_rules('path', $this->lang->line("path"), 'required|is_unique[printers.path]');
        }

        if ($this->form_validation->run() == true) {

            $data = array('title' => $this->input->post('title'),
                'type' => $this->input->post('type'),
                'profile' => $this->input->post('profile'),
                'char_per_line' => $this->input->post('char_per_line'),
                'path' => $this->input->post('path'),
                'ip_address' => $this->input->post('ip_address'),
                'port' => ($this->input->post('type') == 'network') ? $this->input->post('port') : NULL,
            );

        }

        if ( $this->form_validation->run() == true && $cid = $this->settings_model->addPrinter($data)) {

            $this->session->set_flashdata('message', $this->lang->line("printer_added"));
            redirect("settings/printers");

        } else {
            if($this->input->is_ajax_request()) {
                echo json_encode(array('status' => 'failed', 'msg' => validation_errors())); die();
            }

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['page_title'] = lang('add_printer');
            $bc = array(array('link' => site_url('settings'), 'page' => lang('settings')), array('link' => site_url('settings/printers'), 'page' => lang('printers')), array('link' => '#', 'page' => lang('add_printer')));
            $meta = array('page_title' => lang('add_printer'), 'bc' => $bc);
            $this->page_construct('settings/add_printer', $this->data, $meta);

        }
    }

    function edit_printer($id = NULL) {

        if($this->input->get('id')) { $id = $this->input->get('id', TRUE); }

        $printer = $this->site->getPrinterByID($id);
        $this->form_validation->set_rules('title', $this->lang->line("title"), 'required');
        $this->form_validation->set_rules('type', $this->lang->line("type"), 'required');
        $this->form_validation->set_rules('profile', $this->lang->line("profile"), 'required');
        $this->form_validation->set_rules('char_per_line', $this->lang->line("char_per_line"), 'required');
        if ($this->input->post('type') == 'network') {
            $this->form_validation->set_rules('ip_address', $this->lang->line("ip_address"), 'required');
            if ($this->input->post('ip_address') != $printer->ip_address) {
                $this->form_validation->set_rules('ip_address', $this->lang->line("ip_address"), 'is_unique[printers.ip_address]');
            }
            $this->form_validation->set_rules('port', $this->lang->line("port"), 'required');
        } else {
            $this->form_validation->set_rules('path', $this->lang->line("path"), 'required');
            if ($this->input->post('path') != $printer->path) {
                $this->form_validation->set_rules('path', $this->lang->line("path"), 'is_unique[printers.path]');
            }
        }

        if ($this->form_validation->run() == true) {

            $data = array('title' => $this->input->post('title'),
                'type' => $this->input->post('type'),
                'profile' => $this->input->post('profile'),
                'char_per_line' => $this->input->post('char_per_line'),
                'path' => $this->input->post('path'),
                'ip_address' => $this->input->post('ip_address'),
                'port' => ($this->input->post('type') == 'network') ? $this->input->post('port') : NULL,
            );

        }

        if ( $this->form_validation->run() == true && $this->settings_model->updatePrinter($id, $data)) {

            $this->session->set_flashdata('message', $this->lang->line("printer_updated"));
            redirect("settings/printers");

        } else {

            $this->data['printer'] = $printer;
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['page_title'] = lang('edit_printer');
            $bc = array(array('link' => site_url('settings'), 'page' => lang('settings')), array('link' => site_url('settings/printers'), 'page' => lang('printers')), array('link' => '#', 'page' => lang('edit_printer')));
            $meta = array('page_title' => lang('edit_printer'), 'bc' => $bc);
            $this->page_construct('settings/edit_printer', $this->data, $meta);

        }
    }

    function delete_printer($id = NULL) {
        if(DEMO) {
            $this->session->set_flashdata('error', $this->lang->line("disabled_in_demo"));
            redirect('pos');
        }

        if ($this->input->get('id')) { $id = $this->input->get('id', TRUE); }

        if ($this->settings_model->deletePrinter($id)) {
            $this->session->set_flashdata('message', lang("printer_deleted"));
            redirect("settings/printers");
        }

    }

    function timezone() {
        $this->tec->send_json(['timezone' => TIMEZONE]);
    }

    function write_index($timezone) {
        $output_path    = FCPATH.'/index.php';
        $template_path  = FCPATH.'/files/config/index.php';
        $index_file = file_get_contents($template_path);
        $saved  = str_replace("%TIMEZONE%", $timezone,$index_file);
        $handle = fopen($output_path,'w+');
        @chmod($output_path,0777);
        if(is_writable($output_path)) {
            if(fwrite($handle,$saved)) {
                @chmod($output_path,0644);
                return true;
            }
            return false;
        }
        return false;
    }
}
