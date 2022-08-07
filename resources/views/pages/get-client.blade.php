@extends('layouts.theme')

@section('content')
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-12 col-md-5 mb-5 filter-col">
				<form id="getClientForm" method="post">
					@csrf
					<div class="form-group">
						<label>Transaction ID</label>
						<input id="fromDate" type="text" name="transactionId" class="form-control" required="required" value="{{ old('transactionId') }}" autocomplete="off" />
					</div>
					<div class="form-group text-center">
						<button type="submit" class="btn btn-success">Get Client</button>
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
			$('#getClientForm').on('submit', function(event) {
				event.preventDefault();

				$form = $('#getClientForm');
				$form.find('button[type=submit]').html('<i class="fas fa-spinner fa-spin"></i>').attr('disabled', 'disabled');
				$('.alert-danger').html('').css('display', 'none');


				jQuery.ajax({
					type: 'POST',
					url: '{{ route('website.getClient') }}',
					dataType: 'json',
					data: $form.serialize(),
					success: function($response) {
						$form.find('button[type=submit]').html('Get Client').removeAttr('disabled');
						console.log($response);

						if(typeof($response.result) !== 'undefined' && $response.result) {
							$result = '<div id="updateFilter" class="w-100 mb-3 text-center"><button class="btn btn-dark"><i class="fa-solid fa-rotate"></i> Change Transaction ID</button></div>';

							if(!$response.data || typeof($response.data) == 'undefined' || $response.data.length <= 0 && typeof($response.data.customerInfo) !== 'undefined' && $response.data.customerInfo.length > 0) {
								$result += '<div class="w-100 text-center">No client found.</div>';
							} else {
								$result += '<table class="table table-striped">';
									$result += '<tbody>';
										$result += '<tr>';
											$result += '<th>ID:</th>';
											$result += '<td>' + $response.data.customerInfo['id'] + '</td>';
										$result += '</tr>';
										$result += '<tr>';
											$result += '<th>First Name:</th>';
											$result += '<td>' + $response.data.customerInfo['billingFirstName'] + '</td>';
										$result += '</tr>';
										$result += '<tr>';
											$result += '<th>Last Name:</th>';
											$result += '<td>' + $response.data.customerInfo['billingLastName'] + '</td>';
										$result += '</tr>';
										$result += '<tr>';
											$result += '<th>Number:</th>';
											$result += '<td>' + $response.data.customerInfo['number'] + '</td>';
										$result += '</tr>';
										$result += '<tr>';
											$result += '<th>Email:</th>';
											$result += '<td>' + $response.data.customerInfo['email'] + '</td>';
										$result += '</tr>';
										if($response.data.customerInfo['birthday'] != null) {
											$result += '<tr>';
												$result += '<th>Birthday:</th>';
												$result += '<td>' + $response.data.customerInfo['birthday'] + '</td>';
											$result += '</tr>';
										}

										$billingAddress = '';
										if($response.data.customerInfo['billingAddress1'] != null) {
											$billingAddress += $response.data.customerInfo['billingAddress1'];
										}
										if($response.data.customerInfo['billingAddress2'] != null) {
											$billingAddress += ($billingAddress != '' ? '<br />' : '') + $response.data.customerInfo['billingAddress2'];
										}
										if($response.data.customerInfo['billingCity'] != null) {
											$billingAddress += ($billingAddress != '' ? '<br />' : '') + $response.data.customerInfo['billingCity'];
										}
										if($response.data.customerInfo['billingState'] != null) {
											$billingAddress += ($billingAddress != '' ? '<br />' : '') + $response.data.customerInfo['billingState'];
										}
										if($response.data.customerInfo['billingPostcode'] != null) {
											$billingAddress += ($billingAddress != '' ? '<br />' : '') + $response.data.customerInfo['billingPostcode'];
										}
										if($response.data.customerInfo['billingCountry'] != null) {
											$billingAddress += ($billingAddress != '' ? '<br />' : '') + $response.data.customerInfo['billingCountry'];
										}
										if($billingAddress != '') {
											$result += '<tr>';
												$result += '<th>Billing Address:</th>';
												$result += '<td>' + $billingAddress + '</td>';
											$result += '</tr>';
										}

										$shippingAddress = '';
										if($response.data.customerInfo['shippingAddress1'] != null) {
											$shippingAddress += $response.data.customerInfo['shippingAddress1'];
										}
										if($response.data.customerInfo['shippingAddress2'] != null) {
											$shippingAddress += ($shippingAddress != '' ? '<br />' : '') + $response.data.customerInfo['shippingAddress2'];
										}
										if($response.data.customerInfo['shippingCity'] != null) {
											$shippingAddress += ($shippingAddress != '' ? '<br />' : '') + $response.data.customerInfo['shippingCity'];
										}
										if($response.data.customerInfo['shippingState'] != null) {
											$shippingAddress += ($shippingAddress != '' ? '<br />' : '') + $response.data.customerInfo['shippingState'];
										}
										if($response.data.customerInfo['shippingPostcode'] != null) {
											$shippingAddress += ($shippingAddress != '' ? '<br />' : '') + $response.data.customerInfo['shippingPostcode'];
										}
										if($response.data.customerInfo['shippingCountry'] != null) {
											$shippingAddress += ($shippingAddress != '' ? '<br />' : '') + $response.data.customerInfo['shippingCountry'];
										}
										if($shippingAddress != '') {
											$result += '<tr>';
												$result += '<th>Shipping Address:</th>';
												$result += '<td>' + $shippingAddress + '</td>';
											$result += '</tr>';
										}
										


										
										
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