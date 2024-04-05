<?php
$dashboard_menu = (isset($menu) && ($menu === "dashboard") ? "show" : "");
$dashboard_sale = ($page === "dashboard-sale" ? 'class="active"' : "");
$dashboard_purchase = ($page === "dashboard-purchase" ? 'class="active"' : "");
$dashboard_issue = ($page === "dashboard-issue" ? 'class="active"' : "");
$dashboard_product = ($page === "dashboard-product" ? 'class="active"' : "");

$user_menu = (isset($menu) && ($menu === "user") ? "show" : "");
$user_profile = ($page === "user-profile" ? 'class="active"' : "");
$user_change = ($page === "user-change" ? 'class="active"' : "");

$service_menu = (isset($menu) && ($menu === "service") ? "show" : "");
$service_pos = ($page === "service-pos" ? 'class="active"' : "");
$service_purchase = ($page === "service-purchase" ? 'class="active"' : "");
$service_sale = ($page === "service-sale" ? 'class="active"' : "");
$service_issue = ($page === "service-issue" ? 'class="active"' : "");
$service_waste = ($page === "service-waste" ? 'class="active"' : "");

$setting_menu = (isset($menu) && ($menu === "setting") ? "show" : "");
$setting_system = ($page === "setting-system" ? 'class="active"' : "");
$setting_user = ($page === "setting-user" ? 'class="active"' : "");
$setting_auth = ($page === "setting-auth" ? 'class="active"' : "");
$setting_bom = ($page === "setting-bom" ? 'class="active"' : "");
$setting_product = ($page === "setting-product" ? 'class="active"' : "");
$setting_promotion = ($page === "setting-promotion" ? 'class="active"' : "");
$setting_machine = ($page === "setting-machine" ? 'class="active"' : "");
$setting_customer = ($page === "setting-customer" ? 'class="active"' : "");
$setting_supplier = ($page === "setting-supplier" ? 'class="active"' : "");
$setting_category = ($page === "setting-category" ? 'class="active"' : "");
$setting_location = ($page === "setting-location" ? 'class="active"' : "");
$setting_store = ($page === "setting-store" ? 'class="active"' : "");
$setting_unit = ($page === "setting-unit" ? 'class="active"' : "");
$setting_brand = ($page === "setting-brand" ? 'class="active"' : "");

