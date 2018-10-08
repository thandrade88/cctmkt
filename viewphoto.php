<?php

include 'assets/functions.php';
include 'assets/parts/header.php';


unset($_request);
?>


	<div class="container">

		<div class="row justify-content-md-center mb-4">
			<div class="col-sm-10  text-center">
				<?php showPhoto($_GET['id']); ?>
			</div>
		</div>

	</div>


	</div>




	<!-- Optional JavaScript -->

<?php

include 'assets/parts/footer.php';

?>