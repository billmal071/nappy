@extends('admin.layout')

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
            		{{ trans('misc.payment_settings') }}

          </h4>

        </section>

        <!-- Main content -->
        <section class="content">

        	 @if(Session::has('success_message'))
		    <div class="alert alert-success">
		    	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">×</span>
								</button>
		       <i class="fa fa-check margin-separator"></i> {{ Session::get('success_message') }}
		    </div>
		@endif

        	<div class="content">

        		<div class="row">

        	<div class="box box-danger">
                <div class="box-header with-border">
                  <h3 class="box-title"><strong>{{ trans('misc.payment_settings') }}</strong></h3>
                </div><!-- /.box-header -->

                <!-- form start -->
                <form class="form-horizontal" method="POST" action="{{ url('panel/admin/payments') }}" enctype="multipart/form-data">

                	<input type="hidden" name="_token" value="{{ csrf_token() }}">

					@include('errors.errors-forms')


                      <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('misc.currency_code') }}</label>
                      <div class="col-sm-10">
                      	<select name="currency_code" class="form-control">

                      		<option @if( $settings->currency_code == 'USD' ) selected="selected" @endif value="USD">USD - U.S Dollar</option>
						  	<option @if( $settings->currency_code == 'EUR' ) selected="selected" @endif  value="EUR">EUR - Euro</option>
						  	<option @if( $settings->currency_code == 'GBP' ) selected="selected" @endif value="GBP">GBP - UK</option>
						  	<option @if( $settings->currency_code == 'AUD' ) selected="selected" @endif value="AUD">AUD - Australian Dollar</option>
						  	<option @if( $settings->currency_code == 'JPY' ) selected="selected" @endif value="JPY">JPY - Japanese Yen</option>

						  	<option @if( $settings->currency_code == 'BRL' ) selected="selected" @endif value="BRL">BRL - Brazilian Real</option>
						  	<option @if( $settings->currency_code == 'MXN' ) selected="selected" @endif  value="MXN">MXN - Mexican Peso</option>
						  	<option @if( $settings->currency_code == 'SEK' ) selected="selected" @endif value="SEK">SEK - Swedish Krona</option>
						  	<option @if( $settings->currency_code == 'CHF' ) selected="selected" @endif value="CHF">CHF - Swiss Franc</option>


						  	<option @if( $settings->currency_code == 'SGD' ) selected="selected" @endif value="SGD">SGD - Singapore Dollar</option>
						  	<option @if( $settings->currency_code == 'DKK' ) selected="selected" @endif value="DKK">DKK - Danish Krone</option>
						  	<option @if( $settings->currency_code == 'RUB' ) selected="selected" @endif value="RUB">RUB - Russian Ruble</option>

						  	<option @if( $settings->currency_code == 'CAD' ) selected="selected" @endif value="CAD">CAD - Canadian Dollar</option>
						  	<option @if( $settings->currency_code == 'CZK' ) selected="selected" @endif value="CZK">CZK - Czech Koruna</option>
						  	<option @if( $settings->currency_code == 'HKD' ) selected="selected" @endif value="HKD">HKD - Hong Kong Dollar</option>
						  	<option @if( $settings->currency_code == 'PLN' ) selected="selected" @endif value="PLN">PLN - Polish Zloty</option>
						  	<option @if( $settings->currency_code == 'NOK' ) selected="selected" @endif value="NOK">NOK - Norwegian Krone</option>
                          </select>
                      </div>
                    </div>
                  </div><!-- /.box-body -->

                   <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('misc.fee_commission') }}</label>
                      <div class="col-sm-10">
                      	<select name="fee_commission" class="form-control">
                          @for ($i=1; $i <= 50; ++$i)
                            <option @if( $settings->fee_commission == $i ) selected="selected" @endif value="{{$i}}">{{$i}}%</option>
                            @endfor
                            </select>
                      </div>
                    </div>
                  </div><!-- /.box-body -->

                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('misc.min_sale_amount') }}</label>
                      <div class="col-sm-10">
                        <input type="number" min="1" autocomplete="off" value="{{ $settings->min_sale_amount }}" name="min_sale_amount" class="form-control onlyNumber" placeholder="{{ trans('misc.min_sale_amount') }}">
                      </div>
                    </div>
                  </div><!-- /.box-body -->

                   <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('misc.max_sale_amount') }}</label>
                      <div class="col-sm-10">
                        <input type="number" min="1" autocomplete="off" value="{{ $settings->max_sale_amount }}" name="max_sale_amount" class="form-control onlyNumber" placeholder="{{ trans('misc.max_sale_amount') }}">
                      </div>
                    </div>
                  </div><!-- /.box-body -->

                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('misc.min_deposits_amount') }}</label>
                      <div class="col-sm-10">
                        <input type="number" min="1" autocomplete="off" value="{{ $settings->min_deposits_amount }}" name="min_deposits_amount" class="form-control onlyNumber" placeholder="{{ trans('misc.min_deposits_amount') }}">
                      </div>
                    </div>
                  </div><!-- /.box-body -->

                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('misc.max_deposits_amount') }}</label>
                      <div class="col-sm-10">
                        <input type="number" min="1" autocomplete="off" value="{{ $settings->max_deposits_amount }}" name="max_deposits_amount" class="form-control onlyNumber" placeholder="{{ trans('misc.max_deposits_amount') }}">
                      </div>
                    </div>
                  </div><!-- /.box-body -->

                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{ trans('misc.amount_min_withdrawal') }}</label>
                      <div class="col-sm-10">
                        <input type="number" min="1" autocomplete="off" value="{{ $settings->amount_min_withdrawal }}" name="amount_min_withdrawal" class="form-control onlyNumber" placeholder="{{ trans('misc.amount_min_withdrawal') }}">
                      </div>
                    </div>
                  </div><!-- /.box-body -->

                  <!-- Start Box Body -->
                 <div class="box-body">
                   <div class="form-group">
                     <label class="col-sm-2 control-label">{{ trans('misc.currency_position') }}</label>
                     <div class="col-sm-10">
                       <select name="currency_position" class="form-control">
                         <option @if( $settings->currency_position == 'left' ) selected="selected" @endif value="left">{{$settings->currency_symbol}}99 - {{trans('misc.left')}}</option>
                         <option @if( $settings->currency_position == 'right' ) selected="selected" @endif value="right">99{{$settings->currency_symbol}} {{trans('misc.right')}}</option>
                         </select>
                     </div>
                   </div>
                 </div><!-- /.box-body -->

                  <!-- Start Box Body -->
                  <div class="box-body">
                    <div class="form-group">
                      <label class="col-sm-2 control-label">{{trans('misc.payments_options')}}</label>
                      <div class="col-sm-10">
                        <div class="checkbox icheck">
                        <label class="padding-zero">
                          <input type="checkbox" value="1" name="enable_paypal" @if( $settings->enable_paypal == '1' ) checked="checked" @endif>
                          PayPal
                        </label>
                      </div>
                      <div class="checkbox icheck">
                        <label class="padding-zero">
                          <input type="checkbox" value="1" name="enable_stripe" @if( $settings->enable_stripe == '1' ) checked="checked" @endif>
                          Stripe
                        </label>
                      </div>
                      </div>
                    </div>
                  </div><!-- /.box-body -->

                  <div class="box-header with-border">
                    <h3 class="box-title"><strong>PayPal</strong></h3>
                  </div><!-- /.box-header -->

                  <!-- Start Box Body -->
               <div class="box-body">
                 <div class="form-group">
                   <label class="col-sm-2 control-label">{{ trans('admin.paypal_account') }}</label>
                   <div class="col-sm-10">
                     <input type="text" value="{{ $settings->paypal_account }}" name="paypal_account" class="form-control" placeholder="{{ trans('admin.paypal_account') }}">
                   </div>
                 </div>
               </div><!-- /.box-body -->

               <!-- Start Box Body -->
               <div class="box-body">
                 <div class="form-group">
                   <label class="col-sm-2 control-label">PayPal Sandbox</label>
                   <div class="col-sm-10">
                     <div class="radio">
                     <label class="padding-zero">
                       <input type="radio" value="true" name="paypal_sandbox" @if( $settings->paypal_sandbox == 'true' ) checked="checked" @endif checked>
                       On
                     </label>
                   </div>
                   <div class="radio">
                     <label class="padding-zero">
                       <input type="radio" value="false" name="paypal_sandbox" @if( $settings->paypal_sandbox == 'false' ) checked="checked" @endif>
                       Off
                     </label>
                   </div>
                   </div>
                 </div>
               </div><!-- /.box-body -->

               <div class="box-header with-border">
                 <h3 class="box-title"><strong>Stripe</strong></h3>
               </div><!-- /.box-header -->

                 <!-- Start Box Body -->
                 <div class="box-body">
                   <div class="form-group">
                     <label class="col-sm-2 control-label">Stripe Secret Key</label>
                     <div class="col-sm-10">
                       <input type="text" value="{{ $settings->stripe_secret_key }}" name="stripe_secret_key" class="form-control">
                      <p class="help-block"><a href="https://dashboard.stripe.com/account/apikeys" target="_blank">https://dashboard.stripe.com/account/apikeys</a></p>
                     </div>
                   </div>
                 </div><!-- /.box-body -->

                 <!-- Start Box Body -->
                 <div class="box-body">
                   <div class="form-group">
                     <label class="col-sm-2 control-label">Stripe Publishable Key</label>
                     <div class="col-sm-10">
                       <input type="text" value="{{ $settings->stripe_public_key }}" name="stripe_public_key" class="form-control">
                      <p class="help-block"><a href="https://dashboard.stripe.com/account/apikeys" target="_blank">https://dashboard.stripe.com/account/apikeys</a></p>
                     </div>
                   </div>
                 </div><!-- /.box-body -->


               <div class="box-footer">
                 <button type="submit" class="btn btn-success">{{ trans('admin.save') }}</button>
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

	<script type="text/javascript">
		//Flat red color scheme for iCheck
        $('input[type="radio"]').iCheck({
          radioClass: 'iradio_flat-red'
        });

        $('input[type="checkbox"]').iCheck({
          checkboxClass: 'icheckbox_square-red',
    	    radioClass: 'iradio_square-red'
	  });

    $(document).ready(function() {

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

    });

	</script>


@endsection