$auth_perchase = (isset($user_auth[0]) ? intval($user_auth[0]) : "");
$auth_issue = (isset($user_auth[1]) ? intval($user_auth[1]) : "");
$auth_waste = (isset($user_auth[2]) ? intval($user_auth[2]) : "");
?>
<nav id="sidebar">
  <ul class="list-unstyled">
    <li>
      <a href="#dashboard-menu" data-toggle="collapse" class="dropdown-toggle">รายงาน</a>
      <ul class="collapse list-unstyled <?php echo $dashboard_menu ?>" id="dashboard-menu">
        <?php if ($auth_perchase === 1) : ?>
          <li <?php echo $dashboard_purchase ?>>
            <a href="/dashboard/purchase">
              <i class="fa fa-chart-line pr-2"></i>
              รายงานผลิต
            </a>
          </li>
        <?php endif; ?>
        <?php if ($auth_issue === 1) : ?>
          <li <?php echo $dashboard_issue ?>>
            <a href="/dashboard/issue">
              <i class="fa fa-chart-line pr-2"></i>
              รายงานนำสินค้าเข้า - ออก
            </a>
          </li>
        <?php endif; ?>
        <li <?php echo $dashboard_product ?>>
          <a href="/dashboard/product">
            <i class="fa fa-chart-line pr-2"></i>
            รายงานวัตถุดิบ / สินค้า
          </a>
        </li>
      </ul>
    </li>
    <li>
      <a href="#user-menu" data-toggle="collapse" class="dropdown-toggle">ข้อมูลส่วนตัว</a>
      <ul class="collapse list-unstyled <?php echo $user_menu ?>" id="user-menu">
        <li <?php echo $user_profile ?>>
          <a href="/user/profile">
            <i class="fa fa-address-book pr-2"></i>
            รายละเอียด
          </a>
        </li>
        <li <?php echo $user_change ?>>
          <a href="/user/change">
            <i class="fa fa-key pr-2"></i>
            เปลี่ยนรหัสผ่าน
          </a>
        </li>
      </ul>
    </li>
    <li>
      <a href="#service-menu" data-toggle="collapse" class="dropdown-toggle">
        บริการ
      </a>
      <ul class="collapse list-unstyled <?php echo $service_menu ?>" id="service-menu">
        <?php if ($auth_perchase === 1) : ?>
          <li <?php echo $service_purchase ?>>
            <a href="/purchase">
              <i class="fa fa-bars pr-2"></i>
              ใบสั่งผลิต
            </a>
          </li>
        <?php endif ?>
        <?php if ($auth_issue === 1) : ?>
          <li <?php echo $service_issue ?>>
            <a href="/issue">
              <i class="fa fa-bars pr-2"></i>
              ใบนำสินค้าเข้า - ออก
            </a>
          </li>
        <?php endif ?>
        <?php if ($auth_waste === 1) : ?>
          <li <?php echo $service_waste ?>>
            <a href="/waste">
              <i class="fa fa-bars pr-2"></i>
              ใบสรุปของเสีย
            </a>
          </li>
        <?php endif ?>
      </ul>
    </li>
    <?php if (intval($user['level']) === 9) : ?>
      <li>
        <a href="#setting-menu" data-toggle="collapse" class="dropdown-toggle">ตั้งค่าระบบ</a>
        <ul class="collapse list-unstyled <?php echo $setting_menu ?>" id="setting-menu">
          <li <?php echo $setting_system ?>>
            <a href="/system">
              <i class="fa fa-gear pr-2"></i>
              ระบบ
            </a>
          </li>
          <li <?php echo $setting_user ?>>
            <a href="/user">
              <i class="fa fa-gear pr-2"></i>
              ผู้ใช้งาน
            </a>
          </li>
          <li <?php echo $setting_auth ?>>
            <a href="/auth">
              <i class="fa fa-gear pr-2"></i>
              จัดการสิทธิ์
            </a>
          </li>
          <li <?php echo $setting_bom ?>>
            <a href="/bom">
              <i class="fa fa-gear pr-2"></i>
              สูตรการผลิต
            </a>
          </li>
          <li <?php echo $setting_product ?>>
            <a href="/product">
              <i class="fa fa-gear pr-2"></i>
              วัตถุดิบ / สินค้า
            </a>
          </li>
          <li <?php echo $setting_machine ?>>
            <a href="/machine">
              <i class="fa fa-gear pr-2"></i>
              เครื่องจักร
            </a>
          </li>
          <li <?php echo $setting_customer ?>>
            <a href="/customer">
              <i class="fa fa-gear pr-2"></i>
              ลูกค้า
            </a>
          </li>
          <li <?php echo $setting_supplier ?>>
            <a href="/supplier">
              <i class="fa fa-gear pr-2"></i>
              ผู้จัดจำหน่าย
            </a>
          </li>
          <li <?php echo $setting_category ?>>
            <a href="/category">
              <i class="fa fa-gear pr-2"></i>
              หมวดหมู่
            </a>
          </li>
          <li <?php echo $setting_location ?>>
            <a href="/location">
              <i class="fa fa-gear pr-2"></i>
              สถานที่
            </a>
          </li>
          <li <?php echo $setting_store ?>>
            <a href="/store">
              <i class="fa fa-gear pr-2"></i>
              สถานที่จัดเก็บ
            </a>
          </li>
          <li <?php echo $setting_unit ?>>
            <a href="/unit">
              <i class="fa fa-gear pr-2"></i>
              หน่วยนับ
            </a>
          </li>
          <li <?php echo $setting_brand ?>>
            <a href="/brand">
              <i class="fa fa-gear pr-2"></i>
              ยี่ห้อ
            </a>
          </li>
        </ul>
      </li>
    <?php endif; ?>
  </ul>
</nav>