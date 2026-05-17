<?php
    // AJAX search endpoint - GET /controller/apiSearch.php?q=&location=&area=
    // Returns JSON {restaurants: [...], items: [...]}.
    // Public: anyone (visitor, member, admin) can search.

    header('Content-Type: application/json');
    require_once('../model/searchModel.php');

    $q        = trim($_GET['q']        ?? '');
    $location = trim($_GET['location'] ?? '');
    $area     = trim($_GET['area']     ?? '');

    $restaurants = searchRestaurants($q, $location, $area);
    $items       = searchMenuItems($q, $location, $area);

    echo json_encode([
        'q'           => $q,
        'location'    => $location,
        'area'        => $area,
        'restaurants' => $restaurants,
        'items'       => $items,
    ]);
?>
