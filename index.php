<?php

	include 'assets/functions.php';
	include 'assets/parts/header.php';


    unset($_request);
?>


	<div class="container">
		<div class="row justify-content-md-center mb-4">
			<div class="col-sm-4  text-center">
				<h2>Image Gallery</h2>
			</div>
		</div>

		<div class="row justify-content-md-center mb-5">
			<div class="col-sm-4">
                <form action="index.php" method="post" enctype="multipart/form-data" id="uploadForm">

                <!-- Title -->
					<div class="input-group mb-3">
						<input type="text" name="photoName" class="form-control" placeholder="Name" aria-label="Name" aria-describedby="basic-addon1">
					</div>

					<!-- Photo -->
					<div class="input-group mb-3">
						<div class="custom-file">
							<input type="file" name="file" class="custom-file-input" id="inputGroupFile01" required>
							<label class="custom-file-label" for="inputGroupFile01">Choose file</label>
						</div>
					</div>

					<!-- Submit -->
					<div class="input-group mb-3">
						<input type="submit" class="btn btn-secondary btn-lg btn-block">
					</div>

				</form>
			</div>
		</div>

		<div class="border-top my-5"></div>

		<!--	Gallery	-->

            <?php the_gallery(); ?>

		</div>


	</div>




<!-- Optional JavaScript -->

<?php

	include 'assets/parts/footer.php';

?>