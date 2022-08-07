<div class="main-container">
	<?php if($errors->any()) { ?>
		<div class="container">
			<div class="alert alert-danger">
				<?php foreach($errors->all() as $message) { ?>
					{{ $message }}<br />
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	@yield('content')
</div>