<?php
    require_once(db.php);
    $queries = [
            'restaurants' => "SELECT COUNT(*) FROM restaurants",
            'menu_items'  => "SELECT COUNT(*) FROM menu_items", 
            'reviews'     => "SELECT COUNT(*) FROM reviews", 
            'posts'       => "SELECT COUNT(*) FROM food_experience_posts" 
        ];

    $counts = [];
        foreach ($queries as $key => $sql) {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $counts[$key] = $stmt->fetchColumn();
        }
    return $counts;
?>