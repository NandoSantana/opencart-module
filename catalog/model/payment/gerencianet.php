<?php 
/**
 * Módulo de Pagamento Gerencianet para OpenCart
 * catalog/model/payment/gerencianet.php
 *
 * NÃO MODIFIQUE OS ARQUIVOS DESTE MÓDULO PARA O BOM FUNCIONAMENTO DO MESMO
 * Em caso de dúvidas entre em contato com a Gerencianet. Contatos através do site:
 * http://www.gerencianet.com.br/
 */

class ModelPaymentGerencianet extends Model {
  	public function getMethod($address) {
		$this->load->language('payment/gerencianet');
		
		if ($this->config->get('gerencianet_status')) 
		{
			$status = TRUE;
		}
		
		$method_data = array();
	
		if ($status) 
		{  
      		$method_data = array( 
        		'code'         => 'gerencianet',
        		'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('gerencianet_sort_order')
      		);
    	}
   
    	return $method_data;
  	}
}
?>