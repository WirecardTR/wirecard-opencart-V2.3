<?php
ini_set("display_errors", 'on');
class ControllerExtensionPaymentwirecard extends Controller
{

	public function index()
	{
		$this->load->language('payment/wirecard');
		$this->document->addStyle('catalog/view/theme/default/stylesheet/wirecard_form.css');

		$data['text_instruction'] = $this->language->get('text_instruction');
		$data['text_description'] = $this->language->get('text_description');
		$data['text_payment'] = $this->language->get('text_payment');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['bank'] = nl2br($this->config->get('wirecard_bank' . $this->config->get('config_language_id')));

		$data['continue'] = $this->url->link('checkout/success');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/wirecard.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/wirecard.tpl', $data);
		} else {
			return $this->load->view('payment/wirecard.tpl', $data);
		}
	}

	
}
