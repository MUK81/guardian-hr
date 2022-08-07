@extends('layouts.theme')

@section('content')
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-12 mb-3">
				<div class="alert alert-danger" style="display: none;"></div>
			</div>
			<div class="col-12 col-md-5 mb-5 filter-col">
				<form id="transactionQueryForm" method="post">
					@csrf
					<input type="hidden" name="url" value="" />
					<div class="form-group">
						<label>From Date</label>
						<input id="fromDate" type="text" name="fromDate" class="form-control" value="{{ old('fromDate') }}" placeholder="DD/MM/YYYY" autocomplete="off" />
					</div>
					<div class="form-group">
						<label>To Date</label>
						<input id="toDate" type="text" name="toDate" class="form-control" value="{{ old('toDate') }}" placeholder="DD/MM/YYYY" autocomplete="off" />
					</div>
					<div class="form-group">
						<label>Status</label>
						<select class="form-control" name="status">
							<option value=""></option>
							<option value="APPROVED">APPROVED</option>
							<option value="WAITING">WAITING</option>
							<option value="DECLINED">DECLINED</option>
							<option value="ERROR">ERROR</option>
						</select>
					</div>
					<div class="form-group">
						<label>Operation</label>
						<select class="form-control" name="operation">
							<option value=""></option>
							<option value="DIRECT">DIRECT</option>
							<option value="REFUND">REFUND</option>
							<option value="3D">3D</option>
							<option value="3DAUTH">3DAUTH</option>
							<option value="STORED">STORED</option>
						</select>
					</div>
					<div class="form-group">
						<label>Payment Method</label>
						<select class="form-control" name="paymentMethod">
							<option value=""></option>
							<option value="CREDITCARD">CREDITCARD</option>
							<option value="CUP">CUP</option>
							<option value="IDEAL">IDEAL</option>
							<option value="GIROPAY">GIROPAY</option>
							<option value="MISTERCASH">MISTERCASH</option>
							<option value="STORED">STORED</option>
							<option value="PAYTOCARD">PAYTOCARD</option>
							<option value="CEPBANK">CEPBANK</option>
							<option value="CITADEL">CITADEL</option>
						</select>
					</div>
					<div class="form-group">
						<label>Error Code</label>
						<select class="form-control" name="errorCode">
							<option value=""></option>
							<option value="Do not honor">Do not honor</option>
							<option value="Invalid Transaction">Invalid Transaction</option>
							<option value="Invalid Card">Invalid Card</option>
							<option value="Not sufficient funds">Not sufficient funds</option>
							<option value="Incorrect PIN">Incorrect PIN</option>
							<option value="Invalid country associatio">Invalid country associatio</option>
							<option value="Currency not allowed">Currency not allowed</option>
							<option value="3-D Secure Transport Error">3-D Secure Transport Error</option>
							<option value="Transaction not permitted to cardholder">Transaction not permitted to cardholder</option>
						</select>
					</div>
					<div class="form-group">
						<label>Filter Field</label>
						<select class="form-control" name="filterField">
							<option value=""></option>
							<option value="Transaction UUID">Transaction UUID</option>
							<option value="Customer Email">Customer Email</option>
							<option value="Reference No">Reference No</option>
							<option value="Custom Data">Custom Data</option>
							<option value="Card PAN">Card PAN</option>
						</select>
					</div>
					<div class="form-group text-center">
						<button id="submitButton" type="submit" class="btn btn-success">Get Report</button>
					</div>
				</form>
			</div>
			<div class="col-12 result-col" style="display: none;">
			</div>
			<ul class="pagination mt-4 justify-content-center" style="display: none;">
				<li class="page-item prev"><a class="page-link" href="">Previous</a></li>
				<li class="page-item active"><a class="page-link">1</a></li>
				<li class="page-item next"><a class="page-link" href="">Next</a></li>
			</ul>
		</div>
	</div>
@endsection

