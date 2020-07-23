@extends('dashboard.layout')

@section('css')
<link href="{{ asset('public/plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h4>
            {{ trans('admin.admin') }}
            	<i class="fa fa-angle-right margin-separator"></i>
            		{{ trans('misc.add_funds') }}
          </h4>

        </section>

        <!-- Main content -->
        <section class="content">

          <div class="alert alert-danger display-none" id="error">
              <ul class="list-unstyled" id="showErrors"></ul>
            </div>

        	<div class="content">
            <div class="row">
              <div class="box box-danger">

                <!-- form start -->
                <form class="form-horizontal padding-top-20" method="post" action="{{url('user/dashboard/add/funds')}}" id="formAddFunds">

                	<input type="hidden" name="_token" value="{{ csrf_token() }}">

                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('misc.amount') }}</label>
                      <div class="col-sm-10">
                        <div class="input-group">
                        <div class="input-group-addon">
                          {{ $settings->currency_symbol }}
                        </div>
                        <input type="number" min="{{ $settings->min_deposits_amount }}" max="{{ $settings->max_deposits_amount }}" autocomplete="off" value="" name="amount" class="form-control onlyNumber" placeholder="{{ trans('misc.amount') }}">
                      </div>
                      <p class="help-block margin-bottom-zero">
                        + {{ $settings->currency_position == 'left' ? $settings->currency_symbol : null }}<span id="handlingFee">0</span>{{ $settings->currency_position == 'right' ? $settings->currency_symbol : null }} {{ trans('misc.handling_fee') }}

                        <strong>{{ trans('misc.total') }}:</strong> {{ $settings->currency_position == 'left' ? $settings->currency_symbol : null }}<span id="total">0</span>{{ $settings->currency_position == 'right' ? $settings->currency_symbol : null }}
                      </p>
                      </div>
                    </div>
                  </div><!-- /.box-body -->

                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('misc.payment_gateway') }}</label>
                      <div class="col-sm-10">
                        <div class="input-group">
                        <div class="input-group-addon" id="imgPayment">
                          @if($settings->enable_paypal == '1')
                            <img src="{{url('public/img/paypal.png')}}" />
                          @else
                            <img src="{{url('public/img/cards.png')}}" />
                            @endif
                        </div>
                      	<select name="payment_gateway" class="form-control" id="paymentGateway">
                          @if($settings->enable_paypal == '1' && $settings->paypal_account != '')
                            <option value="paypal">PayPal</option>
                          @endif

                          @if($settings->enable_stripe == '1' && $settings->stripe_secret_key != '' && $settings->stripe_secret_key != '')
                            <option value="stripe">{{trans('misc.debit_credit_card')}}</option>
                          @endif
                        </select>
                      </div>
                    </div>
                    </div>
                  </div><!-- /.box-body -->

                  <div class="box-footer">
                    <a href="{{url('user/dashboard/deposits')}}" class="btn btn-default"><i class="fa fa-long-arrow-left"></i> {{ trans('auth.back') }}</a>
                    <button type="submit" class="btn btn-success pull-right spin-btn" id="addFundsBtn">
                      {{ trans('misc.add_funds') }} <span></span>
                    </button>
                  </div><!-- /.box-footer -->
                </form>

        		</div><!-- /.row -->

        	</div><!-- /.content -->

          <!-- Your Page Content Here -->

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
@endsection

