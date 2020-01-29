<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reset extends MY_Controller
{

    function __construct() {
        parent::__construct();
    }

    function index() {
        show_404();
    }

    function demo() {
        if (DEMO) {
            $this->db->truncate('combo_items');
            $this->db->truncate('expenses');
            $this->db->truncate('login_attempts');
            $this->db->truncate('sessions');
            $this->db->truncate('suspended_items');
            $this->db->truncate('suspended_sales');
            $this->db->truncate('user_logins');
            $this->db->truncate('printers');

            $this->db->truncate('categories');
            $this->db->truncate('customers');
            $this->db->truncate('payments');
            $this->db->truncate('products');
            $this->db->truncate('product_store_qty');
            $this->db->truncate('purchases');
            $this->db->truncate('purchase_items');
            $this->db->truncate('registers');
            $this->db->truncate('sales');
            $this->db->truncate('sale_items');
            $this->db->truncate('settings');
            $this->db->truncate('stores');
            $this->db->truncate('gift_cards');
            $this->db->truncate('suppliers');
            $this->db->truncate('users');

            $file = file_get_contents('./files/demo.sql');
            $this->db->conn_id->multi_query($file);
            $this->db->conn_id->close();
            $this->load->dbutil();
            $this->dbutil->optimize_database();

            redirect('login');
        } else {
            echo '<!DOCTYPE html>
            <html>
                <head>
                    <title>Stock Manager Advance</title>
                    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
                    <style>
                        html, body { height: 100%; }
                        body { margin: 0; padding: 0; width: 100%; display: table; font-weight: 72; font-family: \'Lato\'; }
                        .container { text-align: center; display: table-cell; vertical-align: middle; }
                        .content { text-align: center; display: inline-block; }
                        .title { font-size: 72px; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="content">
                            <div class="title">Demo is disabled!</div>
                        </div>
                    </div>
                </body>
            </html>
            ';
        }
    }

}
