<?php echo $header; ?>
<?php echo $column_left; ?>
<?php echo $column_right; ?>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/wirecard_form.css" />
<script src="catalog/view/javascript/wirecard/jquery.card.js"></script>
<script src="catalog/view/javascript/wirecard/jquery.payment.min.js"></script>


<div class="container"><?php echo $content_top; ?>


<section>
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <h2>Kredi Kartı ile Güvenli Ödeme</h2>
                    Bu sayfada kredi kartı bilgilerinizi girerek veya ortak ödeme sayfasına yönlenerek güvenli ödeme yapabilirsiniz.<br/>
         
        </div>
         
    </div>
    <?php if($error_message) { ?>
		<div class="row">
            <div class="alert alert-danger" id="errDiv">
                Ödemeniz yapılamadı. <br/> 
                <b><?php echo $error_message; ?></b><br/>
                Lütfen formu kontrol edip yeniden deneyiniz.
            </div>
        </div>
    <?php } ?>
    <hr/>
</section>
<form novalidate autocomplete="on" method="POST" id="cc_form" action="<?php echo $form_link ?>">

    <div class="row">
        <div class="col-xs-12 col-sm-6">
               <?php if($mode == 'form') : ?>  
			   <table id="cc_form_table">
			   
			   
                <tr>
                    <td>
                    Kart No <br/>
                <input type="text" id="cc_number" name="cc_number" class="cc_input" placeholder="•••• •••• •••• ••••"/>
                </td>
                <td>
                    Kart son kullanım tarihi<br/>
                <input type="text" id="cc_expiry" name="cc_expiry" class="cc_input" placeholder="AA/YY"/>
                </td>
                </tr>
                <tr>
                    <td>
                    Güvenlik kodu (kartın arka yüzünde)<br/>
                <input type="text" id="cc_cvc" name="cc_cvc" class="cc_input" placeholder="•••"/>
                </td>
                <td> Kart üzerindeki isim<br/>
                <input type="text" id="cc_name" name="cc_name" class="cc_input" placeholder="Ad Soyad"/>
                </td>
                </tr>
			
					 <?php if($isInstallment == 'on') : ?>
                    <tr>
                                <td >
                                Taksit Sayısı<br/>
                                <select name="wirecard-installment-count">
                                <option value="0">Peşin Ödeme</option>
                                <option value="3">3 Taksit</option>
                                <option value="6">6 Taksit</option>
                                <option value="9">9 Taksit</option>         
                                </select>
                            </td>
                            </tr>
                    <?php endif; ?>
					
              
            
            </table>
			
         

        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="card-wrapper"></div>
        </div>	   
		   <hr/>  
	<?php endif; ?>
             <input type="hidden" name="cc_form_key" value="<?php echo $cc_form_key; ?>"/>
            <button type="submit" id="cc_form_submit" class="btn btn-lg btn-primary">Ödemeyi Tamamla</button>
    </div>
	
    <div class="row">
       
    </div>
</form> 


<script>
    $('form#cc_form').card({
        // a selector or DOM element for the form where users will
        // be entering their information
        form: 'form#cc_form', // *required*
        // a selector or DOM element for the container
        // where you want the card to appear
		formSelectors: {
			numberInput: 'input#cc_number', // optional — default input[name="number"]
			expiryInput: 'input#cc_expiry', // optional — default input[name="expiry"]
			cvcInput: 'input#cc_cvc', // optional — default input[name="cvc"]
			nameInput: 'input#cc_name' // optional - defaults input[name="name"]
		},
		placeholders: {
		  number: '&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;',
		  cvc: '&bull;&bull;&bull;',
		  expiry: 'AA/YY',
		  name: 'AD SOYAD'
		},
		messages: {
            monthYear: 'mm/yy' // optional - default 'month/year'
        },
        container: '.card-wrapper', // *required*
        width: "100%",
        formatting: true, // optional - default true
        // Default placeholders for rendered fields - optional
        // if true, will log helpful messages for setting up Card
        debug: true // optional - default false
    });

	$('table#cc_table tr').click(function() {
		$(this).find('td input:radio').prop('checked', true);
	})

    jQuery(function ($) {
        $('table#cc_form_table').removeClass('error success');
        $('input#cc_number').payment('formatCardNumber');
        $('input#cc_expiry').payment('formatCardExpiry');
        $('input#cc_cvc').payment('formatCardCVC');
        $("#cc_form_submit").attr("disabled", true);

        $('.cc_input').bind('keypress keyup keydown focus', function (e) {
            $(this).removeClass('error');
            $("#cc_form_submit").attr("disabled", true);
            var hasError = false;
            var cardType = $.payment.cardType($('input#cc_number').val());


            if (!$.payment.validateCardNumber($('input#cc_number').val())) {
                $('input#cc_number').addClass('error');
                hasError = 'number';
            }
            if (!$.payment.validateCardExpiry($('input#cc_expiry').payment('cardExpiryVal'))) {
                $('input#cc_expiry').addClass('error');
                hasError = 'expiry';
            }
            if (!$.payment.validateCardCVC($('input#cc_cvc').val(), cardType)) {
                $('input#cc_cvc').addClass('error');
                hasError = 'cvc';
            }
            if ($('input#cc_name').val().length < 3) {
                $('input#cc_name').addClass('error');
                hasError = 'name';
            }

            if (hasError === false) {
//                console.log(hasError);
                $("#cc_form_submit").removeAttr("disabled");
                $("#cc_validation").hide();
            }
            else {
                $("#cc_validation").show();
                $("#cc_form_submit").attr("disabled", true);
                $('table#cc_form_table').addClass('error');
            }
        });
		$('.cc_input').keypress();
    });
</script>
<?php echo $footer; ?>