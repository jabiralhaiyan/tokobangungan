<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pos extends MY_Controller {

    function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            redirect('login');
        }
        $this->load->helper('pos');
        $this->load->model('pos_model');
        $this->load->library('form_validation');

    }

    function index($sid = NULL, $eid = NULL) {
        if (!$this->Settings->multi_store) {
            $this->session->set_userdata('store_id', 1);
        }
        if ( ! $this->session->userdata('store_id')) {
            $this->session->set_flashdata('warning', lang("please_select_store"));
            redirect($this->Settings->multi_store ? 'stores' : 'welcome');
        }
        if( $this->input->get('hold') ) { $sid = $this->input->get('hold'); }
        if( $this->input->get('edit') ) { $eid = $this->input->get('edit'); }
        if( $this->input->post('eid') ) { $eid = $this->input->post('eid'); }
        if( $this->input->post('did') ) { $did = $this->input->post('did'); } else { $did = NULL; }
        if($eid && !$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'pos');
        }
        if (!$this->Settings->default_customer) {
            $this->session->set_flashdata('warning', lang('please_update_settings'));
            redirect('settings');
        }
        if (!$this->session->userdata('register_id')) {
            if ($register = $this->pos_model->registerData($this->session->userdata('user_id'))) {
                $register_data = array('register_id' => $register->id, 'cash_in_hand' => $register->cash_in_hand, 'register_open_time' => $register->date);
                $this->session->set_userdata($register_data);
            } else {
                $this->session->set_flashdata('error', lang('register_not_open'));
                redirect('pos/open_register');
            }
        }

        $suspend = $this->input->post('suspend') ? TRUE : FALSE;

        $this->form_validation->set_rules('customer', lang("customer"), 'trim|required');

        if ($this->form_validation->run() == true) {

            $quantity = "quantity";
            $product = "product";
            $unit_cost = "unit_cost";
            $tax_rate = "tax_rate";

            $date = $eid ? $this->input->post('date') : date('Y-m-d H:i:s');
            $customer_id = $this->input->post('customer_id');
            $customer_details = $this->pos_model->getCustomerByID($customer_id);
            $customer = $customer_details->name;
            $note = $this->tec->clear_tags($this->input->post('spos_note'));

            $total = 0;
            $product_tax = 0;
            $order_tax = 0;
            $product_discount = 0;
            $order_discount = 0;
            $percentage = '%';
            $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $item_id = $_POST['product_id'][$r];
                $real_unit_price = $this->tec->formatDecimal($_POST['real_unit_price'][$r]);
                $item_quantity = $_POST['quantity'][$r];
                $item_comment = $_POST['item_comment'][$r];
                $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : '0';

                if (isset($item_id) && isset($real_unit_price) && isset($item_quantity)) {
                    $product_details = $this->site->getProductByID($item_id);
                    if ($product_details) {
                        $product_name = $product_details->name;
                        $product_code = $product_details->code;
                        $product_cost = $product_details->cost;
                    } else {
                        $product_name = $_POST['product_name'][$r];
                        $product_code = $_POST['product_code'][$r];
                        $product_cost = 0;
                    }
                    if (!$this->Settings->overselling) {
                        if ($product_details->type == 'standard') {
                            if ($product_details->quantity < $item_quantity) {
                                $this->session->set_flashdata('error', lang("quantity_low").' ('.
                                    lang('name').': '.$product_details->name.' | '.
                                    lang('ordered').': '.$item_quantity.' | '.
                                    lang('available').': '.$product_details->quantity.
                                    ')');
                                redirect("pos");
                            }
                        } elseif ($product_details->type == 'combo') {
                            $combo_items = $this->pos_model->getComboItemsByPID($product->id);
                            foreach ($combo_items as $combo_item) {
                                $cpr = $this->site->getProductByID($combo_item->id);
                                if ($cpr->quantity < $item_quantity) {
                                    $this->session->set_flashdata('error', lang("quantity_low").' ('.
                                        lang('name').': '.$cpr->name.' | '.
                                        lang('ordered').': '.$item_quantity.' x '.$combo_item->qty.' = '.$item_quantity*$combo_item->qty.' | '.
                                        lang('available').': '.$cpr->quantity.
                                        ') '.$product_details->name);
                                    redirect("pos");
                                }
                            }
                        }
                    }
                    $unit_price = $real_unit_price;

                    $pr_discount = 0;
                    if (isset($item_discount)) {
                        $discount = $item_discount;
                        $dpos = strpos($discount, $percentage);
                        if ($dpos !== false) {
                            $pds = explode("%", $discount);
                            $pr_discount = $this->tec->formatDecimal((($unit_price * (Float)($pds[0])) / 100), 4);
                        } else {
                            $pr_discount = $this->tec->formatDecimal($discount);
                        }
                    }
                    $unit_price = $this->tec->formatDecimal(($unit_price - $pr_discount), 4);
                    $item_net_price = $unit_price;
                    $pr_item_discount = $this->tec->formatDecimal(($pr_discount * $item_quantity), 4);
                    $product_discount += $pr_item_discount;

                    $pr_item_tax = 0; $item_tax = 0; $tax = "";
                        if (isset($product_details->tax) && $product_details->tax != 0) {

                            if ($product_details && $product_details->tax_method == 1) {
                                $item_tax = $this->tec->formatDecimal(((($unit_price) * $product_details->tax) / 100), 4);
                                $tax = $product_details->tax . "%";
                            } else {
                                $item_tax = $this->tec->formatDecimal(((($unit_price) * $product_details->tax) / (100 + $product_details->tax)), 4);
                                $tax = $product_details->tax . "%";
                                $item_net_price -= $item_tax;
                            }

                            $pr_item_tax = $this->tec->formatDecimal(($item_tax * $item_quantity), 4);

                        }

                    $product_tax += $pr_item_tax;
                    $subtotal = $this->tec->formatDecimal((($item_net_price * $item_quantity) + $pr_item_tax), 4);

                    $products[] = array(
                        'product_id' => $item_id,
                        'quantity' => $item_quantity,
                        'unit_price' => $unit_price,
                        'net_unit_price' => $item_net_price,
                        'discount' => $item_discount,
                        'comment' => $item_comment,
                        'item_discount' => $pr_item_discount,
                        'tax' => $tax,
                        'item_tax' => $pr_item_tax,
                        'subtotal' => $subtotal,
                        'real_unit_price' => $real_unit_price,
                        'cost' => $product_cost,
                        'product_code' => $product_code,
                        'product_name' => $product_name,
                        );

                    $total += $this->tec->formatDecimal(($item_net_price * $item_quantity), 4);

                }
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }

            if ($this->input->post('order_discount')) {
                $order_discount_id = $this->input->post('order_discount');
                $opos = strpos($order_discount_id, $percentage);
                if ($opos !== false) {
                    $ods = explode("%", $order_discount_id);
                    $order_discount = $this->tec->formatDecimal(((($total + $product_tax) * (Float)($ods[0])) / 100), 4);
                } else {
                    $order_discount = $this->tec->formatDecimal($order_discount_id);
                }
            } else {
                $order_discount_id = NULL;
            }
            $total_discount = $this->tec->formatDecimal(($order_discount + $product_discount), 4);

            if($this->input->post('order_tax')) {
                $order_tax_id = $this->input->post('order_tax');
                $opos = strpos($order_tax_id, $percentage);
                if ($opos !== false) {
                    $ots = explode("%", $order_tax_id);
                    $order_tax = $this->tec->formatDecimal(((($total + $product_tax - $order_discount) * (Float)($ots[0])) / 100), 4);
                } else {
                    $order_tax = $this->tec->formatDecimal($order_tax_id);
                }

            } else {
                $order_tax_id = NULL;
                $order_tax = 0;
            }

            $total_tax = $this->tec->formatDecimal(($product_tax + $order_tax), 4);
            $grand_total = $this->tec->formatDecimal(($total + $total_tax - $order_discount), 4);
            $paid = $this->input->post('amount') ? $this->input->post('amount') : 0;
            $round_total = $this->tec->roundNumber($grand_total, $this->Settings->rounding);
            $rounding = $this->tec->formatDecimal(($round_total - $grand_total));
            if ($customer_details->id == 1 && $this->tec->formatDecimal($paid) < $this->tec->formatDecimal($round_total)) {
                $this->session->set_flashdata('error', lang('select_customer_for_due'));
                redirect($_SERVER["HTTP_REFERER"]);
            }
            if (!$eid) {
                $status = 'due';
                if ($this->tec->formatDecimal($round_total) <= $this->tec->formatDecimal($paid)) {
                    $status = 'paid';
                } elseif ($this->tec->formatDecimal($round_total) > $this->tec->formatDecimal($paid) && $paid > 0) {
                    $status = 'partial';
                }
            }

            $data = array('date' => $date,
                'customer_id' => $customer_id,
                'customer_name' => $customer,
                'total' => $this->tec->formatDecimal($total, 4),
                'product_discount' => $this->tec->formatDecimal($product_discount, 4),
                'order_discount_id' => $order_discount_id,
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $this->tec->formatDecimal($product_tax, 4),
                'order_tax_id' => $order_tax_id,
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'grand_total' => $grand_total,
                'total_items' => $this->input->post('total_items'),
                'total_quantity' => $this->input->post('total_quantity'),
                'rounding' => $rounding,
                'paid' => $paid,
                'status' => $status,
                'created_by' => $this->session->userdata('user_id'),
                'note' => $note,
                'hold_ref' => $this->input->post('hold_ref'),
                );

            if (!$eid) {
                $data['store_id'] = $this->session->userdata('store_id');
            }

            if (!$eid && !$suspend && $paid) {
                if ($this->input->post('paying_gift_card_no')) {
                    $gc = $this->pos_model->getGiftCardByNO($this->input->post('paying_gift_card_no'));
                    if (!$gc || $gc->balance < $amount) {
                        $this->session->set_flashdata('error', lang("incorrect_gift_card"));
                        redirect("pos");
                    }
                }
                $amount = $this->tec->formatDecimal(($paid > $grand_total ? ($paid - $this->input->post('balance_amount')) : $paid), 4);
                $payment = array(
                    'date' => $date,
                    'amount' => $amount,
                    'customer_id' => $customer_id,
                    'paid_by' => $this->input->post('paid_by'),
                    'cheque_no' => $this->input->post('cheque_no'),
                    'cc_no' => $this->input->post('cc_no'),
                    'gc_no' => $this->input->post('paying_gift_card_no'),
                    'cc_holder' => $this->input->post('cc_holder'),
                    'cc_month' => $this->input->post('cc_month'),
                    'cc_year' => $this->input->post('cc_year'),
                    'cc_type' => $this->input->post('cc_type'),
                    'cc_cvv2' => $this->input->post('cc_cvv2'),
                    'created_by' => $this->session->userdata('user_id'),
                    'store_id' => $this->session->userdata('store_id'),
                    'note' => $this->input->post('payment_note'),
                    'pos_paid' => $this->tec->formatDecimal($this->input->post('amount'), 4),
                    'pos_balance' => $this->tec->formatDecimal($this->input->post('balance_amount'), 4)
                    );
                $data['paid'] = $amount;

            } else {
                $payment = array();
            }

            // $this->tec->print_arrays($data, $products, $payment);
        }

        if ($this->form_validation->run() == true && !empty($products))
        {
            if ($suspend) {
                unset($data['status'], $data['rounding']);
                if ($this->pos_model->suspendSale($data, $products, $did)) {
                    $this->session->set_userdata('rmspos', 1);
                    $this->session->set_flashdata('message', lang("sale_saved_to_opened_bill"));
                    redirect("pos");
                } else {
                    $this->session->set_flashdata('error', lang("action_failed"));
                    redirect("pos/".$did);
                }

            } elseif($eid) {

                unset($data['status'], $data['paid']);
                if (!$this->Admin) {
                    unset($data['date']);
                }
                $data['updated_at'] = date('Y-m-d H:i:s');
                $data['updated_by'] = $this->session->userdata('user_id');
                if($this->pos_model->updateSale($eid, $data, $products)) {
                    $this->session->set_userdata('rmspos', 1);
                    $this->session->set_flashdata('message', lang("sale_updated"));
                    redirect("sales");
                }
                else {
                    $this->session->set_flashdata('error', lang("action_failed"));
                    redirect("pos/?edit=".$eid);
                }

            } else {

                if($sale = $this->pos_model->addSale($data, $products, $payment, $did)) {
                    $this->session->set_userdata('rmspos', 1);
                    $msg = lang("sale_added");
                    if (!empty($sale['message'])) {
                        foreach ($sale['message'] as $m) {
                            $msg .= '<br>' . $m;
                        }
                    }
                    $this->session->set_flashdata('message', $msg);
                    $redirect_to = $this->Settings->after_sale_page ? "pos" : "pos/view/" . $sale['sale_id'];
                    if ($this->Settings->auto_print) {
                        if ( ! $this->Settings->remote_printing) {
                            $this->print_receipt($sale['sale_id'], true);
                        } elseif ($this->Settings->remote_printing == 2) {
                            $redirect_to .= '?print='.$sale['sale_id'];
                        }
                    }
                    redirect($redirect_to);
                }
                else {
                    $this->session->set_flashdata('error', lang("action_failed"));
                    redirect("pos");
                }

            }
        }
        else
        {

            if(isset($sid) && !empty($sid)) {
                $suspended_sale = $this->pos_model->getSuspendedSaleByID($sid);
                $inv_items = $this->pos_model->getSuspendedSaleItems($sid);
                krsort($inv_items);
                $c = rand(100000, 9999999);
                foreach ($inv_items as $item) {
                    $row = $this->site->getProductByID($item->product_id);
                    if (!$row) {
                        $row = json_decode('{}');
                        $row->id = 0;
                        $row->code = $item->product_code;
                        $row->name = $item->product_name;
                        $row->tax = 0;
                    }
                    $row->price = $item->net_unit_price+($item->item_discount/$item->quantity);
                    $row->unit_price = $item->unit_price+($item->item_discount/$item->quantity)+($item->item_tax/$item->quantity);
                    $row->real_unit_price = $item->real_unit_price;
                    $row->discount = $item->discount;
                    $row->qty = $item->quantity;
                    $row->comment = $item->comment;
                    $row->ordered = $item->quantity;
                    $combo_items = FALSE;
                    $ri = $this->Settings->item_addition ? $row->id : $c;
                    $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'combo_items' => $combo_items);
                    $c++;
                }
                $this->data['items'] = json_encode($pr);
                $this->data['sid'] = $sid;
                $this->data['suspend_sale'] = $suspended_sale;
                $this->data['message'] = lang('suspended_sale_loaded');
            }

            if(isset($eid) && !empty($eid)) {
                $sale = $this->pos_model->getSaleByID($eid);
                $inv_items = $this->pos_model->getAllSaleItems($eid);
                krsort($inv_items);
                $c = rand(100000, 9999999);
                foreach ($inv_items as $item) {
                    $row = $this->site->getProductByID($item->product_id);
                    if (!$row) {
                        $row = json_decode('{}');
                    }
                    $row->price = $item->net_unit_price;
                    $row->unit_price = $item->unit_price;
                    $row->real_unit_price = $item->real_unit_price;
                    $row->discount = $item->discount;
                    $row->qty = $item->quantity;
                    $row->comment = $item->comment;
                    $combo_items = FALSE;
                    $row->quantity += $item->quantity;
                    if ($row->type == 'combo') {
                        $combo_items = $this->pos_model->getComboItemsByPID($row->id);
                        foreach ($combo_items as $combo_item) {
                            $combo_item->quantity += ($combo_item->qty*$item->quantity);
                        }
                    }
                    $ri = $this->Settings->item_addition ? $row->id : $c;
                    $pr[$ri] = array('id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'combo_items' => $combo_items);
                    $c++;
                }
                $this->data['items'] = json_encode($pr);
                $this->data['eid'] = $eid;
                $this->data['sale'] = $sale;
                $this->data['message'] = lang('sale_loaded');
            }
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['reference_note'] = isset($sid) && !empty($sid) ? $suspended_sale->hold_ref : (isset($eid) && !empty($eid) ? $sale->hold_ref : NULL);
            $this->data['sid'] = isset($sid) && !empty($sid) ? $sid : 0;
            $this->data['eid'] = isset($eid) && !empty($eid) ? $eid : 0;
            $this->data['customers'] = $this->site->getAllCustomers();
            $this->data["tcp"] = $this->pos_model->products_count($this->Settings->default_category);
            $this->data['products'] = $this->ajaxproducts($this->Settings->default_category, 1);
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['message'] = $this->session->flashdata('message');
            $this->data['suspended_sales'] = $this->site->getUserSuspenedSales();

            $this->data['printer'] = $this->site->getPrinterByID($this->Settings->printer);
            $printers = array();
            if (!empty($order_printers = json_decode($this->Settings->order_printers))) {
                foreach ($order_printers as $printer_id) {
                    $printers[] = $this->site->getPrinterByID($printer_id);
                }
            }
            $this->data['order_printers'] = $printers;

            if ($saleid = $this->input->get('print', true)) {
                if ($inv = $this->pos_model->getSaleByID($saleid)) {
                    if ($this->session->userdata('store_id') != $inv->store_id) {
                        $this->session->set_flashdata('error', lang('access_denied'));
                        redirect('pos');
                    }
                    $this->tec->view_rights($inv->created_by, false, 'pos');
                    $this->load->helper('text');
                    $this->data['rows'] = $this->pos_model->getAllSaleItems($saleid);
                    $this->data['customer'] = $this->pos_model->getCustomerByID($inv->customer_id);
                    $this->data['store'] = $this->site->getStoreByID($inv->store_id);
                    $this->data['inv'] = $inv;
                    $this->data['print'] = $saleid;
                    $this->data['payments'] = $this->pos_model->getAllSalePayments($saleid);
                    $this->data['created_by'] = $this->site->getUser($inv->created_by);
                }
            }

            $this->data['page_title'] = lang('pos');
            $bc = array(array('link' => '#', 'page' => lang('pos')));
            $meta = array('page_title' => lang('pos'), 'bc' => $bc);
            $this->load->view($this->theme.'pos/index', $this->data, $meta);

        }
    }


    function get_product($code = NULL) {

        if ($this->input->get('code')) { $code = $this->input->get('code'); }
        $combo_items = FALSE;
        if($product = $this->pos_model->getProductByCode($code)) {
            unset($product->cost, $product->details);
            $product->qty = 1;
            $product->comment = '';
            $product->discount = '0';
            $product->price = $product->store_price > 0 ? $product->store_price : $product->price;
            $product->real_unit_price = $product->price;
            $product->unit_price = $product->tax ? ($product->price+(($product->price*$product->tax)/100)) : $product->price;
            if ($product->type == 'combo') {
                $combo_items = $this->pos_model->getComboItemsByPID($product->id);
            }
            echo json_encode(array('id' => str_replace(".", "", microtime(true)), 'item_id' => $product->id, 'label' => $product->name . " (" . $product->code . ")", 'row' => $product, 'combo_items' => $combo_items));
        } else {
            echo NULL;
        }

    }

    function suggestions() {
        $term = $this->input->get('term', TRUE);

        $rows = $this->pos_model->getProductNames($term);
        if ($rows) {
            foreach ($rows as $row) {
                unset($row->cost, $row->details);
                $row->qty = 1;
                $row->comment = '';
                $row->discount = '0';
                $row->price = $row->store_price > 0 ? $row->store_price : $row->price;
                $row->real_unit_price = $row->price;
                $row->unit_price = $row->tax ? ($row->price+(($row->price*$row->tax)/100)) : $row->price;
                $combo_items = FALSE;
                if ($row->type == 'combo') {
                    $combo_items = $this->pos_model->getComboItemsByPID($row->id);
                }
                $pr[] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row, 'combo_items' => $combo_items);
            }
            echo json_encode($pr);
        } else {
            echo json_encode(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }


    function registers() {

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['registers'] = $this->pos_model->getOpenRegisters();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('pos'), 'page' => lang('pos')), array('link' => '#', 'page' => lang('open_registers')));
        $meta = array('page_title' => lang('open_registers'), 'bc' => $bc);
        $this->page_construct('pos/registers', $this->data, $meta);
    }

    function open_register() {
        if ( ! $this->session->userdata('store_id')) {
            $this->session->set_flashdata('warning', lang("please_select_store"));
            redirect('stores');
        }
        $this->form_validation->set_rules('cash_in_hand', lang("cash_in_hand"), 'trim|required|numeric');

        if ($this->form_validation->run() == true) {
            $data = array('date' => date('Y-m-d H:i:s'),
                'cash_in_hand' => $this->input->post('cash_in_hand'),
                'user_id' => $this->session->userdata('user_id'),
                'store_id' => $this->session->userdata('store_id'),
                'status' => 'open',
                );
        }
        if ($this->form_validation->run() == true && $this->pos_model->openRegister($data)) {
            $this->session->set_flashdata('message', lang("welcome_to_pos"));
            redirect("pos");
        } else {

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('open_register')));
            $meta = array('page_title' => lang('open_register'), 'bc' => $bc);
            $this->page_construct('pos/open_register', $this->data, $meta);
        }
    }

    function close_register($user_id = NULL) {
        if (!$this->Admin) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->form_validation->set_rules('total_cash', lang("total_cash"), 'trim|required|numeric');
        $this->form_validation->set_rules('total_cheques', lang("total_cheques"), 'trim|required|numeric');
        $this->form_validation->set_rules('total_cc_slips', lang("total_cc_slips"), 'trim|required|numeric');

        if ($this->form_validation->run() == true) {
            if ($this->Admin) {
                $user_register = $user_id ? $this->pos_model->registerData($user_id) : NULL;
                $rid = $user_register ? $user_register->id : $this->session->userdata('register_id');
                $user_id = $user_register ? $user_register->user_id : $this->session->userdata('user_id');
                $register_open_time = $user_register ? $user_register->date : $this->session->userdata('register_open_time');
                $cash_in_hand = $user_register ? $user_register->cash_in_hand : $this->session->userdata('cash_in_hand');
                $ccsales = $this->pos_model->getRegisterCCSales($register_open_time, $user_id);
                $cashsales = $this->pos_model->getRegisterCashSales($register_open_time, $user_id);
                $expenses = $this->pos_model->getRegisterExpenses($register_open_time, $user_id);
                $chsales = $this->pos_model->getRegisterChSales($register_open_time, $user_id);
                $total_cash = ($cashsales->paid ? ($cashsales->paid + $cash_in_hand) : $cash_in_hand);
                $total_cash -= ($expenses->total ? $expenses->total : 0);
            } else {
                $rid = $this->session->userdata('register_id');
                $user_id = $this->session->userdata('user_id');
                $register_open_time = $this->session->userdata('register_open_time');
                $cash_in_hand = $this->session->userdata('cash_in_hand');
                $ccsales = $this->pos_model->getRegisterCCSales($register_open_time);
                $cashsales = $this->pos_model->getRegisterCashSales($register_open_time);
                $expenses = $this->pos_model->getRegisterExpenses($register_open_time);
                $chsales = $this->pos_model->getRegisterChSales($register_open_time);
                $total_cash = ($cashsales->paid ? ($cashsales->paid + $cash_in_hand) : $cash_in_hand);
                $total_cash -= ($expenses->total ? $expenses->total : 0);
            }

            $data = array('closed_at' => date('Y-m-d H:i:s'),
                'total_cash' => $total_cash,
                'total_cheques' => $chsales->total_cheques,
                'total_cc_slips' => $ccsales->total_cc_slips,
                'total_cash_submitted' => $this->input->post('total_cash_submitted'),
                'total_cheques_submitted' => $this->input->post('total_cheques_submitted'),
                'total_cc_slips_submitted' => $this->input->post('total_cc_slips_submitted'),
                'note' => $this->input->post('note'),
                'status' => 'close',
                'transfer_opened_bills' => $this->input->post('transfer_opened_bills'),
                'closed_by' => $this->session->userdata('user_id'),
                );

            // $this->tec->print_arrays($data);

        } elseif ($this->input->post('close_register')) {
            $this->session->set_flashdata('error', (validation_errors() ? validation_errors() : $this->session->flashdata('error')));
            redirect("pos");
        }

        if ($this->form_validation->run() == true && $this->pos_model->closeRegister($rid, $user_id, $data)) {
            $this->session->unset_userdata('register_id');
            $this->session->unset_userdata('cash_in_hand');
            $this->session->unset_userdata('register_open_time');
            $this->session->set_flashdata('message', lang("register_closed"));
            redirect("welcome");
        } else {
            if ($this->Admin) {
                $user_register = $user_id ? $this->pos_model->registerData($user_id) : NULL;
                $register_open_time = $user_register ? $user_register->date : $this->session->userdata('register_open_time');
                $this->data['cash_in_hand'] = $user_register ? $user_register->cash_in_hand : NULL;
                $this->data['register_open_time'] = $user_register ? $register_open_time : NULL;
            } else {
                $register_open_time = $this->session->userdata('register_open_time');
                $this->data['cash_in_hand'] = NULL;
                $this->data['register_open_time'] = NULL;
            }
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['ccsales'] = $this->pos_model->getRegisterCCSales($register_open_time, $user_id);
            $this->data['cashsales'] = $this->pos_model->getRegisterCashSales($register_open_time, $user_id);
            $this->data['chsales'] = $this->pos_model->getRegisterChSales($register_open_time, $user_id);
            $this->data['other_sales'] = $this->pos_model->getRegisterOtherSales($register_open_time, $user_id);
            $this->data['gcsales'] = $this->pos_model->getRegisterGCSales($register_open_time, $user_id);
            $this->data['stripesales'] = $this->pos_model->getRegisterStripeSales($register_open_time, $user_id);
            $this->data['totalsales'] = $this->pos_model->getRegisterSales($register_open_time, $user_id);
            $this->data['expenses'] = $this->pos_model->getRegisterExpenses($register_open_time);
            $this->data['users'] = $this->tec->getUsers($user_id);
            $this->data['suspended_bills'] = $this->pos_model->getSuspendedsales($user_id);
            $this->data['user_id'] = $user_id;
            $this->load->view($this->theme . 'pos/close_register', $this->data);
        }
    }

    function ajaxproducts( $category_id = NULL, $return = NULL) {

        if($this->input->get('category_id')) { $category_id = $this->input->get('category_id'); } elseif(!$category_id) { $category_id = $this->Settings->default_category; }
        if($this->input->get('per_page') == 'n' ) { $page = 0; } else { $page = $this->input->get('per_page'); }
        if($this->input->get('tcp') == 1 ) { $tcp = TRUE; } else { $tcp = FALSE; }

        $products = $this->pos_model->fetch_products($category_id, $this->Settings->pro_limit, $page);
        $pro = 1;
        $prods = "<div>";
        if($products) {
            if($this->Settings->bsty == 1) {
                foreach($products as $product) {
                    $count = $product->id;
                    if($count < 10) { $count = "0".($count /100) *100;  }
                    if($category_id < 10) { $category_id = "0".($category_id /100) *100;  }
                    $prods .= "<button type=\"button\" data-name=\"".$product->name."\" id=\"product-".$category_id.$count."\" type=\"button\" value='".$product->code."' class=\"btn btn-name btn-default btn-flat product\">".$product->name."</button>";
                    $pro++;
                }
            } elseif($this->Settings->bsty == 2) {
                foreach($products as $product) {
                    $count = $product->id;
                    if($count < 10) { $count = "0".($count /100) *100;  }
                    if($category_id < 10) { $category_id = "0".($category_id /100) *100;  }
                    $prods .= "<button type=\"button\" data-name=\"".$product->name."\" id=\"product-".$category_id.$count."\" type=\"button\" value='".$product->code."' class=\"btn btn-img btn-flat product\"><img src=\"".base_url()."uploads/thumbs/".$product->image."\" alt=\"".$product->name."\" style=\"width: 110px; height: 110px;\"></button>";
                    $pro++;
                }
            } elseif($this->Settings->bsty == 3) {
                foreach($products as $product) {
                    $count = $product->id;
                    if($count < 10) { $count = "0".($count /100) *100;  }
                    if($category_id < 10) { $category_id = "0".($category_id /100) *100;  }
                    $prods .= "<button type=\"button\" data-name=\"".$product->name."\" id=\"product-".$category_id.$count."\" type=\"button\" value='".$product->code."' class=\"btn btn-both btn-flat product\"><span class=\"bg-img\"><img src=\"".base_url()."uploads/thumbs/".$product->image."\" alt=\"".$product->name."\" style=\"width: 100px; height: 100px;\"></span><span><span>".$product->name."</span></span></button>";
                    $pro++;
                }
            }
        } else {
            $prods .= '<h4 class="text-center text-info" style="margin-top:50px;">'.lang('category_is_empty').'</h4>';
        }

        $prods .= "</div>";

        if(!$return) {
            if(!$tcp) {
                echo $prods;
            } else {
                $category_products = $this->pos_model->products_count($category_id);
                header('Content-Type: application/json');
                echo json_encode(array('products' => $prods, 'tcp' => $category_products));
            }
        } else {
            return $prods;
        }

    }

    function view($sale_id = NULL, $noprint = NULL) {
        if($this->input->get('id')){ $sale_id = $this->input->get('id'); }
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['message'] = $this->session->flashdata('message');
        $inv = $this->pos_model->getSaleByID($sale_id);
        if ( ! $this->session->userdata('store_id')) {
            $this->session->set_flashdata('warning', lang("please_select_store"));
            redirect('stores');
        } elseif ($this->session->userdata('store_id') != $inv->store_id) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('welcome');
        }
        $this->tec->view_rights($inv->created_by);
        $this->load->helper('text');
        $this->data['rows'] = $this->pos_model->getAllSaleItems($sale_id);
        $this->data['customer'] = $this->pos_model->getCustomerByID($inv->customer_id);
        $this->data['store'] = $this->site->getStoreByID($inv->store_id);
        $this->data['inv'] = $inv;
        $this->data['sid'] = $sale_id;
        $this->data['noprint'] = $noprint;
        $this->data['modal'] = $noprint ? true : false;
        $this->data['payments'] = $this->pos_model->getAllSalePayments($sale_id);
        $this->data['created_by'] = $this->site->getUser($inv->created_by);
        $this->data['printer'] = $this->site->getPrinterByID($this->Settings->printer);
        $this->data['store'] = $this->site->getStoreByID($inv->store_id);
        $this->data['page_title'] = lang("invoice");
        $this->load->view($this->theme.'pos/'.($this->Settings->print_img ? 'eview' : 'view'), $this->data);

    }

    function email_receipt($sale_id = NULL, $to = NULL) {
        if($this->input->post('id')) { $sale_id = $this->input->post('id'); }
        if($this->input->post('email')){ $to = $this->input->post('email'); }
        if(!$sale_id || !$to) { die(); }

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['message'] = $this->session->flashdata('message');
        $inv = $this->pos_model->getSaleByID($sale_id);
        $this->tec->view_rights($inv->created_by);
        $this->load->helper('text');
        $this->data['rows'] = $this->pos_model->getAllSaleItems($sale_id);
        $this->data['customer'] = $this->pos_model->getCustomerByID($inv->customer_id);
        $this->data['inv'] = $inv;
        $this->data['sid'] = $sale_id;
        $this->data['noprint'] = NULL;
        $this->data['page_title'] = lang('invoice');
        $this->data['modal'] = false;
        $this->data['payments'] = $this->pos_model->getAllSalePayments($sale_id);
        $this->data['created_by'] = $this->site->getUser($inv->created_by);

        $receipt = $this->load->view($this->theme.'pos/view', $this->data, TRUE);
        $message = preg_replace('#\<!-- start -->(.+)\<!-- end -->#Usi', '', $receipt);
        $subject = lang('email_subject').' - '.$this->Settings->site_name;

        try {
            if ($this->tec->send_email($to, $subject, $message)) {
                echo json_encode(array('msg' => lang("email_success")));
            } else {
                echo json_encode(array('msg' => lang("email_failed")));
            }
        } catch (Exception $e) {
            echo json_encode(array('msg' => $e->getMessage()));
        }

    }


    function register_details() {

        $register_open_time = $this->session->userdata('register_open_time');
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['ccsales'] = $this->pos_model->getRegisterCCSales($register_open_time);
        $this->data['cashsales'] = $this->pos_model->getRegisterCashSales($register_open_time);
        $this->data['chsales'] = $this->pos_model->getRegisterChSales($register_open_time);
        $this->data['other_sales'] = $this->pos_model->getRegisterOtherSales($register_open_time);
        $this->data['gcsales'] = $this->pos_model->getRegisterGCSales($register_open_time);
        $this->data['stripesales'] = $this->pos_model->getRegisterStripeSales($register_open_time);
        $this->data['totalsales'] = $this->pos_model->getRegisterSales($register_open_time);
        $this->data['expenses'] = $this->pos_model->getRegisterExpenses($register_open_time);
        $this->load->view($this->theme . 'pos/register_details', $this->data);
    }

    function today_sale() {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['ccsales'] = $this->pos_model->getTodayCCSales();
        $this->data['cashsales'] = $this->pos_model->getTodayCashSales();
        $this->data['chsales'] = $this->pos_model->getTodayChSales();
        $this->data['other_sales'] = $this->pos_model->getTodayOtherSales();
        $this->data['gcsales'] = $this->pos_model->getTodayGCSales();
        $this->data['stripesales'] = $this->pos_model->getTodayStripeSales();
        $this->data['totalsales'] = $this->pos_model->getTodaySales();
        // $this->data['expenses'] = $this->pos_model->getTodayExpenses();
        $this->load->view($this->theme . 'pos/today_sale', $this->data);
    }

    function shortcuts() {
        $this->load->view($this->theme . 'pos/shortcuts', $this->data);
    }

    function view_bill() {
        $this->load->view($this->theme . 'pos/view_bill', $this->data);
    }

    function promotions() {
        $this->load->view($this->theme . 'promotions', $this->data);
    }

    function stripe_balance() {
        if (!$this->Owner) {
            return FALSE;
        }
        $this->load->model('stripe_payments');
        return $this->stripe_payments->get_balance();
    }

    function language($lang = false) {
        if ($this->input->get('lang')) {
            $lang = $this->input->get('lang');
        }
        //$this->load->helper('cookie');
        $folder = 'app/language/';
        $languagefiles = scandir($folder);
        if (in_array($lang, $languagefiles)) {
            $cookie = array(
                'name' => 'language',
                'value' => $lang,
                'expire' => '31536000',
                'prefix' => 'spos_',
                'secure' => false
            );

            $this->input->set_cookie($cookie);
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }

    function validate_gift_card($no) {
        if ($gc = $this->pos_model->getGiftCardByNO(urldecode($no))) {
            if ($gc->expiry) {
                if ($gc->expiry >= date('Y-m-d')) {
                    echo json_encode($gc);
                } else {
                    echo json_encode(false);
                }
            } else {
                echo json_encode($gc);
            }
        } else {
            echo json_encode(false);
        }
    }

    function print_register($re = NULL) {

        if ($this->session->userdata('register_id')) {

            $register = $this->pos_model->registerData();
            $ccsales = $this->pos_model->getRegisterCCSales();
            $cashsales = $this->pos_model->getRegisterCashSales();
            $chsales = $this->pos_model->getRegisterChSales();
            $other_sales = $this->pos_model->getRegisterOtherSales();
            $gcsales = $this->pos_model->getRegisterGCSales();
            $stripesales = $this->pos_model->getRegisterStripeSales();
            $totalsales = $this->pos_model->getRegisterSales();
            $expenses = $this->pos_model->getRegisterExpenses();
            $user = $this->site->getUser();

            $total_cash = $cashsales->paid ? ($cashsales->paid + $register->cash_in_hand) : $register->cash_in_hand;
            $total_cash -= ($expenses->total ? $expenses->total : 0);
            $info = array(
                (object) array('label' => lang('opened_at'), 'value' => $this->tec->hrld($register->date)),
                (object) array('label' => lang('cash_in_hand'), 'value' => $register->cash_in_hand),
                (object) array('label' => lang('user'), 'value' => $user->first_name.' '.$user->last_name.' ('.$user->email.')'),
                (object) array('label' => lang('printed_at'),  'value' => $this->tec->hrld(date('Y-m-d H:i:s')))
                );

            $reg_totals = array(
                (object) array('label' => lang('cash_sale'), 'value' => $this->tec->formatMoney($cashsales->paid ? $cashsales->paid : '0.00') . ' (' . $this->tec->formatMoney($cashsales->total ? $cashsales->total : '0.00') . ')'),
                (object) array('label' => lang('ch_sale'), 'value' => $this->tec->formatMoney($chsales->paid ? $chsales->paid : '0.00') . ' (' . $this->tec->formatMoney($chsales->total ? $chsales->total : '0.00') . ')'),
                (object) array('label' => lang('gc_sale'),  'value' => $this->tec->formatMoney($gcsales->paid ? $gcsales->paid : '0.00') . ' (' . $this->tec->formatMoney($gcsales->total ? $gcsales->total : '0.00') . ')'),
                (object) array('label' => lang('cc_sale'),  'value' => $this->tec->formatMoney($ccsales->paid ? $ccsales->paid : '0.00') . ' (' . $this->tec->formatMoney($ccsales->total ? $ccsales->total : '0.00') . ')'),
                (object) array('label' => lang('stripe'),  'value' => $this->tec->formatMoney($stripesales->paid ? $stripesales->paid : '0.00') . ' (' . $this->tec->formatMoney($stripesales->total ? $stripesales->total : '0.00') . ')'),
                (object) array('label' => lang('other_sale'),  'value' => $this->tec->formatMoney($other_sales->paid ? $other_sales->paid : '0.00') . ' (' . $this->tec->formatMoney($other_sales->total ? $other_sales->total : '0.00') . ')'),
                (object) array('label' => 'line',  'value' => ''),
                (object) array('label' => lang('total_sales'),  'value' => $this->tec->formatMoney($totalsales->paid ? $totalsales->paid : '0.00') . ' (' . $this->tec->formatMoney($totalsales->total ? $totalsales->total : '0.00') . ')'),
                (object) array('label' => lang('cash_in_hand'),  'value' => $this->tec->formatMoney($register->cash_in_hand)),
                (object) array('label' => lang('expenses'),  'value' => $this->tec->formatMoney($expenses->total ? $expenses->total : '0.00')),
                (object) array('label' => 'line',  'value' => ''),
                (object) array('label' => lang('total_cash'),  'value' => $this->tec->formatMoney($total_cash))
                );

            $data = (object) array(
                'printer' => $this->Settings->local_printers ? '' : json_encode($printer),
                'logo' => !empty($store->logo) ? base_url('uploads/'.$store->logo) : '',
                'heading' => lang('register_details'),
                'info' => $info,
                'totals' => $reg_totals
                );

            // $this->tec->print_arrays($data);
            if ($re == 1) {
                return $data;
            } elseif ($re == 2) {
                echo json_encode($data);
            } else {
                $printer = $this->site->getPrinterByID($this->Settings->printer);
                $this->load->library('escpos');
                $this->escpos->load($printer);
                $this->escpos->print_data($data);
                echo json_encode(true);
            }

        } else {
            echo json_encode(false);
        }
    }

    function print_receipt($id, $open_drawer = false) {

        $sale = $this->pos_model->getSaleByID($id);
        $items = $this->pos_model->getAllSaleItems($id);
        $payments = $this->pos_model->getAllSalePayments($id);
        $store = $this->site->getStoreByID($sale->store_id);
        $created_by = $this->site->getUser($sale->created_by);
        $printer = $this->site->getPrinterByID($this->Settings->printer);
        $this->load->library('escpos');
        $this->escpos->load($printer);
        $this->escpos->print_receipt($store, $sale, $items, $payments, $created_by, $open_drawer);

    }

    function receipt_img() {

        $data = $this->input->post('img', TRUE);
        $filename = date('Y-m-d-H-i-s-').uniqid().'.png';
        $cd = !empty($this->input->post('cd')) ? true : false;
        $imgData = str_replace(' ', '+', $data);
        $imgData = base64_decode($imgData);
        file_put_contents('files/receipts/'.$filename, $imgData);
        $printer = $this->site->getPrinterByID($this->Settings->printer);
        $this->load->library('escpos');
        $this->escpos->load($printer);
        $this->escpos->print_img($filename, $cd);

    }

    function open_drawer() {

        $printer = $this->site->getPrinterByID($this->Settings->printer);
        $this->load->library('escpos');
        $this->escpos->load($printer);
        $this->escpos->open_drawer();

    }

    function p($bo = 'order') {

        $date = date('Y-m-d H:i:s');
        $customer_id = $this->input->post('customer_id');
        $customer_details = $this->pos_model->getCustomerByID($customer_id);
        $customer = $customer_details->name;
        $note = $this->tec->clear_tags($this->input->post('spos_note'));

        $total = 0;
        $product_tax = 0;
        $order_tax = 0;
        $product_discount = 0;
        $order_discount = 0;
        $percentage = '%';
        $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
        for ($r = 0; $r < $i; $r++) {
            $item_id = $_POST['product_id'][$r];
            $real_unit_price = $this->tec->formatDecimal($_POST['real_unit_price'][$r]);
            $item_quantity = $_POST['quantity'][$r];
            $item_comment = $_POST['item_comment'][$r];
            $item_ordered = $_POST['item_was_ordered'][$r];
            $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : '0';

            if (isset($item_id) && isset($real_unit_price) && isset($item_quantity)) {
                $product_details = $this->site->getProductByID($item_id);
                if ($product_details) {
                    $product_name = $product_details->name;
                    $product_code = $product_details->code;
                    $product_cost = $product_details->cost;
                } else {
                    $product_name = $_POST['product_name'][$r];
                    $product_code = $_POST['product_code'][$r];
                    $product_cost = 0;
                }
                if (!$this->Settings->overselling) {
                    if ($product_details->type == 'standard') {
                        if ($product_details->quantity < $item_quantity) {
                            $this->session->set_flashdata('error', lang("quantity_low").' ('.
                                lang('name').': '.$product_details->name.' | '.
                                lang('ordered').': '.$item_quantity.' | '.
                                lang('available').': '.$product_details->quantity.
                                ')');
                            redirect("pos");
                        }
                    } elseif ($product_details->type == 'combo') {
                        $combo_items = $this->pos_model->getComboItemsByPID($product->id);
                        foreach ($combo_items as $combo_item) {
                            $cpr = $this->site->getProductByID($combo_item->id);
                            if ($cpr->quantity < $item_quantity) {
                                $this->session->set_flashdata('error', lang("quantity_low").' ('.
                                    lang('name').': '.$cpr->name.' | '.
                                    lang('ordered').': '.$item_quantity.' x '.$combo_item->qty.' = '.$item_quantity*$combo_item->qty.' | '.
                                    lang('available').': '.$cpr->quantity.
                                    ') '.$product_details->name);
                                redirect("pos");
                            }
                        }
                    }
                }
                $unit_price = $real_unit_price;

                $pr_discount = 0;
                if (isset($item_discount)) {
                    $discount = $item_discount;
                    $dpos = strpos($discount, $percentage);
                    if ($dpos !== false) {
                        $pds = explode("%", $discount);
                        $pr_discount = $this->tec->formatDecimal((($unit_price * (Float)($pds[0])) / 100), 4);
                    } else {
                        $pr_discount = $this->tec->formatDecimal($discount);
                    }
                }
                $unit_price = $this->tec->formatDecimal(($unit_price - $pr_discount), 4);
                $item_net_price = $unit_price;
                $pr_item_discount = $this->tec->formatDecimal(($pr_discount * $item_quantity), 4);
                $product_discount += $pr_item_discount;

                $pr_item_tax = 0; $item_tax = 0; $tax = "";
                if (isset($product_details->tax) && $product_details->tax != 0) {

                    if ($product_details && $product_details->tax_method == 1) {
                        $item_tax = $this->tec->formatDecimal(((($unit_price) * $product_details->tax) / 100), 4);
                        $tax = $product_details->tax . "%";
                    } else {
                        $item_tax = $this->tec->formatDecimal(((($unit_price) * $product_details->tax) / (100 + $product_details->tax)), 4);
                        $tax = $product_details->tax . "%";
                        $item_net_price -= $item_tax;
                    }

                    $pr_item_tax = $this->tec->formatDecimal(($item_tax * $item_quantity), 4);

                }

                $product_tax += $pr_item_tax;
                $subtotal = (($item_net_price * $item_quantity) + $pr_item_tax);

                $products[] = (object) array(
                    'product_id' => $item_id,
                    'quantity' => $item_quantity,
                    'unit_price' => $unit_price,
                    'net_unit_price' => $item_net_price,
                    'discount' => $item_discount,
                    'comment' => $item_comment,
                    'item_discount' => $pr_item_discount,
                    'tax' => $tax,
                    'item_tax' => $pr_item_tax,
                    'subtotal' => $subtotal,
                    'real_unit_price' => $real_unit_price,
                    'cost' => $product_cost,
                    'product_code' => $product_code,
                    'product_name' => $product_name,
                    'ordered' => $item_ordered,
                    );

                $total += $item_net_price * $item_quantity;

            }
        }
        if (empty($products)) {
            $this->form_validation->set_rules('product', lang("order_items"), 'required');
        } else {
            krsort($products);
        }

        if ($this->input->post('order_discount')) {
            $order_discount_id = $this->input->post('order_discount');
            $opos = strpos($order_discount_id, $percentage);
            if ($opos !== false) {
                $ods = explode("%", $order_discount_id);
                $order_discount = $this->tec->formatDecimal(((($total + $product_tax) * (Float)($ods[0])) / 100), 4);
            } else {
                $order_discount = $this->tec->formatDecimal($order_discount_id);
            }
        } else {
            $order_discount_id = NULL;
        }
        $total_discount = $this->tec->formatDecimal(($order_discount + $product_discount), 4);

        if($this->input->post('order_tax')) {
            $order_tax_id = $this->input->post('order_tax');
            $opos = strpos($order_tax_id, $percentage);
            if ($opos !== false) {
                $ots = explode("%", $order_tax_id);
                $order_tax = $this->tec->formatDecimal(((($total + $product_tax - $order_discount) * (Float)($ots[0])) / 100), 4);
            } else {
                $order_tax = $this->tec->formatDecimal($order_tax_id);
            }

        } else {
            $order_tax_id = NULL;
            $order_tax = 0;
        }

        $total_tax = $this->tec->formatDecimal(($product_tax + $order_tax), 4);
        $grand_total = $this->tec->formatDecimal(($this->tec->formatDecimal($total) + $total_tax - $order_discount), 4);
        $paid = 0;
        $round_total = $this->tec->roundNumber($grand_total, $this->Settings->rounding);
        $rounding = $this->tec->formatDecimal(($round_total - $grand_total));

        $data = (object) array('date' => $date,
            'customer_id' => $customer_id,
            'customer_name' => $customer,
            'total' => $this->tec->formatDecimal($total),
            'product_discount' => $this->tec->formatDecimal($product_discount, 4),
            'order_discount_id' => $order_discount_id,
            'order_discount' => $order_discount,
            'total_discount' => $total_discount,
            'product_tax' => $this->tec->formatDecimal($product_tax, 4),
            'order_tax_id' => $order_tax_id,
            'order_tax' => $order_tax,
            'total_tax' => $total_tax,
            'grand_total' => $grand_total,
            'total_items' => $this->input->post('total_items'),
            'total_quantity' => $this->input->post('total_quantity'),
            'rounding' => $rounding,
            'paid' => $paid,
            'created_by' => $this->session->userdata('user_id'),
            'note' => $note,
            'hold_ref' => $this->input->post('hold_ref'),
            );

        // $this->tec->print_arrays($data, $products);
        $store = $this->site->getStoreByID($this->session->userdata('store_id'));
        $created_by = $this->site->getUser($this->session->userdata('user_id'));

        if ($bo == 'bill') {
            $printer = $this->site->getPrinterByID($this->Settings->printer);
            $this->load->library('escpos');
            $this->escpos->load($printer);
            $this->escpos->print_receipt($store, $data, $products, false, $created_by, false, true);
        } else {
            $order_printers = json_decode($this->Settings->order_printers);
            $this->load->library('escpos');
            foreach ($order_printers as $printer_id) {
                $printer = $this->site->getPrinterByID($printer_id);
                $this->escpos->load($printer);
                $this->escpos->print_order($store, $data, $products, $created_by);
            }
        }

    }

}
