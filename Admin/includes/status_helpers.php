<?php
function statusBadgeClass($status){
  $status = strtolower(trim($status));

  if ($status === 'pending') {
    return 'bg-warning text-dark';
  }

  if ($status === 'in progress' || $status === 'in-progress') {
    return 'bg-info text-dark';
  }

  if ($status === 'resolved') {
    return 'bg-success';
  }

  return 'bg-secondary';
}
