<?php

$filename = strtolower($_FILES['image']['name']);

if (strpos($filename, 'garbage') !== false) {
    $category = "Waste Management";
    $category_id = 1;
    $priority = "Medium";
}
elseif (strpos($filename, 'water') !== false) {
    $category = "Water";
    $category_id = 2;
    $priority = "High";
}
elseif (strpos($filename, 'road') !== false) {
    $category = "Road";
    $category_id = 3;
    $priority = "High";
}
else {
    $category = "Other";
    $category_id = 4;
    $priority = "Low";
}

echo json_encode([
    "category" => $category,
    "category_id" => $category_id,
    "priority" => $priority
]);