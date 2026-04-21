<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

include("db.php");

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=locations_export_' . date('Y-m-d_H-i') . '.csv');

$output = fopen('php://output', 'w');

// CSV Header
fputcsv($output, [
  'ID','Name','Zone','Address','Latitude','Longitude',
  'Active Issues','Resolved Issues','Problem Heat','Priority','Description','Created At'
]);

$q = mysqli_query($conn, "SELECT * FROM locations ORDER BY id DESC");
while($row = mysqli_fetch_assoc($q)){
  fputcsv($output, [
    $row['id'] ?? '',
    $row['name'] ?? '',
    $row['zone'] ?? '',
    $row['address'] ?? '',
    $row['latitude'] ?? '',
    $row['longitude'] ?? '',
    $row['active_issues'] ?? 0,
    $row['resolved_issues'] ?? 0,
    $row['problem_heat'] ?? '',
    $row['priority'] ?? '',
    $row['description'] ?? '',
    $row['created_at'] ?? ''
  ]);
}

fclose($output);
exit();
