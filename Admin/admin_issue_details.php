<?php
include("../db.php");

$id = $_GET['id'];

$q = mysqli_query($conn, "SELECT * FROM issues WHERE id='$id'");
$row = mysqli_fetch_assoc($q);
?>

<h5><?= $row['title']; ?></h5>
<p><b>Status:</b> <?= $row['status']; ?></p>
<p><b>Location:</b> <?= $row['location']; ?></p>

<hr>

<h6 class="text-danger">User Feedback</h6>

<?php if(!empty($row['feedback'])): ?>
    <div class="alert alert-danger">
        <?= nl2br(htmlspecialchars($row['feedback'])); ?>
    </div>
<?php else: ?>
    <p class="text-muted">No feedback given</p>
<?php endif; ?>

<hr>

<form method="POST" action="update_issue_status.php">
    <input type="hidden" name="id" value="<?= $row['id'] ?>">

    <label>Status</label>
    <select name="status" class="form-select mb-3">
        <option <?= $row['status']=='Pending'?'selected':'' ?>>Pending</option>
        <option <?= $row['status']=='In Progress'?'selected':'' ?>>In Progress</option>
        <option <?= $row['status']=='Resolved'?'selected':'' ?>>Resolved</option>
    </select>

    <button class="btn btn-primary">Update Status</button>
</form>

<?php if($row['user_confirmed'] == 2): ?>
    <hr>
    <button class="btn btn-danger reopenBtn" data-id="<?= $row['id'] ?>">
        🔄 Reopen Issue
    </button>
<?php endif; ?>