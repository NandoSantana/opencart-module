<?php
/**
 * Módulo de Pagamento Gerencianet para OpenCart
 * catalog/controller/payment/gerencianet.php
 * NÃO MODIFIQUE OS ARQUIVOS DESTE MÓDULO PARA O BOM FUNCIONAMENTO DO MESMO
 * Em caso de dúvidas entre em contato com a Gerêncianet. Contatos através do site:
 * http://www.gerencianet.com.br/
 */

class ControllerPaymentGerencianet extends Controller
{

    const CHARGE_GENERATION_URL = 'https://go.gerencianet.com.br/api/pagamento/xml';
    const CHARGE_HISTORY_URL = 'https://go.gerencianet.com.br/api/notificacao/xml';

    private $xml_gerencianet;

    protected function index() {
        $this->load->model('checkout/order');

        $this->data['action'] = $this->config->get('config_url') . 'index.php?route=payment/gerencianet/charge';

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/gerencianet.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/gerencianet.tpl';
        } else {
            $this->template = 'default/template/payment/gerencianet.tpl';
        }

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $_SESSION['gerencianet_xml'] = $this->generateChargeXML($order_info);

        $this->render();
    }

    protected function makeRequest($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('cURL error: ' . curl_errno($ch) . ' - ' . curl_error($ch));
        }

        return $result;
    }

    public function generateChargeXML($order_info) {
        require_once dirname(__FILE__) . '/lib-gerencianet/simple_xml_gerencianet.php';
        $xml = new SimpleXMLGerencianet('<?xml version="1.0" encoding="utf-8"?><integracao></integracao>');



        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $client_url = HTTPS_SERVER;
        } else {
            $client_url = HTTP_SERVER;
        }
        $client_url = str_replace("admin/", "", HTTP_SERVER);
        $gerencianet_callback_url = $client_url."index.php?route=payment/gerencianet/callback";


        //tag cliente
        $cliente = $xml->addChild('cliente');
        if($order_info['email']) {
            $cliente->addChild('email')->addCData($order_info['email']);
        }
        if($order_info['payment_firstname'] && $order_info['payment_lastname']) {
            $cliente->addChild('nome')->addCData($order_info['payment_firstname']." ".$order_info['payment_lastname']);
        }
        if($order_info['payment_address_2']) {
            $cliente->addChild('logradouro')->addCData($order_info['payment_address_1']);
        }   
        if($order_info['payment_address_2']) {
            $cliente->addChild('bairro')->addCData($order_info['payment_address_2']);
        }
        if($order_info['payment_zone_code']) {
            $cliente->addChild('estado', $order_info['payment_zone_code']);
        }
        if($order_info['payment_city']) {
            $cliente->addChild('cidade')->addCData($order_info['payment_city']);
        }
        if($order_info['payment_postcode']) {
            $cep = preg_replace("/[()-.\ ]/", "", $order_info['payment_postcode']);
            $cliente->addChild('cep', $cep);
        }
        if($order_info['telephone']) {
            $celular = $order_info['telephone'];
            $celular = preg_replace("/[()-.\ ]/", "", $celular);
            $celular = ltrim($celular, '0');
            $cliente->addChild('celular', $celular);
        }

        //tag retorno
        $retorno = $xml->addChild('retorno');
        $retorno->addChild('identificador', 'opencart_' . $order_info['order_id']);
        $retorno->addChild('urlNotificacao', $gerencianet_callback_url);
        if($this->config->get('gerencianet_return_url')) {
            $retorno->addChild('url')->addCData($this->config->get('gerencianet_return_url'));
        }
        
        //tag itens
        $itens = $xml->addChild('itens');
        $produtos = array();
        foreach ($this->cart->getProducts() as $product) {
            $item = $itens->addChild('item');

            $itemValor = $this->currency->format($product['price'], $this->_order_info['currency_code'], false, false);
            $itemValor = (int)($itemValor * 100);
            $item->addChild('itemValor', $itemValor);
            $item->addChild('itemDescricao')->addCData($product['name']);
            $item->addChild('itemQuantidade', $product['quantity']);

            $produtos[] = array(
                'preco'      => $itemValor,
                'quantidade' => $product['quantity']
            );
        }

        //frete
        $valorFrete = 0;
        if (isset($this->session->data['coupon'])) {
            $this->load->model('checkout/coupon');
            $coupon = $this->model_checkout_coupon->getCoupon($this->session->data['coupon']);
            if (isset($coupon)) {
                if ($coupon['shipping'] == 1) {
                    $valorFrete = '0.00';
                } else {
                    $valorFrete = $this->session->data['shipping_method']['cost'];
                }
            }
        } else {
            $valorFrete = $this->session->data['shipping_method']['cost'];
        }
        if($valorFrete > 0) {
            $valorFreteFormatado = $this->currency->format($valorFrete, $this->_order_info['currency_code'], false, false);
            $valorFrete = (int)($valorFreteFormatado * 100);
            $xml->addChild('frete', $valorFrete);
        }

        //desconto
        $itensMaisFrete = 0;
        foreach ($produtos as $produto) {
            $itensMaisFrete += $produto['preco'] * $produto['quantidade'];
        }

        $itensMaisFrete += $valorFrete;
        $totalFormatado = $this->currency->format($order_info['total'], $this->_order_info['currency_code'], false, false);
        $total = (int)($totalFormatado * 100);

        if ($itensMaisFrete > $total) {
            $desconto = $itensMaisFrete - $total;
            $xml->addChild('desconto', $desconto);
        }


        return $xml->asXML();
    }

    public function generateHistoryXML($notification_token) {
        require_once dirname(__FILE__) . '/lib-gerencianet/simple_xml_gerencianet.php';
        $xml = new SimpleXMLGerencianet('<?xml version="1.0" encoding="utf-8"?><integracao></integracao>');
        $xml->addChild('notificacao', $notification_token);
        return $xml->asXML();
    }

    public function charge() {
        if (isset($_SESSION['gerencianet_xml'])) {
            $token = $this->config->get('gerencianet_token');
            $dados = $_SESSION['gerencianet_xml'];

            $resposta = $this->makeRequest(self::CHARGE_GENERATION_URL, array('token' => $token, 'dados' => $dados));
            //$resposta = preg_replace("/[\n]/", "", $resposta);
            $resposta = trim($resposta);

            $result   = simplexml_load_string($resposta);

            if ($result->status == 2) {
                $url = $result->resposta->link;
                $this->load->model('checkout/order');
                $this->model_checkout_order->update($this->session->data['order_id'], (int)1, '', true);
                $this->model_checkout_order->confirm($this->session->data['order_id'], (int)1, '', true);
                $this->clearCart();
                return $this->abrirURL($url);
            } else {
                return $this->abrirURL($this->config->get('config_url').'index.php?route=checkout/checkout');
            }
        } else {
            return $this->abrirURL($this->config->get('config_url').'index.php?route=checkout/checkout');
        }
    }

    private function clearCart() {
        if (isset($this->session->data['order_id'])) {
            $this->cart->clear();
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['guest']);
            unset($this->session->data['comment']);
            unset($this->session->data['order_id']);    
            unset($this->session->data['coupon']);
            unset($this->session->data['reward']);
            unset($this->session->data['voucher']);
            unset($this->session->data['vouchers']);
        }   
    }

    public function abrirURL($url) {
        $html = '<script type="text/javascript">';
        $html .= 'setTimeout(function() { window.location.href = "' . $url . '"; }, 0);';
        $html .= '</script>';
        echo $html;
    }

    public function callback() {
        $token = $this->config->get('gerencianet_token');
        $dados = $this->generateHistoryXML($_POST['notificacao']);
        $resposta = $this->makeRequest(self::CHARGE_HISTORY_URL, array('token' => $token, 'dados' => $dados));
        $resposta = trim($resposta);
        $result   = simplexml_load_string($resposta);
        if ($result->status == 2) {
            $status = $result->resposta->status;
            $order_status_id = null;
            switch($status) {
                case 'visualizado':
                    $order_status_id =(int)1;
                break;
                case 'selecionado':
                    $order_status_id =(int)2;
                break;
                case 'pago':
                    $order_status_id =(int)5;
                break;
                case 'recusado':
                    $order_status_id =(int)10;
                break;
                case 'cancelado':
                    $order_status_id =(int)7;
                break;
                case 'vencido':
                    $order_status_id =(int)14;
                break;
                case 'vencido':
                    $order_status_id =(int)14;
                break;
                case 'contestado':
                    $order_status_id =(int)13;
                break;
                case 'devolvido':
                    $order_status_id =(int)1;
                break;
                contestado
            }
            if($order_status_id != null) {
                $identificador = $result->resposta->identificador;
                $identificador = preg_replace("/[opencart_]/", "", $identificador);

                $this->load->model('checkout/order');
                $this->model_checkout_order->update($identificador, $order_status_id, '', true);
                $this->model_checkout_order->confirm($identificador, $order_status_id, '', true);
            }
        } 
    }

}