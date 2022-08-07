<?php
 
namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseWebsiteController;

use Illuminate\Http\Request;
use Validator;
 
class PageController extends BaseWebsiteController {
	/**
	 * Show the profile for a given user.
	 *
	 * @param  int  $id
	 * @return \Illuminate\View\View
	 */

	public function transactionQuery(Request $request) {
		if($request->isMethod('post')) {
			$validator = Validator::make($request->all(), [
				'fromDate' => 'nullable|date_format:d/m/Y',
				'toDate' => 'nullable|date_format:d/m/Y',
				'status' => 'nullable',
				'operation' => 'nullable',
				'paymentMethod' => 'nullable',
				'errorCode' => 'nullable',
				'filterField' => 'nullable',
				'url' => 'nullable',
			]);

			if($validator->fails()) {
				return ['result' => false, 'message' => $validator->getMessageBag()->toArray()];
			}

			if($this->getToken()) {
				$CHData = [];
				if($request->input('fromDate')) {
					$CHData['fromDate'] = \DateTime::createFromFormat('d/m/Y H:i:s', $request->input('fromDate').' 00:00:00')->format('Y-m-d');
				}
				if($request->input('toDate')) {
					$CHData['toDate'] = \DateTime::createFromFormat('d/m/Y H:i:s', $request->input('toDate').' 00:00:00')->format('Y-m-d');
				}
				if($request->input('status')) {
					$CHData['status'] = $request->input('status');
				}
				if($request->input('operation')) {
					$CHData['operation'] = $request->input('operation');
				}
				if($request->input('paymentMethod')) {
					$CHData['paymentMethod'] = $request->input('paymentMethod');
				}
				if($request->input('errorCode')) {
					$CHData['errorCode'] = $request->input('errorCode');
				}
				if($request->input('filterField')) {
					$CHData['filterField'] = $request->input('filterField');
				}

				$URL = 'https://sandbox-reporting.rpdpymnt.com/api/v3/transaction/list';
				if($request->input('url')) {
					$URL = $request->input('url');
				}
				$CH = curl_init($URL);
				curl_setopt($CH, CURLOPT_CUSTOMREQUEST, 'POST');
				curl_setopt($CH, CURLOPT_POSTFIELDS, http_build_query($CHData));
				curl_setopt($CH, CURLOPT_HTTPHEADER, array(
					'Authorization: '.$this->getToken(),
				));
				curl_setopt($CH, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($CH);
				curl_close($CH);
				if($response && !is_array($response)) $response = json_decode($response, true);

				return ['result' => true, 'data' => $response];
			} else {
				return ['result' => false, 'message' => [['Login error.']]];
			}
		}

		$this->vData['meta']['title'] = 'Transaction Query';
		return view('pages.transaction-query')->with($this->vData);
	}

	public function getTransaction(Request $request) {
		if($request->isMethod('post')) {
			$validator = Validator::make($request->all(), [
				'transactionId' => 'required',
			]);

			if($validator->fails()) {
				return ['result' => false, 'message' => $validator->getMessageBag()->toArray()];
			}

			if($this->getToken()) {
				$CHData = [
					'transactionId' => trim($request->input('transactionId')),
				];
				$CH = curl_init('https://sandbox-reporting.rpdpymnt.com/api/v3/transaction');
				curl_setopt($CH, CURLOPT_CUSTOMREQUEST, 'POST');
				curl_setopt($CH, CURLOPT_POSTFIELDS, http_build_query($CHData));
				curl_setopt($CH, CURLOPT_HTTPHEADER, array(
					'Authorization: '.$this->getToken(),
				));
				curl_setopt($CH, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($CH);
				curl_close($CH);
				if($response && !is_array($response)) $response = json_decode($response, true);

				return ['result' => true, 'data' => $response];
			} else {
				return ['result' => false, 'message' => [['Login error.']]];
			}
		}

		$this->vData['meta']['title'] = 'Get Transaction';
		return view('pages.get-transaction')->with($this->vData);
	}

	public function getClient(Request $request) {
		if($request->isMethod('post')) {
			$validator = Validator::make($request->all(), [
				'transactionId' => 'required',
			]);

			if($validator->fails()) {
				return ['result' => false, 'message' => $validator->getMessageBag()->toArray()];
			}

			if($this->getToken()) {
				$CHData = [
					'transactionId' => trim($request->input('transactionId')),
				];
				$CH = curl_init('https://sandbox-reporting.rpdpymnt.com/api/v3/client');
				curl_setopt($CH, CURLOPT_CUSTOMREQUEST, 'POST');
				curl_setopt($CH, CURLOPT_POSTFIELDS, http_build_query($CHData));
				curl_setopt($CH, CURLOPT_HTTPHEADER, array(
					'Authorization: '.$this->getToken(),
				));
				curl_setopt($CH, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($CH);
				curl_close($CH);
				if($response && !is_array($response)) $response = json_decode($response, true);

				return ['result' => true, 'data' => $response];
			} else {
				return ['result' => false, 'message' => [['Login error.']]];
			}
		}

		$this->vData['meta']['title'] = 'Get Client';
		return view('pages.get-client')->with($this->vData);
	}

	public function getToken() {
		if(session('merchantToken', null)) {
			if(session('merchantToken.expiryDT', null)){
				if(session('merchantToken.expiryDT') > (new \DateTime)) {
					return session('merchantToken.value', null);
				}
			}
		}

		$CHData = [
			'email' => env('MERCHANT_EMAIL'),
			'password' => env('MERCHANT_PASSWORD'),
		];
		$CH = curl_init('https://sandbox-reporting.rpdpymnt.com/api/v3/merchant/user/login');
		curl_setopt($CH, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($CH, CURLOPT_POSTFIELDS, http_build_query($CHData));
		curl_setopt($CH, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($CH);
		curl_close($CH);
		if($response && !is_array($response)) $response = json_decode($response, true);
		if(!is_array($response) || count($response) <= 0 || !isset($response['status']) || $response['status'] != 'APPROVED' || !isset($response['token'])) { 
			return false;
		}

		session(['merchantToken.value' => $response['token']]);
		session(['merchantToken.expiryDT' => (new \DateTime)->modify('+10 Minutes')]);

		return session('merchantToken.value');
	}
}