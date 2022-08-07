<?php

namespace Tests\Unit;

use Tests\TestCase;

class RouteTest extends TestCase {
	/**
	 * A basic unit test example.
	 *
	 * @return void
	 */
	public function test_transaction_query_get_method() {
		$response = $this->get('/transaction-query');
		$response->assertStatus(200);
	}
	public function test_transaction_query_post_method() {
		$response = $this->post('/transaction-query', [
			'fromDate' => '01/07/2015',
			'toDate' => '01/10/2015',
		]);
		$response->assertSee('data');
	}

	public function test_transaction_get_method() {
		$response = $this->get('/get-transaction');
		$response->assertStatus(200);
	}
	public function test_transaction_post_method() {
		$response = $this->post('/get-transaction', [
			'transactionId' => '1030245-1606174013-1307',
		]);
		$response->assertSee('data');
	}

	public function test_client_get_method() {
		$response = $this->get('/get-client');
		$response->assertStatus(200);
	}
	public function test_client_post_method() {
		$response = $this->post('/get-client', [
			'transactionId' => '1030245-1606174013-1307',
		]);
		$response->assertSee('data');
	}
}
