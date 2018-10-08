<?php

class ControllerPaymentwirecard extends Controller
{

	public function index()
	{
		$this->load->language('payment/wirecard');
		$this->document->addStyle('catalog/view/theme/default/stylesheet/wirecard_form.css');
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['text_instruction'] = $this->language->get('text_instruction');
		$data['text_description'] = $this->language->get('text_description');
		$data['text_payment'] = $this->language->get('text_payment');
		$data['text_loading'] = $this->language->get('text_loading');

	//	$data['bank'] = nl2br($this->config->get('wirecard_bank' . $this->config->get('config_language_id')));

		$data['continue'] = $this->url->link('checkout/success');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/wirecard.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/wirecard.tpl', $data);
		} else {
			return $this->load->view('/payment/wirecard.tpl', $data);
		}
	}

	public function paymentform()
	{

		$this->load->model('checkout/order');
		$this->load->model('setting/setting');	
		require_once(DIR_APPLICATION . 'controller/payment/includes/restHttpCaller.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/CCProxySaleRequest.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/WDTicketPaymentFormRequest.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/BaseModel.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/helper.php');
		
		if(!isset($this->session->data['order_id']) OR !$this->session->data['order_id'])
			die('Sipariş ID bulunamadı');
		$orderid =$this->session->data['order_id'];
		
		$order_info = $this->model_checkout_order->getOrder($orderid);
		
		
		$error_message = false;
		$cc_form_key = md5($order_info['order_id'] . $order_info['store_url']);
		$isInstallment = $this->config->get('wirecard_ins_tab');
		$total_cart = $order_info['total'];

			if(isset($this->request->post['cc_form_key']) AND $this->request->post['cc_form_key'] == $cc_form_key AND !isset( $this->request->post['MPAY'])) { //form ile direk ödeme
			
				$record = $this->pay($orderid);
				if($record["shared_payment_url"] != 'null') // Ortak ödemeye yönlen 
				{	
					$this->saveRecord($record);
					header('Location: '.$record["shared_payment_url"]);
					exit;
				}
				$this->saveRecord($record);
				if($record['status_code'] == 0 ) {//Başarılı işlem
			
					$this->session->data['payment_method']['code'] = 'wirecard';
						$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('wirecard_order_status_id'), "");
						$this->response->redirect($this->url->link('checkout/success', '', 'ssl'));
						$this->saveRecord($record);
				}
				else { //Başarısız işlem
				
					$error_message = 'Ödeme başarısız oldu: Bankanızın cevabı: ('. $record['result_code'] . ') ' . $record['result_message'];
				}				
			}	
		    elseif (isset($this->request->post['MPAY'])) { //Ortak ödemeden gelirse. 
			
				$record = $this->getRecordByOrderId($this->request->post['MPAY']);
				$record['status_code'] = $this->request->post['StatusCode'];
				$record['result_code'] = $this->request->post['ResultCode'];
				$record['result_message'] =$this->request->post['ResultMessage'];		
				$this->saveRecord($record);	
					if($record['status_code'] == 0 ) {//Başarılı işlem
						$this->session->data['payment_method']['code'] = 'wirecard';
						$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('wirecard_order_status_id'), "");
						$this->response->redirect($this->url->link('checkout/success', '', 'ssl'));
						$this->saveRecord($record);
		
				}
				else { //Başarısız işlem7
				
				
					$error_message = 'Ödeme başarısız oldu: Kart Bankası İşlem Cevabı: ('. $record['result_code'] . ') ' . $record['result_message'];
				}
			}
		
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');


		$data['isInstallment'] = $isInstallment;
		$data['cc_form_key'] = $cc_form_key;
		$data['error_message'] = $error_message;
		$data['mode'] = $this->config->get('wirecard_3d_mode');
		$data['cart_id'] = $this->session->data['order_id'];
		$data['form_link'] = $this->url->link('payment/wirecard/paymentform', '', 'SSL');


		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/wirecard_ccform.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/wirecard_ccform.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('/payment/wirecard_ccform.tpl', $data));
		}
	}


	function pay()
	{
		$this->load->model('checkout/order');
		require_once(DIR_APPLICATION . 'controller/payment/includes/restHttpCaller.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/CCProxySaleRequest.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/WDTicketPaymentFormRequest.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/BaseModel.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/helper.php');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$prices =$order_info['total'];

		$record = array(
			'result_code' => '0',
			'result_message' => '',
			'result' => false
		);

	
		$amount = (float) $order_info['total'];
		$order_id = $this->session->data['order_id'];
		$wirecard_usercode  = $this->config->get('wirecard_publickey');
		$wirecard_pin  = $this->config->get('wirecard_privatekey');
		$mode = $this->config->get('wirecard_3d_mode');
		$installment = isset($this->request->post['wirecard-installment-count']) ? (int) $this->request->post['wirecard-installment-count']:0; 
		$user_id=$this->session->data['user_id'];

		$expire_date = isset($this->request->post['cc_expiry']) ? explode('/', $this->request->post['cc_expiry']):00;

	
			$record = array(
					'order_id' => $order_id,
					'customer_id' => $user_id,
					'wirecard_id' => $order_id,
					'amount' => $amount,
					'amount_paid' => $amount,
					'installment' => $installment,
					'cardholdername' =>   isset($this->request->post['cc_name'])?$this->request->post['cc_name']:'',
					'cardexpdate' => str_replace(' ', '', $expire_date[0]) . str_replace(' ', '', $expire_date[1]),
					'cardnumber' => isset($this->request->post['cc_number']) ? substr($this->request->post['cc_number'], 0, 6) . 'XXXXXXXX' . substr($this->request->post['cc_number'], -2):'',
					'createddate' =>date("Y-m-d h:i:s"), 
					'ipaddress' =>  helper::get_client_ip(),
					'status_code' => 1, //default başarısız
					'result_code' => '', 
					'result_message' => '',
					'mode' =>  $mode,
					'shared_payment_url' => 'null'
				);
		

		
				
			if ($mode == 'form')
			{
				$request = new CCProxySaleRequest();
				$request->ServiceType = "CCProxy";
				$request->OperationType = "Sale";
				$request->Token= new Token();
				$request->Token->UserCode=$wirecard_usercode;
				$request->Token->Pin=$wirecard_pin;
				$request->MPAY = $order_id;
				$request->IPAddress = helper::get_client_ip();  
				$request->PaymentContent = "Odeme"; //Ürünisimleri
				$request->InstallmentCount =  is_null($this->request->post['wirecard-installment-count']) ? 0 :  $installment;
				$request->Description = "";
				$request->ExtraParam = "";
				$request->CreditCardInfo= new CreditCardInfo();
				$request->CreditCardInfo->CreditCardNo= str_replace(' ', '', $this->request->post['cc_number']);
				$request->CreditCardInfo->OwnerName= $this->request->post['cc_name'];
				$request->CreditCardInfo->ExpireYear=str_replace(' ', '', $expire_date[1]);
				$request->CreditCardInfo->ExpireMonth = str_replace(' ', '', $expire_date[0]);
				$request->CreditCardInfo->Cvv=$this->request->post['cc_cvc'];
				$request->CreditCardInfo->Price=$amount * 100;  // 1 TL için 100 Gönderilmeli.
			
				$record['shared_payment_url']='null';
				try {
				
					$response = CCProxySaleRequest::execute($request); 
					
				} catch (Exception $e) {
					$record['result_code'] = 'ERROR';
					$record['result_message'] = $e->getMessage();
					$record['status_code'] = 1;
					return $record;
				}
				$sxml = new SimpleXMLElement( $response);
				$record['status_code'] = $sxml->Item[0]['Value'];
				$record['result_code'] = $sxml->Item[1]['Value'];
				$record['result_message'] = helper::turkishreplace( $sxml->Item[2]['Value']);
				$record['wirecard_id'] =   $sxml->Item[3]['Value'];
				$record['cardnumber'] =    $sxml->Item[5]['Value'];
				$record['amount_paid'] =  $response['StatusCode'] == "0" ? $amount : 0;
				$error_message = false;
	
				return $record;
			}
			elseif ($mode =='shared3d') //shared 3d ortak ödeme sayfası 3d 
			{
 			
				$request = new WDTicketPaymentFormRequest();
				$request->ServiceType = "WDTicket";
				$request->OperationType = "Sale3DSURLProxy";
				$request->Token= new Token();
			$request->Token->UserCode=$wirecard_usercode;
				$request->Token->Pin=$wirecard_pin;
				$request->MPAY = $order_id;
				$request->PaymentContent = "Odeme"; //Ürünisimleri
				$request->PaymentTypeId = "1";
				$request->Description = "";
				$request->ExtraParam = "";
				$request->ErrorURL =  $this->url->link('payment/wirecard/paymentform', 'tdvalidate=1', 'SSL');
				$request->SuccessURL = $this->url->link('payment/wirecard/paymentform', 'tdvalidate=1', 'SSL');
				$request->Price = $amount * 100;  // 1 TL için 100 Gönderilmeli.
				try {
				
					$response = WDTicketPaymentFormRequest::Execute($request); 
				
				} catch (Exception $e) {
			
					$record['result_code'] = 'ERROR';
					$record['result_message'] = $e->getMessage();
					$record['status_code'] = 1;
					return $record;
				}
	
				$sxml = new SimpleXMLElement( $response);
			
				$record['status_code'] = $sxml->Item[0]['Value'];
				$record['result_code'] = $sxml->Item[1]['Value'];
				$record['result_message'] = helper::turkishreplace( $sxml->Item[2]['Value']);
				$record['shared_payment_url'] =$sxml->Item[3]['Value'];
				return $record;
			}
			else 
			{ //shared
				$request = new WDTicketPaymentFormRequest();
				$request->ServiceType = "WDTicket";
				$request->OperationType = "SaleURLProxy";
				$request->Token= new Token();
		      	$request->Token->UserCode=$wirecard_usercode;
				$request->Token->Pin=$wirecard_pin;
				$request->MPAY = $order_id;
				$request->PaymentContent = "Odeme"; //Ürünisimleri
				$request->PaymentTypeId = "1";
				$request->Description = "";
				$request->ExtraParam = "";
				$request->ErrorURL =$this->url->link('payment/wirecard/paymentform', 'tdvalidate=1', 'SSL');
				$request->SuccessURL = $this->url->link('payment/wirecard/paymentform', 'tdvalidate=1', 'SSL');
				$request->Price = $amount * 100;  // 1 TL için 100 Gönderilmeli.
	
				try {
				
					$response = WDTicketPaymentFormRequest::Execute($request); 
				
				} catch (Exception $e) {
			
					$record['result_code'] = 'ERROR';
					$record['result_message'] = $e->getMessage();
					$record['status_code'] = 1;
					return $record;
				}
	
				$sxml = new SimpleXMLElement( $response);
			
				$record['status_code'] = $sxml->Item[0]['Value'];
				$record['result_code'] = $sxml->Item[1]['Value'];
				$record['result_message'] = helper::turkishreplace( $sxml->Item[2]['Value']);
				$record['shared_payment_url'] =$sxml->Item[3]['Value'];
				
				return $record;
			}	

	}


	private function addRecord($record)
	{
		return $this->db->query($this->insertRowQuery('wirecard_payment', $record));
	}

	private function updateRecordByOrderId($record)
	{
		return $this->db->query($this->updateRowQuery('wirecard_payment', $record, array('order_id' => (int) $record['order_id'])));
	}

	
	public function saveRecord($record)
	{
		$record['createddate'] = date("Y-m-d h:i:s");
		if (isset($record['order_id'])
				AND $record['order_id']
				AND $this->getRecordByOrderId($record['order_id']))
			return $this->updateRecordByOrderId($record);

			return $this->addRecord($record);
	}

	public function getRecordByOrderId($order_id)
	{
		$row = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'wirecard_payment` '
				. 'WHERE `order_id` = ' . (int) $order_id);
		return $row->num_rows == 0 ? false : $row->row;
	}

	
	private function updateRowQuery($table, $array, $where, $what = null, $deb = false)
	{
		$q = "UPDATE `" . DB_PREFIX . "$table` SET ";
		$i = count($array);
		foreach ($array as $k => $v) {
			$q .= '`' . $k . '` = ' . "'" . $this->escape($v) . "'";
			$i--;
			if ($i > 0)
				$q .=" ,\n";
		}
		$q .= ' WHERE ';
		if (is_array($where)) {
			$i = count($where);
			foreach ($where as $k => $v) {
				$i--;
				$q .= '`' . $k . '` = \'' . $this->escape($v) . '\' ';
				if ($i != 0)
					$q .= ' AND ';
			}
		} else
			$q .= "`$where` = '" . $this->escape($what) . "' LIMIT 1";
		if ($deb)
			echo $q;
		return $q;
	}

	private function insertRowQuery($table, $array, $deb = false)
	{
		$f = '';
		$d = '';
		$q = "INSERT INTO `" . DB_PREFIX . "$table` ( ";
		$i = count($array);
		foreach ($array as $k => $v) {
			if (is_array($v))
				print_r($v);
			$f .= "`" . $k . "`";
			$d .= "'" . $this->escape($v) . "'";
			$i--;
			if ($i > 0) {
				$f .=", ";
				$d .=", ";
			}
		}
		$q .= $f . ') VALUES (' . $this->escape($d) . ' )';
		if ($deb)
			echo $q;
		return $q;
	}

	private function escape($var)
	{
		return $var;
	}
}
