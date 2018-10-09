<?php

/**
 *
 * Db connection
 *
 */

function db_connect() {
	$con = new PDO("mysql:host=localhost;dbname=cct_mkt", "root", "");
	return $con;
}


/**
 *
 * Upload FIle
 *
 */
function upload() {

	$currentDir = getcwd();
	$uploadDirectory = "/uploads/";

	$errors = []; // Store all foreseen and unforseen errors here

	$namePhoto = $_POST['photoName'];

	$fileExtensions = ['jpeg','jpg','png']; // Get all the file extensions

	$fileName = $_FILES['file']['name'];
	$fileSize = $_FILES['file']['size'];
	$fileTmpName  = $_FILES['file']['tmp_name'];
	$fileType = $_FILES['file']['type'];
	$fileExtension = strtolower(end(explode('.',$fileName)));

	$newFileName = time().'.'.$fileExtension ;
	$uploadPath = $currentDir . $uploadDirectory . $newFileName;

	if (! in_array($fileExtension,$fileExtensions)) {

		$errors[] = "This file extension is not allowed. Please upload a JPEG or PNG file";

	}

	if ($fileSize > 10000000) {

		$errors[] = "This file is more than 10MB. Sorry, it has to be up to 10MB";

	}

	if (empty($errors)) {
		$didUpload = move_uploaded_file($fileTmpName, $uploadPath);

		if ($didUpload) {
			$con = db_connect();

			$stmt = $con->prepare("INSERT INTO photo(photoname, file) VALUES(?, ?)");
			$stmt->bindParam(1,$namePhoto);
			$stmt->bindParam(2,$newFileName);

			if($stmt->execute()) {

				echo "The file " . basename($fileName) . " has been uploaded";
				header("location: index.php");

			} else {

				unlink($uploadPath);
				echo "An error occurred somewhere. Try again or contact the admin";

			}
		} else {

			echo "An error occurred somewhere. Try again or contact the admin";

		}

	} else {

		foreach ($errors as $error) {

			echo $error . "These are the errors" . "\n";

		}

	}

}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	upload();
}


/**
 *
 * Load gallery
 *
 */

function the_gallery() {


		$con = db_connect();

		$limit = 9;
		$query = "SELECT *  FROM photo";

		$s = $con->prepare($query);
		$s->execute();
		$total_results = $s->rowCount();
		$total_pages = ceil($total_results/$limit);

		if (!isset($_GET['page'])) {
		    $page = 1;
		} else{
		    $page = $_GET['page'];
		}

		$starting_limit = ($page-1)*$limit;

		$show  = "SELECT * FROM photo ORDER BY id DESC LIMIT $starting_limit, $limit";

		$r = $con->prepare($show);
		$r->execute();

		?>

		<div class="row">

		<?php
			while($res = $r->fetch(PDO::FETCH_ASSOC)):
		?>

			<div class="col-sm-4 text-center mb-4 photo-single photo-<?php echo $res['id' ]; ?>">
				<div class="card">
					<div class="card-header">
						<img class="card-img-top" src="./uploads/<?php echo $res['file']; ?>" alt="<?php echo $res['photoname']; ?>">
					</div>

					<div class="card-body">

						<div class="input-group mb-2">
							<div class="input-group-prepend">
								<a class="input-group-text upload_meta" href="./viewphoto.php?id=<?php echo $res['id' ]; ?>" data-id="<?php echo $res['id']; ?>" data-action="views"><i class="fa fa-eye"></i></a>
							</div>
							<input type="text" class="form-control views" value="<?php echo $res['views']; ?>" aria-label="Views" aria-describedby="btnGroupAddon" readonly>
						</div>
						<div class="input-group">
							<div class="input-group-prepend">
								<a class="input-group-text upload_meta" href="./uploads/<?php echo $res['file']; ?>" target="_blank" data-id="<?php echo $res['id']; ?>" data-action="downloads" download><i class="fa fa-download"></i></a>
							</div>
							<input type="text" class="form-control downloads" value="<?php echo $res['downloads']; ?>" aria-label="Downloads" aria-describedby="btnGroupAddon" readonly>
						</div>


					</div>
				</div>
			</div>



		<?php
			endwhile;
		?>

		</div>

		<div class="border-top my-5"></div>


        <?php if($total_pages > 1) { ?>
            <!--	Pagination	-->
            <div class="row">
                <div class="col-sm-12 text-right">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-end">
                            <li class="page-item">
                                <a class="page-link" href="?page=1" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                    <span class="sr-only">Previous</span>
                                </a>
                            </li>

                                                <?php for ( $page = 1; $page <= $total_pages; $page ++ ): ?>
                              <li class="page-item"><a class="page-link"
                                                       href="<?php echo "?page=$page"; ?>"><?php echo $page; ?></a></li>
                                                <?php endfor; ?>

                            <li class="page-item">
                                <a class="page-link" href="<?php echo "?page=$total_pages"; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        <?php
            }
        }



