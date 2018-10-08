<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-wirecard-settings" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1>Kredi Kartı ile Ödeme Ayarları</h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="container-fluid">

   
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> Ödeme Entegrasyon Bilgileriniz </h3>
            </div>

            <div class="panel-body">
                <ul class="nav nav-tabs" id="tabs">
                    <li class="active"><a href="#tab-wirecard_settings" data-toggle="tab">Genel Ayarlar</a></li>
                
                    <li><a href="#tab-wirecard_help" data-toggle="tab">Yardım</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-wirecard_settings">
                        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-wirecard-settings" class="form-horizontal">

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="wirecard_publickey"><span data-toggle="tooltip" title="<?php echo $help_total; ?>">Wirecard User Code</span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="wirecard_publickey" value="<?php echo $wirecard_publickey; ?>" placeholder="X1Y2Z3Q4..." id="wirecard_publickey" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="wirecard_privatekey"><span data-toggle="tooltip" title="<?php echo $help_total; ?>">Wirecard Pin</span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="wirecard_privatekey" value="<?php echo $wirecard_privatekey; ?>" placeholder="X1Y2Z3Q4A5B6C7Z8..." id="wirecard_privatekey" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="wirecard_ins_tab"><span data-toggle="tooltip" title="<?php echo $help_total; ?>">Taksitli İşlem</span></label>
                                <div class="col-sm-10">
                                    <select name="wirecard_ins_tab" id="input-wirecard_ins_tab" class="form-control">              
                                        <option value="on">Evet</option>
                                        <option value="off" <?php if ($wirecard_ins_tab == 'off') { ?>selected="selected"<?php } ?>>Hayır</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-order-status">Ödeme Yöntemi</label>
                                <div class="col-sm-10">
                                    <select name="wirecard_3d_mode" id="input-wirecard_3d_mode" class="form-control">              
                                        <option value="shared3d" <?php if ($wirecard_3d_mode == 'shared_3D') { ?>selected="selected"<?php } ?>>Ortak Ödeme Sayfası (3DS li) </option>
                                        <option value="shared" <?php if ($wirecard_3d_mode == 'shared') { ?>selected="selected"<?php } ?>>Ortak Ödeme Sayfası (3DS siz)  </option>
                                        <option value="form" <?php if ($wirecard_3d_mode == 'form') { ?>selected="selected"<?php } ?>>Form ile Ödeme </option>
                                    </select>
                                </div>
                            </div>
                             <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-status">Modül Durumu</label>
                                <div class="col-sm-10">
                                    <select name="wirecard_status" id="input-status" class="form-control">                
                                        <option value="1" selected="selected">Aktif</option>
                                        <option value="0" <?php if (!$wirecard_status) { ?> checked="checked" <?php } ?> >Pasif</option>
                                    </select>
                                </div>
                            </div>
                           <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-status">Sipariş Durumu</label>
                                <div class="col-sm-10">
                                    <select name="wirecard_order_status_id" id="input-order-status" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                        <?php if ($order_status['order_status_id'] == $wirecard_order_status_id) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
								<input type="hidden" name="wirecard_submit" value="1"/>
                    </div>
                
  
                    <div class="tab-pane" id="tab-wirecard_help">
						<div class="panel">
							<div class="row wirecard-header">
								<img src="../catalog/view/theme/default/image/wirecard/wirecard_logo.png" class="col-sm-2 text-center" id="payment-logo">
								<div class="col-sm-6 text-center text-muted">
								Teknik ve diğer sorularınız için yandaki butonlar ile iletişime geçebilirsiniz.
								</div>
								<div class="col-sm-4 text-center">
									<a class="btn btn-primary" href="https://www.wirecard.com.tr/">wirecard.com.tr</a>
									<a class="btn btn-primary" href="https://developer.wirecard.com.tr">Wirecard Developer</a>
								</div>
							</div>

							
						</div>					
					
					
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>
<style>
    #content .tab-pane:first-child .panel {
        border-top-left-radius: 0;
    }

    .wirecard-header .text-branded,
    .wirecard-content .text-branded {
        color: #00aff0;
    }

    .wirecard-header h4,
    .wirecard-content h4,
    .wirecard-content h5 {
        margin: 2px 0;
        color: #00aff0;
        font-size: 1.8em;
    }

    .wirecard-header h4 {
        margin-top: 5px;
    }

    .wirecard-header .col-md-6 {
        margin-top: 18px;
    }

    .wirecard-content h4 {
        margin-bottom: 10px;
    }

    .wirecard-content h5 {
        font-size: 1.4em;
        margin-bottom: 10px;
    }

    .wirecard-content h6 {
        font-size: 1.3em;
        margin: 1px 0 4px 0;
    }

    .wirecard-header > .col-md-4 {
        height: 65px;
        vertical-align: middle;
        border-left: 1px solid #ddd;
    }

    .wirecard-header > .col-md-4:first-child {
        border-left: none;
    }

    .wirecard-header #create-account-btn {
        margin-top: 14px;
    }

    .wirecard-content dd + dt {
        margin-top: 5px;
    }

    .wirecard-content ul {
        padding-left: 15px;
    }

    .wirecard-content .ul-spaced li {
        margin-bottom: 5px;
    }
    table.wirecard_table {
        width:90%;
        margin:auto;
    }
    table.wirecard_table td,th {
        width: 60px;
        margin:0px;
        padding:2px;
    }
    table.wirecard_table input[type="number"] {
        width:50px;
    }
</style>