<?php
ob_start(); // Start output buffering

include_once ("components_admin/cp_navbar_admin.php");
include_once ("../components/cp_head.php");
include_once ("components_admin/cp_index_admin.php");
include_once ("../components/cp_footer.php");

ob_end_flush(); // Send all output at once

?>