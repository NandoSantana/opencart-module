<?php
/**
 * Módulo de Pagamento Gerencianet para OpenCart
 * admin/controller/payment/gerencianet.php
 *
 * NÃO MODIFIQUE OS ARQUIVOS DESTE MÓDULO PARA O BOM FUNCIONAMENTO DO MESMO
 * Em caso de dúvidas entre em contato com a Gerêncianet. Contatos através do site:
 * https://gerencianet.com.br/
 */
 
class ControllerPaymentGerencianet extends Controller {
  
    private $error = array(); 
 
    public function index() {
        $this->load->language('payment/gerencianet');
        $this->load->model('setting/setting');
        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_all_zones'] = $this->language->get('text_all_zones');
        $this->data['text_none'] = $this->language->get('text_none');
        $this->data['text_yes'] = $this->language->get('text_yes');
        $this->data['text_no'] = $this->language->get('text_no');
        $this->data['entry_token'] = $this->language->get('entry_token');
        $this->data['entry_return_url'] = $this->language->get('entry_return_url');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $this->data['entry_callback_url'] = $this->language->get('entry_callback_url');
        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');
        $this->data['tab_general'] = $this->language->get('tab_general');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->model_setting_setting->editSetting('gerencianet', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect(HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token']);
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }
                    
        if (isset($this->error['token'])) {
            $this->data['error_token'] = $this->error['token'];
        } else {
            $this->data['error_token'] = '';
        }

        $this->document->breadcrumbs = array();

        $this->document->breadcrumbs[] = array(
            'href'      => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
            'text'      => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $this->document->breadcrumbs[] = array(
            'href'      => HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'],
            'text'      => $this->language->get('text_payment'),
            'separator' => ' :: '
        );

        $this->document->breadcrumbs[] = array(
            'href'      => HTTPS_SERVER . 'index.php?route=payment/gerencianet&token=' . $this->session->data['token'],
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'] = $this->document->breadcrumbs;
                    
        $this->data['action'] = HTTPS_SERVER . 'index.php?route=payment/gerencianet&token=' . $this->session->data['token'];
            
        $this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'];

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $client_url = HTTPS_SERVER;
        } else {
            $client_url = HTTP_SERVER;
        }
        $client_url = str_replace("admin/", "", HTTP_SERVER);
        $this->data['gerencianet_callback_url'] = $client_url."index.php?route=payment/gerencianet/callback";

                
        if (isset($this->request->post['gerencianet_token'])) {
            $this->data['gerencianet_token'] = $this->request->post['gerencianet_token'];
        } else {
            $this->data['gerencianet_token'] = $this->config->get('gerencianet_token');
        }

        if (isset($this->request->post['gerencianet_return_url'])) {
            $this->data['gerencianet_return_url'] = $this->request->post['gerencianet_return_url'];
        } else {
            if($this->config->get('gerencianet_return_url')) {
                $this->data['gerencianet_return_url'] = $this->config->get('gerencianet_return_url');    
            } else {
                $this->data['gerencianet_return_url'] = $client_url."index.php?route=checkout/success";
            }
        }

        $this->load->model('localisation/order_status');

        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['gerencianet_status'])) {
            $this->data['gerencianet_status'] = $this->request->post['gerencianet_status'];
        } else {
            $this->data['gerencianet_status'] = $this->config->get('gerencianet_status');
        }

        // if (isset($this->request->post['gerencianet_sort_order'])) {
        //     $this->data['gerencianet_sort_order'] = $this->request->post['gerencianet_sort_order'];
        // } else {
        //     $this->data['gerencianet_sort_order'] = $this->config->get('gerencianet_sort_order');
        // }
        $this->data['gerencianet_sort_order'] = 1;

        $this->template = 'payment/gerencianet.tpl';
        $this->children = array(
            'common/header',    
            'common/footer' 
        );

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'payment/gerencianet')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['gerencianet_token']) {
            $this->error['token'] = $this->language->get('error_token');
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }   
    }
}