/**
 *
 * Update photo meta
 *
 */

function update_meta($id,$action) {
	$con = db_connect();

	$stmt = $con->prepare("SELECT * FROM photo WHERE id=?");
	$stmt->bindParam(1,$id);
	$stmt->execute();
	$photo = $stmt->fetch(PDO::FETCH_ASSOC);

	$photo[$action]++;

	$query_update = "UPDATE photo SET {$action}=? WHERE id=?";
	$stmt= $con->prepare($query_update);
	$stmt->bindParam(1,$photo[$action]);
	$stmt->bindParam(2,$id);
	$stmt->execute();


	echo $photo[$action];
}

if (isset($_GET['id']) && isset($_GET['action'])) {
	update_meta($_GET['id'], $_GET['action']);
}


/**
 *
 * Update photo meta
 *
 */

function checkPhotos() {
	$con = db_connect();

	$stmt = $con->prepare("SELECT * FROM photo");
	$stmt->execute();

	while($res = $stmt->fetch(PDO::FETCH_ASSOC)):
		$photos[] = array('id' => $res['id'], 'views' => $res['views'] , 'downloads' => $res['downloads']);
	endwhile;

	echo json_encode($photos);
}

if ( isset($_GET['checkPhotos'])) {
	checkPhotos();
}


/**
 *
 * View photo
 *
 */

function showPhoto($id) {
	$con = db_connect();

	$stmt = $con->prepare("SELECT * FROM photo WHERE id=?");
	$stmt->bindParam(1,$id);
	$stmt->execute();
	$photo = $stmt->fetch(PDO::FETCH_ASSOC);

	?>

	<div class=" text-center mb-4 photo-single photo-<?php echo $photo['id' ]; ?>">
		<div class="card">
			<div class="card-header big-photo">
				<img src="./uploads/<?php echo $photo['file']; ?>" alt="<?php echo $photo['photoname']; ?>">
			</div>

			<div class="card-body">
				<?php if(!empty($photo['photoname'])){ ?>
				<div class="row">
					<div class="col-sm-12 text-center mb-3">
						<h2><?php echo $photo['photoname']; ?></h2>
					</div>
				</div>
				<?php } ?>

				<div class="row">
					<div class="input-group col-sm-6 mb-2">
						<div class="input-group-prepend">
							<div class="input-group-text upload_meta" style="cursor:default;"><i class="fa fa-eye"></i></div>
						</div>
						<input type="text" class="form-control views" value="<?php echo $photo['views']; ?>" aria-label="Views" aria-describedby="btnGroupAddon" readonly>
					</div>

					<div class="input-group col-sm-6 mb-2">
						<div class="input-group-prepend">
							<a class="input-group-text upload_meta" href="./uploads/<?php echo $photo['file']; ?>" target="_blank" data-id="<?php echo $photo['id']; ?>" data-action="downloads" download><i class="fa fa-download"></i></a>
						</div>
						<input type="text" class="form-control downloads" value="<?php echo $photo['downloads']; ?>" aria-label="Downloads" aria-describedby="btnGroupAddon" readonly>
					</div>
				</div>

			</div>
		</div>
	</div>
	
	<div class="clearfix"></div>
	
	<div class="col-sm-12 mb-3 text-left">
		<a href="javascript:history.go(-1)" class="go-back"><i class="fa fa-arrow-left"></i></a>
	</div>


	<?php

}
