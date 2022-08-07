<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BaseWebsiteController extends BaseController {
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	protected $vData;

	public function __construct() {
		$this->vData = [];
		$this->vData['_v'] = '0.0.1';
	}
}