@section('javascript')

	<!-- icheck -->
	<script src="{{ asset('public/plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/js/jquery.form.js') }}"></script>
  <script src="https://checkout.stripe.com/checkout.js"></script>

	<script type="text/javascript">
  $(document).ready(function() {

    //<---------------- Add Funds ----------->>>>
			$(document).on('click','#addFundsBtn',function(s){

				s.preventDefault();
				var element = $(this);
				element.attr({'disabled' : 'true'});

				(function(){
					 $("#formAddFunds").ajaxForm({
					 dataType : 'json',
					 success:  function(result){

					 //===== SUCCESS =====//
					 if( result.success != false && result.formPP ){

					 	$( result.formPP ).appendTo( "body" );
					 	$("#form_pp").submit();

						}//<-- e

						else if( result.success != false && result.stripeTrue == true  ) {

							    var handler = StripeCheckout.configure({
							    key: result.key,
							    locale: 'auto',
							    token: function(token) {
							      // You can access the token ID with `token.id`.
							      // Get the token ID to your server-side code for use.
							      var $input = $('<input type=hidden name=stripeToken />').val(token.id);
							      $('#formAddFunds').append($input).submit();
                    element.attr({'disabled' : 'true'});
							    }
							  });

							    // Open Checkout with further options:
							    handler.open({
							      email: result.email,
							      name: result.name,
							      description: result.description,
							      currency: result.currency,
							      amount: result.amount
							    });

							  // Close Checkout on page navigation:
							  $(window).on('popstate', function() {
							    handler.close();
							  });

					 	   element.removeAttr('disabled');
							 $('#error').fadeOut();
						}

						else if( result.success != false && result.stripeSuccess == true ) {
							window.location.href = result.url;
						}

						else if( result.success != false && result.bankTransfer == true ) {
							window.location.href = result.url;
						}

					else {
						var error = '';
                        for( $key in result.errors ){
                        	error += '<li><i class="glyphicon glyphicon-remove myicon-right"></i> ' + result.errors[$key] + '</li>';
                        }
						$('#showErrors').html(error);
						$('#error').fadeIn(500);
						element.removeAttr('disabled');
						}
						}//<----- SUCCESS
					}).submit();
				})(); //<--- FUNCTION %
			});//<<<-------- * END FUNCTION CLICK * ---->>>>
	//<---------------- End Add Funds ----------->>>>

      $(".onlyNumber").keydown(function (e) {
          // Allow: backspace, delete, tab, escape, enter and .
          if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
               // Allow: Ctrl+A, Command+A
              (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
               // Allow: home, end, left, right, down, up
              (e.keyCode >= 35 && e.keyCode <= 40)) {
                   // let it happen, don't do anything
                   return;
          }
          // Ensure that it is a number and stop the keypress
          if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
              e.preventDefault();
          }
      });

  });// document ready

  $('#paymentGateway').on('change', function(){

    var valueOriginal = $('.onlyNumber').val();
    var value = parseFloat($('.onlyNumber').val());

    if($(this).val() == 'paypal') {
			$('#imgPayment > img').attr('src','{{url('public/img/paypal.png')}}');

      // PayPal
      $feePayPal   = {{config('commissions.paypal_fee')}};
      $centsPayPal =  {{config('commissions.paypal_cents')}};

      var amount = (value * $feePayPal / 100) + $centsPayPal;
      var total = (value + amount);

      if( valueOriginal != '' || valueOriginal !=  0 ) {
      	$('#handlingFee').html(amount.toFixed(2));
        $('#total').html(total.toFixed(2));
      }

		} else {
				$('#imgPayment > img').attr('src','{{url('public/img/cards.png')}}');

        // Stripe
        $feeStripe   = {{config('commissions.stripe_fee')}};
        $centsStripe =  {{config('commissions.stripe_cents')}};

        var amount = (value * $feeStripe / 100) + $centsStripe;
        var total = (value + amount);

        if( valueOriginal != '' || valueOriginal !=  0 ) {
        	$('#handlingFee').html(amount.toFixed(2));
          $('#total').html(total.toFixed(2));
        }
		}
});

//<-------- * TRIM * ----------->

$('.onlyNumber').on('keyup', function(){

    var valueOriginal = $(this).val();
    var value = parseFloat($(this).val());
    var paymentGateway = $('#paymentGateway').val();

    if(paymentGateway == 'paypal') {

      // PayPal
      $feePayPal   = {{config('commissions.paypal_fee')}};
      $centsPayPal =  {{config('commissions.paypal_cents')}};

      var amount = (value * $feePayPal / 100) + $centsPayPal;
      var total = (value + amount);

      if( valueOriginal != '' || valueOriginal !=  0 ) {
      	$('#handlingFee').html(amount.toFixed(2));
        $('#total').html(total.toFixed(2));
      } else {
        $('#handlingFee, #total').html('0');
        }


    } else if (paymentGateway == 'stripe') {

      // Stripe
      $feeStripe   = {{config('commissions.stripe_fee')}};
      $centsStripe =  {{config('commissions.stripe_cents')}};

      var amount = (value * $feeStripe / 100) + $centsStripe;
      var total = (value + amount);

      if( valueOriginal != '' || valueOriginal !=  0 ) {
      	$('#handlingFee').html(amount.toFixed(2));
        $('#total').html(total.toFixed(2));
      } else {
        $('#handlingFee, #total').html('0');
    }
  }
});



</script>


@endsection
