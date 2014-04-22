Gerencianet Módulo Opencart
===============

Módulo de integração da Gerencianet com o Opencart




Instalação
===============

1. Baixe o arquivo zip do módulo.
2. Extraia o arquivo zip e substitua as pastas admin e catalog de seu projeto opencart com as pastas do arquivo zip.
3. Vá no painel administrativo e do seu opencart e acesse "Extensões->Formas de pagamento" (Extensions->Payments).
4. Procure pelo módulo da Gerencianet e instale-o.
5. Após a instalação clique em editar.
6. No painel de configuração do módulo da Gerencianet cadastre o token de sua conta.
7. Cadastre também uma url de retorno que é exibida para o clique voltar para sua loja. Por padrão já colocamos a url ideal para um projeto utilizando opencart mas você poderá alterá-la.
8. Em situação altera o status para habilitado.
9. Em ordem coloque a posição nas opções de pagamento que Gerencianet deve ser exibida.
10. Clique em salvar.
11. Após as configurações no opencart basta cadastrar a url de Callback (notificações)




Token de integração Gerencianet
===============

Para conseguir um token de integração de sua conta Gerencianet com sua loja opencart acesse sua conta Gerencianet.

Ao entrar no sistema vá ao menu "Desenvolvedor->Token De Integração" e nesta tela gere seu token de integração caso não possua nenhum.




Callback (Notificações) de pagamento
===============

Para que seu sistema opencart seja avisado dos pagamentos realizados você deve cadastrar uma url de callback do opencart.

Para isso acesse sua conta Gerencianet e vá ao menu "Desenvolvedor->Notificações" e cadastre a seguinte url:

"seu_dominio"/opencart/index.php?route=payment/gerencianet/callback

Troque "seu_dominio" para o dominio de sua loja, por exemplo: "https://lojaopencart.com.br", ficando o exemplo da url de notificação assim:

https://lojaopencart.com.br/opencart/index.php?route=payment/gerencianet/callback

