<?php
ini_set("display_errors", "on");

class ControllerExtensionPaymentwirecard extends Controller {
	private $error = array();
	
	public function install () {
		$this->load->model('extension/payment/wirecard');

		$this->model_extension_payment_wirecard->install();
	}
	

	public function index() {
		$this->load->language('payment/wirecard');
		
		$this->document->setTitle('Kredi Kartı İle Ödeme');

		$this->load->model('setting/setting');
		$a;

		if (isset($this->request->post['wirecard_submit'])) {
			$this->model_setting_setting->editSetting('wirecard', $this->request->post);			
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/payment/wirecard', 'token=' . $this->session->data['token'] . '&type=payment', true));
		}
		
		if (isset($this->request->post['confirm_wirecard_register'])) {
		
			$this->response->redirect($this->url->link('extension/payment/wirecard', 'token=' . $this->session->data['token'] . '&type=payment', true));
		}


		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['help_total'] = $this->language->get('help_total');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
	
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		if($this->config->get('wirecard_publickey') == null)
			$data['error_warning'] .= 'wirecard User Code Boş<br/>';
			
		if($this->config->get('wirecard_privatekey') == null)
			$data['error_warning'] .= 'wirecard Pin Boş<br/>';
		
		

		
		
		if (isset($this->request->post['wirecard_3d_mode'])) {
			$data['wirecard_3d_mode'] = $this->request->post['wirecard_3d_mode'];
		} else {
			$data['wirecard_3d_mode'] = $this->config->get('wirecard_3d_mode');
		}
		
		if (isset($this->request->post['wirecard_publickey'])) {
			$data['wirecard_publickey'] = $this->request->post['wirecard_publickey'];
		} else {
			$data['wirecard_publickey'] = $this->config->get('wirecard_publickey');
		}

		if (isset($this->request->post['wirecard_ins_tab'])) {
			$data['wirecard_ins_tab'] = $this->request->post['wirecard_ins_tab'];
		} else {
			$data['wirecard_ins_tab'] = $this->config->get('wirecard_ins_tab');
		}
		
		if (isset($this->request->post['wirecard_privatekey'])) {
			$data['wirecard_privatekey'] = $this->request->post['wirecard_privatekey'];
		} else {
			$data['wirecard_privatekey'] = $this->config->get('wirecard_privatekey');
		}
		if (isset($this->request->post['wirecard_status'])) {
			$data['wirecard_status'] = $this->request->post['wirecard_status'];
		} else {
			$data['wirecard_status'] = $this->config->get('wirecard_status');
		}
		if (isset($this->request->post['wirecard_order_status_id'])) {
			$data['wirecard_order_status_id'] = $this->request->post['wirecard_order_status_id'];
		} else {
			$data['wirecard_order_status_id'] = $this->config->get('wirecard_order_status_id');
		}
		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/wirecard', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['action'] = $this->url->link('extension/payment/wirecard', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		$this->response->setOutput($this->load->view('payment/wirecard.tpl', $data));
	}
	
	
	private function curlPostExt($data, $url){
		$ch = curl_init();    // initialize curl handle
		curl_setopt($ch, CURLOPT_URL,$url); // set url to post to
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); // times out after 4s
		curl_setopt($ch, CURLOPT_POST, 1); // set POST method
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // add POST fields
		if($result = curl_exec($ch)) { // run the whole process
			curl_close($ch); 
			return $result;
		}
	}

	protected function validate() {
		
		if (!$this->user->hasPermission('modify', 'payment/wirecard')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		return true;
		
		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();

		foreach ($languages as $language) {
			if (empty($this->request->post['wirecard_bank' . $language['language_id']])) {
				$this->error['bank' .  $language['language_id']] = $this->language->get('error_bank');
			}
		}

		return !$this->error;
	}
}