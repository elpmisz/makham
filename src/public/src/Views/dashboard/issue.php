<?php
$menu = "dashboard";
$page = "dashboard-issue";
include_once(__DIR__ . "/../layout/header.php");

use App\Classes\User;

$USER = new User();
?>
<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">DASHBOARD</h4>
      </div>
      <div class="card-body">



      </div>
    </div>
  </div>
</div>

<?php include_once(__DIR__ . "/../layout/footer.php"); ?>