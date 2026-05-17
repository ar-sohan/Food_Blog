<?php
        session_start();

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { [cite: 68]
            header("Location: "); 
            exit();
        }
?>