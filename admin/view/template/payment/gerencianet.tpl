<!--
/**
 * Módulo de Pagamento Gerencianet para OpenCart
 * admin/view/template/payment/gerencianet.tpl
 *
 * NÃO MODIFIQUE OS ARQUIVOS DESTE MÓDULO PARA O BOM FUNCIONAMENTO DO MESMO
 * Em caso de dúvidas entre em contato com a Gerêncianet. Contatos através do site:
 * https://gerencianet.com.br/
 */
-->

<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>

  <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>

  <div class="box">
    <div class="left"></div>
    <div class="right"></div>
    <div class="heading">
      <h1>
        <img src="view/image/gerencianet-icon.gif" alt="Logo Gerencianet" name="Logo Gerencianet" />
        <?php echo $heading_title; ?>
      </h1>

      <div class="buttons">
        <a onclick="$('#form').submit();" class="button">
          <span><?php echo $button_save; ?></span>
        </a>
        <a onclick="location = '<?php echo $cancel; ?>';" class="button">
          <span><?php echo $button_cancel; ?></span>
        </a>
      </div>
    </div>

    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td>
              <span class="required">*</span> <?php echo $entry_token; ?>
            </td>
            <td>
              <input type="text" name="gerencianet_token" value="<?php echo $gerencianet_token; ?>" />
              <br />
              <?php if ($error_token) { ?>
                <span class="error"><?php echo $error_token; ?></span>
              <?php } ?>
              <span style="font-size: 10px;">Caso você não possua um token de integração, você pode gerá-lo através do menu <a href="https://accounts.gerencianet.com.br/login/externo/pagamentos?url=aHR0cHM6Ly9nby5nZXJlbmNpYW5ldC5jb20uYnIvIy9kZXNlbnZvbHZlZG9yL3Rva2Vu" target="_blank">desenvolvedor/token</a> de sua conta Gerencianet.</span>
            </td>
          </tr>
          <tr>
            <td>
              <?php echo $entry_return_url; ?>
            </td>
            <td>
              <input type="text" name="gerencianet_return_url" value="<?php echo $gerencianet_return_url; ?>"/>
            </td>
          </tr>
          <tr>
            <td width="25%">
              <?php echo $entry_status; ?>
            </td>
            <td>
              <select name="gerencianet_status">
                <?php if ($gerencianet_status) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </td>
          </tr>
          <!-- <tr>
            <td>
              <?php echo $entry_sort_order; ?>
            </td>
            <td>
              <input type="text" name="gerencianet_sort_order" value="<?php echo $gerencianet_sort_order; ?>" size="1" />
            </td>
          </tr> -->
<!--           <tr>
            <td>
              <span class="required">*</span> <?php echo $entry_callback_url; ?>
            </td>
            <td>
              <span style="font-size: 10px;">Acesse sua conta Gerencianet e entre no menu <a href="https://accounts.gerencianet.com.br/login/externo/pagamentos?url=aHR0cHM6Ly9nby5nZXJlbmNpYW5ldC5jb20uYnIvIy9kZXNlbnZvbHZlZG9yL25vdGlmaWNhY29lcw==" target="_blank">desenvolvedor/notificacoes</a> e cadastre a seguinte url de notificação:</span>
              <br/>
              <span style="border: 1px dotted gray; margin-top: 5px; width: auto; padding: 5px; display:inline-block; font-size: 10px;"><?php echo $gerencianet_callback_url; ?></span>
            </td>
          </tr> -->
        </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?>