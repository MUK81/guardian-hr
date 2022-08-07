<div class="container mt-4">
	<nav>
		<div class="nav nav-tabs">
			<a class="nav-link{{ (Route::is('website.transactionQuery') ? ' active' : '') }}" href="{{ route('website.transactionQuery') }}">Transaction Query</a>
			<a class="nav-link{{ (Route::is('website.getTransaction') ? ' active' : '') }}" href="{{ route('website.getTransaction') }}">Get Transaction</a>
			<a class="nav-link{{ (Route::is('website.getClient') ? ' active' : '') }}" href="{{ route('website.getClient') }}">Get Client</a>
		</div>
	</nav>
</div>