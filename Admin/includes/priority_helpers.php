<?php
function priorityBadgeClass($priority){
  $p = strtolower(trim($priority));

  return match($p){
    'high'   => 'bg-danger',
    'medium' => 'bg-warning text-dark',
    'low'    => 'bg-success',
    default  => 'bg-secondary'
  };
}
