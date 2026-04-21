<?php
$replyQ = mysqli_query($conn, "
  SELECT * FROM complaints_status 
  WHERE cid='$id' 
  ORDER BY add_dated DESC
");
?>
<?php while($rep = mysqli_fetch_assoc($replyQ)): ?>
  <div class="timeline-item active">
    <strong><?= htmlspecialchars($rep['status']) ?></strong>
    <p><?= nl2br(htmlspecialchars($rep['message'])) ?></p>
    <small class="text-muted"><?= $rep['add_dated'] ?></small>
  </div>
<?php endwhile; ?>
