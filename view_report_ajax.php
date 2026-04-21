<?php
include("db.php");

function esc($v){
    return htmlspecialchars($v ?? "", ENT_QUOTES, "UTF-8");
}

$id = (int)($_GET['id'] ?? 0);

$q = mysqli_query($conn, "SELECT * FROM issues WHERE id='$id'");
$row = mysqli_fetch_assoc($q);
?>

<style>

.issue-meta{
    background:#f8f9fc;
    border-radius:12px;
    padding:18px;
    margin-bottom:20px;
}

.meta-label{
    font-size:12px;
    color:#6c757d;
    text-transform:uppercase;
    letter-spacing:.5px;
}

.meta-value{
    font-weight:600;
    font-size:15px;
}

.ticket-box{
    background:linear-gradient(135deg,#0d6efd,#0b5ed7);
    color:white;
    border-radius:14px;
    padding:18px;
    text-align:center;
}

.ticket-box small{
    opacity:.8;
    letter-spacing:.5px;
}

.ticket-number{
    font-size:22px;
    font-weight:800;
    letter-spacing:1px;
}

.issue-detail-label{
    font-size:12px;
    text-transform:uppercase;
    color:#6c757d;
    margin-bottom:6px;
}

.issue-detail-value{
    font-weight:500;
}

.status-badge{
    padding:6px 12px;
    border-radius:30px;
    font-size:12px;
    font-weight:600;
}

.timeline{
    border-left:3px solid #e9ecef;
    margin-left:10px;
    padding-left:18px;
}

.timeline-step{
    margin-bottom:18px;
    position:relative;
}

.timeline-step::before{
    content:'';
    width:14px;
    height:14px;
    border-radius:50%;
    background:#0d6efd;
    position:absolute;
    left:-26px;
    top:2px;
}

.timeline-step.pending::before{
    background:#adb5bd;
}

.timeline-title{
    font-weight:600;
}

.timeline-desc{
    font-size:13px;
    color:#6c757d;
}
.image-box {
    max-width: 250px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    transition: 0.3s;
}

.image-box:hover {
    transform: scale(1.03);
}

.report-img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    cursor: pointer;
}
.timeline-step:last-child::before {
    background:#28a745;
    box-shadow:0 0 10px #28a745;
}
</style>

<!-- HEADER -->
<div class="row mb-3">

    <div class="col-md-8">
        <div class="fw-bold fs-5"><?= esc($row['title']) ?></div>
        <div class="text-muted"><?= esc($row['location']) ?></div>
    </div>

    <div class="col-md-4">
        <div class="ticket-box">
            <small>Ticket Number</small>
            <div class="ticket-number">
    <?= esc($row['ticket_no']) ?>
</div>
        </div>
    </div>

</div>

<!-- META INFO -->
<div class="issue-meta">
    <div class="row">

        <div class="col-md-3">
            <div class="meta-label">User ID</div>
            <div class="meta-value"><?= esc($row['user_id']) ?></div>
        </div>

        <div class="col-md-3">
            <div class="meta-label">Reported Date</div>
            <div class="meta-value">
                <?= date("d M Y", strtotime($row['reported_date'])) ?>
            </div>
        </div>

        <div class="col-md-3">
            <div class="meta-label">Priority</div>
            <div class="meta-value">
                <span class="badge bg-<?= strtolower($row['priority'])=='high'?'danger':(strtolower($row['priority'])=='medium'?'warning':'success') ?>">
                    <?= esc($row['priority']) ?>
                </span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="meta-label">Current Status</div>
            <div class="meta-value">
                <span class="status-badge bg-<?= 
                    strtolower($row['status'])=='resolved'?'success':
                    (strtolower($row['status'])=='in progress'?'info':'warning')
                ?>">
                    <?= esc($row['status']) ?>
                </span>
            </div>
        </div>

    </div>
</div>

<!-- DETAILS -->
<div class="row mb-3">

    <div class="col-md-8">
        <div class="issue-detail-label">Issue Details</div>
        <div class="issue-detail-value"><?= esc($row['description']) ?></div>
    </div>

<div class="mt-3">
    <div class="issue-detail-label mb-2">Photo Evidence</div>

    <?php if(!empty($row['image'])): ?>
        
        <div class="image-box">
            <img src="uploads/<?php echo $row['image']; ?>" 
     class="img-fluid report-img"
     onclick="window.open(this.src)">
        </div>

    <?php else: ?>
        <p class="text-muted">No image uploaded</p>
    <?php endif; ?>
</div>

</div>

<hr>

<!-- TIMELINE -->
<div class="issue-detail-label mb-3">Status Timeline</div>

<div class="timeline">

<?php
date_default_timezone_set("Asia/Kolkata"); 

$timeline = mysqli_query($conn, "
SELECT * FROM complaints_status 
WHERE cid='$id' 
ORDER BY add_dated ASC
");

$shown = []; // ✅ duplicate 

if(mysqli_num_rows($timeline) > 0){

    while($t = mysqli_fetch_assoc($timeline)){

        // 🔒 unique key (status + time)
        $key = $t['status'] . '_' . $t['add_dated'];

        if(in_array($key, $shown)){
            continue; // ❌ duplicate skip
        }

        $shown[] = $key;
?>

    <div class="timeline-step">
        <div class="timeline-title"><?= esc($t['status']) ?></div>
        <div class="timeline-desc"><?= esc($t['message']) ?></div>
        <small class="text-muted">
            <?= date("d M Y, h:i A", strtotime($t['add_dated'])) ?>
        </small>
    </div>

<?php 
    }

} else {
?>

    <div class="timeline-step">
        <div class="timeline-title">Report Submitted</div>
        <div class="timeline-desc">
            <?= date("d M Y, h:i A", strtotime($row['reported_date'])) ?>
        </div>
    </div>

<?php } ?>

</div>