@section('additional-js-codes')
	<script type="text/javascript">
		function checkPagination($data) {
			if(!$data || typeof($data.data) == 'undefined' || $data.data.length <= 0) {
				$('.pagination').hide();
			} else {
				$('.pagination .page-item.active').find('.page-link').html($data.current_page);
				if($data.prev_page_url) {
					$('.pagination').find('.prev').removeClass('disabled');
					$('.pagination').find('.prev .page-link').attr('href', $data.prev_page_url);
				} else {
					$('.pagination').find('.prev').addClass('disabled');
					$('.pagination').find('.prev .page-link').attr('href', '');
				}
				if($data.next_page_url) {
					$('.pagination').find('.next').removeClass('disabled');
					$('.pagination').find('.next .page-link').attr('href', $data.next_page_url);
				} else {
					$('.pagination').find('.next').addClass('disabled');
					$('.pagination').find('.next .page-link').attr('href', '');
				}
				$('.pagination').show();
			}
		}

		$(function() {
			$('.pagination .page-link').on('click', function(event) {
				event.preventDefault();

				$pageNumber = $(this);
				if($pageNumber.attr('href') != '') {
					$url = $pageNumber.attr('href');
					$('#transactionQueryForm').find('input[name=url]').val($pageNumber.attr('href'));
					$('#transactionQueryForm').trigger('submit');
				}
			});

			$('#submitButton').on('click', function() {
				$('#transactionQueryForm').find('input[name=url]').val('');
			});

			$('#transactionQueryForm').on('submit', function(event) {
				event.preventDefault();

				$form = $('#transactionQueryForm');
				$form.find('button[type=submit]').html('<i class="fas fa-spinner fa-spin"></i>').attr('disabled', 'disabled');
				$('.alert-danger').html('').css('display', 'none');


				jQuery.ajax({
					type: 'POST',
					url: '{{ route('website.transactionQuery') }}',
					dataType: 'json',
					data: $form.serialize(),
					success: function($response) {
						$form.find('button[type=submit]').html('Get Report').removeAttr('disabled');

						if(typeof($response.result) !== 'undefined' && $response.result) {
							checkPagination($response.data);

							$result = '<div id="updateFilter" class="w-100 mb-3 text-center"><button class="btn btn-dark"><i class="fa-solid fa-rotate"></i> Update Filter</button></div>';

							if(!$response.data.data || typeof($response.data.data) == 'undefined' || $response.data.data.length <= 0) {
								$result += '<div class="w-100 text-center">No records found.</div>';
							} else {
								$result += '<table class="table table-striped">';
									$result += '<thead>';
										$result += '<tr>';
											$result += '<th class="id">#</th>';
											$result += '<th class="customer">Customer</th>';
											$result += '<th class="merchant">Merchant</th>';
											$result += '<th class="acquirer">Acquirer</th>';
											$result += '<th class="transaction">Transaction</th>';
											$result += '<th class="fx">FX</th>';
										$result += '</tr>';
									$result += '</thead>';
									$from = $response.data.from;
									$result += '<tbody>';
										$.each($response.data.data, function (key, $value) {
											$trClass = '';
											if(typeof($value['transaction']) !== 'undefined') {
												switch($value['transaction']['merchant']['status']) {
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
												$result += '<td class="id">' + $from + '</td>';
												$result += '<td class="customer">';
													if(typeof($value['customerInfo']) !== 'undefined') {
														$result += 'Name: ' + (typeof($value['customerInfo']['billingFirstName']) !== 'undefined' ? $value['customerInfo']['billingFirstName'] : '');
														$result += ' ' + (typeof($value['customerInfo']['billingLastName']) !== 'undefined' ? $value['customerInfo']['billingLastName'] : '');
														$result += '<br />';
														$result += 'Email: ' + (typeof($value['customerInfo']['email']) !== 'undefined' ? $value['customerInfo']['email'] : '');
														if(typeof($value['customerInfo']['number']) !== 'undefined') {
															$result += '<br />';
															$result += 'Number: ' + (typeof($value['customerInfo']['number']) !== 'undefined' ? $value['customerInfo']['number'] : '');
														}
													}
												$result += '</td>';
												$result += '<td class="merchant">';
													if(typeof($value['merchant']) !== 'undefined') {
														$result += 'Id: ' + $value['merchant']['id'];
														$result += '<br />';
														$result += 'Name: ' + $value['merchant']['name'];
													}
												$result += '</td>';
												$result += '<td class="acquirer">';
													if(typeof($value['acquirer']) !== 'undefined') {
														$result += 'Id: ' + $value['acquirer']['id'];
														$result += '<br />';
														$result += 'Name: ' + $value['acquirer']['name'];
														$result += '<br />';
														$result += 'Type: ' + $value['acquirer']['type'];
														$result += '<br />';
														$result += 'Code: ' + $value['acquirer']['code'];
													}
												$result += '</td>';
												$result += '<td class="transaction">';
													if(typeof($value['transaction']) !== 'undefined') {
														$result += 'ID: ' + $value['transaction']['merchant']['transactionId'];
														$result += '<br />';
														$result += 'Operation: ' + $value['transaction']['merchant']['operation'];
														$result += '<br />';
														$result += 'Message: ' + $value['transaction']['merchant']['message'];
														$result += '<br />';
														$result += 'Status: ' + $value['transaction']['merchant']['status'];
													}
												$result += '</td>';
												$result += '<td class="fx">';
													if(typeof($value['fx']) !== 'undefined') {
														$result += 'Original: ' + $value['fx']['merchant']['originalAmount'] + ' ' + $value['fx']['merchant']['originalCurrency'];
														$result += '<br />';
														$result += 'Converted: ' + $value['fx']['merchant']['convertedAmount'] + ' ' + $value['fx']['merchant']['convertedCurrency'];
													}
												$result += '</td>';
											$result += '</tr>';

											$from++;
										});
									$result += '</tbody>';
								$result += '</table>';
							}

							$('.filter-col').hide();
							$('.result-col').html($result).show();
							window.scrollTo(0, 0);
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

			var fromDate = document.getElementById('fromDate');
			var toDate = document.getElementById('toDate');
			var dateInputMask = function dateInputMask(elm) {
				elm.addEventListener('keypress', function(e) {
					if(e.keyCode < 47 || e.keyCode > 57) {
						e.preventDefault();
					}

					var len = elm.value.length;

					// If we're at a particular place, let the user type the slash
					// i.e., 12/12/1212
					if(len !== 1 || len !== 3) {
						if(e.keyCode == 47) {
							e.preventDefault();
						}
					}

					// If they don't add the slash, do it for them...
					if(len === 2) {
						elm.value += '/';
					}

					// If they don't add the slash, do it for them...
					if(len === 5) {
						elm.value += '/';
					}
				});
			};
			dateInputMask(fromDate);
			dateInputMask(toDate);
		});
	</script>
@endsection