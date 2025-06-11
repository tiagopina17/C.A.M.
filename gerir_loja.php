<?php

ob_start(); // Start output buffering

include_once ("components/cp_navbar.php");
include_once ("components/cp_head.php");
include_once ("components/cp_gerir_loja.php");
include_once ("components/cp_footer.php");

ob_end_flush(); // Send all output at once
?>