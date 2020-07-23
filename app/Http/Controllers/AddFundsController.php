<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\AdminSettings;
use App\Models\Images;
use App\Models\Deposits;
use App\Models\User;
use Fahim\PaypalIPN\PaypalIPNListener;
use App\Helper;
use Mail;
use Carbon\Carbon;

class AddFundsController extends Controller
{
	public function __construct( AdminSettings $settings, Request $request) {
		$this->settings = $settings::first();
		$this->request = $request;
	}

    public function send()
		{

			if($this->settings->sell_option == 'off') {
				return response()->json([
						'success' => false,
						'errors' => ['error' => trans('misc.error') ],
				]);
			}

			if($this->settings->currency_position == 'right') {
				$currencyPosition =  2;
			} else {
				$currencyPosition =  null;
			}

			$messages = array (
			'amount.min' => trans('misc.amount_minimum'.$currencyPosition, ['symbol' => $this->settings->currency_symbol, 'code' => $this->settings->currency_code]),
			'amount.max' => trans('misc.amount_maximum'.$currencyPosition, ['symbol' => $this->settings->currency_symbol, 'code' => $this->settings->currency_code]),
		);

		//<---- Validation
		$validator = Validator::make($this->request->all(), [
				'amount' => 'required|integer|min:'.$this->settings->min_deposits_amount.'|max:'.$this->settings->max_deposits_amount,
				'payment_gateway' => 'required|in:paypal,stripe'
	    	],$messages);

			if ($validator->fails()) {
			        return response()->json([
					        'success' => false,
					        'errors' => $validator->getMessageBag()->toArray(),
					    ]);
			    }

					// PayPal
					if( $this->request->payment_gateway == 'paypal' ) {
						return $this->paypal();
					}

					// Stripe
					if( $this->request->payment_gateway == 'stripe' ) {
						return $this->stripe();
					}

}//<--------- End Method  Send

protected function stripe()
{

	$email    = Auth::user()->email;

	$feeStripe   = config('commissions.stripe_fee');
	$centsStripe =  config('commissions.stripe_cents');

	$amountFixed = number_format($this->request->amount + ($this->request->amount * $feeStripe / 100) + $centsStripe, 2, '.', ',');

	$amountGross = ($this->request->amount);
	$amount   = ($amountFixed*100);
	$currency_code = $this->settings->currency_code;
	$description = trans('misc.add_funds_desc');
	$nameSite = $this->settings->title;


	if( isset( $this->request->stripeToken ) ) {

			\Stripe\Stripe::setApiKey($this->settings->stripe_secret_key);

			// Get the credit card details submitted by the form
			$token = $this->request->stripeToken;

			// Create a charge: this will charge the user's card
			try {
				$charge = \Stripe\Charge::create(array(
					"amount" => $amount, // Amount in cents
					"currency" => strtolower($currency_code),
					"source" => $token,
					"description" => $description
					));

				// Insert DB
				$sql          = new Deposits;
				$sql->user_id = Auth::user()->id;
			  $sql->txn_id  = $charge->id;
				$sql->amount  = $this->request->amount;
				$sql->payment_gateway = 'Stripe';
				$sql->save();

				//Add Funds to User
				User::find(Auth::user()->id)->increment('funds', $this->request->amount);

		return response()->json([
							'success' => true,
							'stripeSuccess' => true,
							'url' => url('user/dashboard/deposits')
					]);

			} catch(\Stripe\Error\Card $e) {
				// The card has been declined
			}
	} else {
		return response()->json([
							'success' => true,
							'stripeTrue' => true,
							"key" => $this->settings->stripe_public_key,
							"email" => $email,
							"amount" => $amount,
							"currency" => strtoupper($currency_code),
							"description" => $description,
							"name" => $nameSite,
					]);
	}
}//<----- End Method stripe()

	protected function paypal()
	{

			if ( $this->settings->paypal_sandbox == 'true') {
				// SandBox
				$action = "https://www.sandbox.paypal.com/cgi-bin/webscr";
				} else {
				// Real environment
				$action = "https://www.paypal.com/cgi-bin/webscr";
				}

			$urlSuccess = url('user/dashboard/deposits');
			$urlCancel   = url('user/dashboard/add/funds');
			$urlPaypalIPN = url('paypal/ipn');

			$feePayPal   = config('commissions.paypal_fee');
			$centsPayPal =  config('commissions.paypal_cents');

			$amountFixed = number_format($this->request->amount + ($this->request->amount * $feePayPal / 100) + $centsPayPal, 2, '.', ',');

			return response()->json([
					        'success' => true,
					        'formPP' => '<form id="form_pp" name="_xclick" action="'.$action.'" method="post"  style="display:none" target="_blank">
					        <input type="hidden" name="cmd" value="_xclick">
					        <input type="hidden" name="return" value="'.$urlSuccess.'">
					        <input type="hidden" name="cancel_return"   value="'.$urlCancel.'">
					        <input type="hidden" name="notify_url" value="'.$urlPaypalIPN.'">
					        <input type="hidden" name="currency_code" value="'.$this->settings->currency_code.'">
					        <input type="hidden" name="amount" id="amount" value="'.$amountFixed.'">
					        <input type="hidden" name="custom" value="id='.Auth::user()->id.'&amount='.$this->request->amount.'">
					        <input type="hidden" name="item_name" value="'.trans('misc.add_funds_desc').'">
					        <input type="hidden" name="business" value="'.$this->settings->paypal_account.'">
					        <input type="submit">
					        </form>',
					    ]);

	}//<------ End Method paypal()


    public function paypalIpn()
		{

			$ipn = new PaypalIPNListener();

			$ipn->use_curl = false;

			if ($this->settings->paypal_sandbox == 'true') {
				// SandBox
				$ipn->use_sandbox = true;
				} else {
				// Real environment
				$ipn->use_sandbox = false;
				}

	    $verified = $ipn->processIpn();

			$custom  = $_POST['custom'];
			parse_str($custom, $funds);

			$payment_status = $_POST['payment_status'];
			$txn_id         = $_POST['txn_id'];
			$amount         = $_POST['mc_gross'];


	    if($verified) {
				if($payment_status == 'Completed') {
	          // Check outh POST variable and insert in DB

						$verifiedTxnId = Deposits::where('txn_id',$txn_id)->first();

			if(!isset($verifiedTxnId)) {

				$sql = new Deposits;
		   	$sql->user_id = $funds['id'];
			  $sql->txn_id = $txn_id;
				$sql->amount = $funds['amount'];
				$sql->payment_gateway = 'PayPal';
			  $sql->save();

				//Add Funds to User
				User::find($funds['id'])->increment('funds', $funds['amount']);

			}// <--- Verified Txn ID

	      } // <-- Payment status
	    } else {
	    	//Some thing went wrong in the payment !
	    }

    }//<----- End Method paypalIpn()

}
