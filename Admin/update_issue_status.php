<?php
include("db.php");

date_default_timezone_set("Asia/Kolkata"); // ✅ correct time zone

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $id      = (int)($_POST['id'] ?? 0);
  $status  = trim($_POST['status'] ?? '');
  $message = trim($_POST['message'] ?? '');
  $date    = date("Y-m-d H:i:s");

  if ($id > 0 && $status !== '') {

    // ✅ 1. CHECK current status (avoid unnecessary update)
    $checkIssue = mysqli_fetch_assoc(mysqli_query($conn, "
      SELECT status FROM issues WHERE id='$id'
    "));

    if ($checkIssue && $checkIssue['status'] !== $status) {

      // ✅ 2. UPDATE main issue table
      if($status == "Resolved"){

    mysqli_query($conn, "
      UPDATE issues 
      SET status = 'Resolved',
          user_confirmed = 0,
          user_feedback = NULL
      WHERE id = '$id'
    ");

} else {

    mysqli_query($conn, "
      UPDATE issues 
      SET status = '$status'
      WHERE id = '$id'
    ");

}

      // ✅ 3. PREVENT duplicate timeline entry
      $check = mysqli_query($conn, "
        SELECT id FROM complaints_status 
        WHERE cid='$id' AND status='$status'
        ORDER BY add_dated DESC LIMIT 1
      ");

      if (mysqli_num_rows($check) == 0) {

        mysqli_query($conn, "
          INSERT INTO complaints_status (cid, message, status, add_dated)
          VALUES ('$id', '$message', '$status', '$date')
        ");
      }

      // ✅ 4. SEND EMAIL (safe check)
      $res = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT m.email, i.ticket_no 
        FROM issues i
        JOIN members m ON i.user_id = m.id
        WHERE i.id='$id'
      "));

      if (!empty($res['email'])) {
        $email  = $res['email'];
        $ticket = $res['ticket_no'];

        $subject = "Complaint Status Updated";
        $msg = "Your complaint ($ticket) status is now: $status";

        @mail($email, $subject, $msg);
      }
    }
  }

  header("Location: reports.php?updated=1");
  exit();
}
?>