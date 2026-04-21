<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>

<?php
	include("db.php");
	
?>
<html>
	<head>
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

	<title></title>
		<style>
			.icon-circle{
			  width:36px;
			  height:36px;
			  border-radius:50%;
			  background:#f1f3f5;
			  display:inline-flex;
			  align-items:center;
			  justify-content:center;
			  font-size:16px;
			}

			.table td, .table th{
			  padding:14px;
			}

			.table-hover tbody tr:hover{
			  background:#f8fafc;
			}

			.card{
			  border-radius:18px;
			}
		</style>
	</head>
	<body>
		<?php 
				include_once("includes/leftnav.php");
         ?>
            
            <!-- Main Content -->
            <div class="col-lg-9 col-xl-10 main-content">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div>
                        <h2 class="fw-bold text-primary">Report New Issue</h2>
                        <p class="text-muted mb-0">Manage urban problem categories and their configurations</p>
                    </div>
                    <div>
                       
							
                    </div>
                </div>
                
                
                
                <!-- Categories Table -->
                <div class="card" style="padding:50px;">
                    
					
<form action="save_issue.php" method="POST" enctype="multipart/form-data">
						

						<div class="modal-body">

						  <div class="row mb-3">
							<div class="col-md-6">
							  <label class="form-label">Issue Title</label>
							  <input type="text" name="title" class="form-control" required>
							</div>

							<div class="col-md-6">
							  <label class="form-label">Category</label>
							  <select name="category" class="form-select" required>
								<option value="">Select</option>
								<option>Road</option>
								<option>Electricity</option>
								<option>Water</option>
							  </select>
							</div>
						  </div>

						  <div class="mb-3">
							<label class="form-label">Description</label>
							<textarea name="description" class="form-control" rows="3"></textarea>
						  </div>

						  <div class="row mb-3">
							<div class="col-md-6">
							  <label class="form-label">Location</label>
							  <input type="text" name="location" class="form-control">
							</div>

							<div class="col-md-6">
							  <label class="form-label">Priority</label>
							  <select name="priority" class="form-select">
								<option value="low">Low</option>
								<option value="medium" selected>Medium</option>
								<option value="high">High</option>
							  </select>
							</div>
						  </div>

						  <div class="mb-3">
							<label class="form-label">Department</label>
							<select name="department" class="form-select">
							  <option>Electricity</option>
							  <option>Water</option>
							  <option>Road</option>
							</select>
						  </div>

						  <div class="mb-3">
							<label class="form-label">Upload Image</label>
							<input type="file" name="image" class="form-control">
						  </div>

						</div>

						
						 <a href="view_issue.php">
						 <button type="submit" name="submit_issue" class="btn btn-primary">Submit</button>
						   
						   <button type="button" class="btn btn-secondary" >Cancel</button>
							</a>

					  </form>
					  
				</div>
		
	

		<!-- Bootstrap Bundle with Popper -->
		<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

		<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
		
		<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

		

		<div class="modal fade" id="successModal" tabindex="-1">
		  <div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">

			  <div class="modal-header bg-success text-white">
				<h5 class="modal-title">Success</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			  </div>

			  <div class="modal-body text-center">
				<i class="fas fa-check-circle text-success fa-3x mb-3"></i>
				<h5>Issue reported successfully!</h5>
			  </div>

			  <div class="modal-footer justify-content-center">
				<button class="btn btn-success" data-bs-dismiss="modal">OK</button>
			  </div>

			</div>
		  </div>
		</div>
		<?php if(isset($_GET['success'])){ ?>
			<script>
			  document.addEventListener("DOMContentLoaded", function () {
				var successModal = new bootstrap.Modal(document.getElementById('successModal'));
				successModal.show();
			  });
			</script>
		<?php } ?>
	</body>
</html>

