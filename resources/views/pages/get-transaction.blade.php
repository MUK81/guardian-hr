@extends('layouts.theme')

@section('content')
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-12 mb-3">
				<div class="alert alert-danger" style="display: none;"></div>
			</div>
			<div class="col-12 col-md-5 mb-5 filter-col">
				<form id="getTransactionForm" method="post">
					@csrf
					<div class="form-group">
						<label>Transaction ID</label>
						<input id="fromDate" type="text" name="transactionId" class="form-control" required="required" value="{{ old('transactionId') }}" autocomplete="off" />
					</div>
					<div class="form-group text-center">
						<button type="submit" class="btn btn-success">Get Transaction</button>
					</div>
				</form>
			</div>
			<div class="col-12 result-col" style="display: none;">
			</div>
		</div>
	</div>
@endsection

@section('additional-js-codes')
	<script type="text/javascript">
		$(function() {
			$('#getTransactionForm').on('submit', function(event) {
				event.preventDefault();

				$form = $('#getTransactionForm');
				$form.find('button[type=submit]').html('<i class="fas fa-spinner fa-spin"></i>').attr('disabled', 'disabled');
				$('.alert-danger').html('').css('display', 'none');


				jQuery.ajax({
					type: 'POST',
					url: '{{ route('website.getTransaction') }}',
					dataType: 'json',
					data: $form.serialize(),
					success: function($response) {
						$form.find('button[type=submit]').html('Get Transaction').removeAttr('disabled');

						if(typeof($response.result) !== 'undefined' && $response.result) {
							$result = '<div id="updateFilter" class="w-100 mb-3 text-center"><button class="btn btn-dark"><i class="fa-solid fa-rotate"></i> Change Transaction ID</button></div>';

							if(!$response.data || typeof($response.data) == 'undefined' || $response.data.length <= 0) {
								$result += '<div class="w-100 text-center">No records found.</div>';
							} else {
								$result += '<table class="table table-striped">';
									$result += '<thead>';
										$result += '<tr>';
											$result += '<th class="customer">Customer</th>';
											$result += '<th class="merchant">Merchant</th>';
											$result += '<th class="transaction">Transaction</th>';
											$result += '<th class="fx">FX</th>';
										$result += '</tr>';
									$result += '</thead>';
									$result += '<tbody>';
										$trClass = '';
										if(typeof($response.data['transaction']) !== 'undefined') {
											switch($response.data['transaction']['merchant']['status']) {
												case 'ERROR':
												case 'DECLINED':
													$trClass = 'table-danger';
													break;
												case 'WAITING':
													$trClass = 'table-warning';
													break;
												case 'APPROVED':
													$trClass = 'table-success';
													break;
												default:
													break;
											}
										}

										$result += '<tr class="' + $trClass + '">';
											$result += '<td class="customer">';
												if(typeof($response.data['customerInfo']) !== 'undefined') {
													$customer = '';
													if(typeof($response.data['customerInfo']['id']) !== 'undefined') {
														$customer += 'ID: ' + $response.data['customerInfo']['id'];
													}
													$customer += ($customer != '' ? '<br />' : '') + 'Name: ' + $response.data['customerInfo']['billingFirstName'] + ' ' + $response.data['customerInfo']['billingLastName'];
													if($response.data['customerInfo']['billingCompany'] != null) {
														$customer += ($customer != '' ? '<br />' : '') + 'Company: ' + $response.data['customerInfo']['billingCompany'];
													}
													if($response.data['customerInfo']['billingCity'] != null) {
														$customer += ($customer != '' ? '<br />' : '') + 'City: ' + $response.data['customerInfo']['billingCity'];
													}

													$result += $customer;
												}
											$result += '</td>';
											$result += '<td class="merchant">';
												if(typeof($response.data['merchant']) !== 'undefined') {
													if(typeof($response.data['merchant']['id']) != 'undefined') {
														$result += 'Id: ' + $response.data['merchant']['id'];
														$result += '<br />';
													}
													$result += 'Name: ' + $response.data['merchant']['name'];
												}
											$result += '</td>';
											$result += '<td class="transaction">';
												if(typeof($response.data['transaction']) !== 'undefined') {
													$result += 'Operation: ' + $response.data['transaction']['merchant']['operation'];
													$result += '<br />';
													$result += 'TRX ID: ' + $response.data['transaction']['merchant']['transactionId'];
													$result += '<br />';
													$result += 'Message: ' + $response.data['transaction']['merchant']['message'];
													$result += '<br />';
													$result += 'Status: ' + $response.data['transaction']['merchant']['status'];
												}
											$result += '</td>';
											$result += '<td class="fx">';
												if(typeof($response.data['fx']) !== 'undefined') {
													if(typeof($response.data['fx']['merchant']['originalAmount']) !== 'undefined') {
														$result += 'Original: ' + $response.data['fx']['merchant']['originalAmount'] + ' ' + $response.data['fx']['merchant']['originalCurrency'];
														$result += '<br />';
													}
													if(typeof($response.data['fx']['merchant']['convertedAmount']) !== 'undefined') {
														$result += 'Converted: ' + $response.data['fx']['merchant']['convertedAmount'] + ' ' + $response.data['fx']['merchant']['convertedCurrency'];
													}
												}
											$result += '</td>';
										$result += '</tr>';
									$result += '</tbody>';


								$result += '</table>';
							}

							$('.filter-col').hide();
							$('.result-col').html($result).show();
						} else if(typeof($response.result) !== 'undefined' && !$response.result) {
							$error = '';
							$.each($response.message, function (key, val) {
								$error += val[0] + '<br />';
							});
							$('.alert-danger').html($error).css('display', 'block');
						}
					}
				});
			});

			$('body').on('click', '#updateFilter', function() {
				$(this).hide();
				$('.filter-col').show();
			});
		});
	</script>
@endsection