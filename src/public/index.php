<?php
require_once(__DIR__ . "/vendor/autoload.php");

$ROUTER = new AltoRouter();

##################### SALE #####################
$ROUTER->map("GET", "/sale", function () {
  require(__DIR__ . "/src/Views/sale/index.php");
});
$ROUTER->map("GET", "/sale/create", function () {
  require(__DIR__ . "/src/Views/sale/create.php");
});
$ROUTER->map("GET", "/sale/edit/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/sale/edit.php");
});
$ROUTER->map("GET", "/sale/download", function () {
  require(__DIR__ . "/src/Views/sale/download.php");
});
$ROUTER->map("POST", "/sale/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/sale/action.php");
});

##################### PURCHASE #####################
$ROUTER->map("GET", "/purchase", function () {
  require(__DIR__ . "/src/Views/purchase/index.php");
});
$ROUTER->map("GET", "/purchase/create", function () {
  require(__DIR__ . "/src/Views/purchase/create.php");
});
$ROUTER->map("GET", "/purchase/auth", function () {
  require(__DIR__ . "/src/Views/purchase/auth.php");
});
$ROUTER->map("GET", "/purchase/edit/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/purchase/edit.php");
});
$ROUTER->map("GET", "/purchase/approve/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/purchase/approve.php");
});
$ROUTER->map("GET", "/purchase/product/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/purchase/product.php");
});
$ROUTER->map("GET", "/purchase/process/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/purchase/process.php");
});
$ROUTER->map("GET", "/purchase/check/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/purchase/check.php");
});
$ROUTER->map("GET", "/purchase/complete/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/purchase/complete.php");
});
$ROUTER->map("GET", "/purchase/download", function () {
  require(__DIR__ . "/src/Views/purchase/download.php");
});
$ROUTER->map("POST", "/purchase/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/purchase/action.php");
});

##################### ISSUE #####################
$ROUTER->map("GET", "/issue", function () {
  require(__DIR__ . "/src/Views/issue/index.php");
});
$ROUTER->map("GET", "/issue/create", function () {
  require(__DIR__ . "/src/Views/issue/create.php");
});
$ROUTER->map("GET", "/issue/auth", function () {
  require(__DIR__ . "/src/Views/issue/auth.php");
});
$ROUTER->map("GET", "/issue/edit/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/issue/edit.php");
});
$ROUTER->map("GET", "/issue/approve/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/issue/approve.php");
});
$ROUTER->map("GET", "/issue/complete/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/issue/complete.php");
});
$ROUTER->map("GET", "/issue/download", function () {
  require(__DIR__ . "/src/Views/issue/download.php");
});
$ROUTER->map("POST", "/issue/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/issue/action.php");
});

##################### BOM #####################
$ROUTER->map("GET", "/bom", function () {
  require(__DIR__ . "/src/Views/bom/index.php");
});
$ROUTER->map("GET", "/bom/create", function () {
  require(__DIR__ . "/src/Views/bom/create.php");
});
$ROUTER->map("GET", "/bom/edit/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/bom/edit.php");
});
$ROUTER->map("GET", "/bom/download", function () {
  require(__DIR__ . "/src/Views/bom/download.php");
});
$ROUTER->map("POST", "/bom/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/bom/action.php");
});

##################### PRODUCT #####################
$ROUTER->map("GET", "/product", function () {
  require(__DIR__ . "/src/Views/product/index.php");
});
$ROUTER->map("GET", "/product/create", function () {
  require(__DIR__ . "/src/Views/product/create.php");
});
$ROUTER->map("GET", "/product/edit/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/product/edit.php");
});
$ROUTER->map("GET", "/product/download", function () {
  require(__DIR__ . "/src/Views/product/download.php");
});
$ROUTER->map("POST", "/product/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/product/action.php");
});

##################### PROMOTION #####################
$ROUTER->map("GET", "/promotion", function () {
  require(__DIR__ . "/src/Views/promotion/index.php");
});
$ROUTER->map("GET", "/promotion/create", function () {
  require(__DIR__ . "/src/Views/promotion/create.php");
});
$ROUTER->map("GET", "/promotion/edit/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/promotion/edit.php");
});
$ROUTER->map("GET", "/promotion/download", function () {
  require(__DIR__ . "/src/Views/promotion/download.php");
});
$ROUTER->map("POST", "/promotion/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/promotion/action.php");
});

##################### MACHINE #####################
$ROUTER->map("GET", "/machine", function () {
  require(__DIR__ . "/src/Views/machine/index.php");
});
$ROUTER->map("GET", "/machine/create", function () {
  require(__DIR__ . "/src/Views/machine/create.php");
});
$ROUTER->map("GET", "/machine/edit/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/machine/edit.php");
});
$ROUTER->map("GET", "/machine/download", function () {
  require(__DIR__ . "/src/Views/machine/download.php");
});
$ROUTER->map("POST", "/machine/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/machine/action.php");
});

##################### CUSTOMER #####################
$ROUTER->map("GET", "/customer", function () {
  require(__DIR__ . "/src/Views/customer/index.php");
});
$ROUTER->map("GET", "/customer/create", function () {
  require(__DIR__ . "/src/Views/customer/create.php");
});
$ROUTER->map("GET", "/customer/edit/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/customer/edit.php");
});
$ROUTER->map("GET", "/customer/download", function () {
  require(__DIR__ . "/src/Views/customer/download.php");
});
$ROUTER->map("POST", "/customer/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/customer/action.php");
});

##################### SUPPLIER #####################
$ROUTER->map("GET", "/supplier", function () {
  require(__DIR__ . "/src/Views/supplier/index.php");
});
$ROUTER->map("GET", "/supplier/create", function () {
  require(__DIR__ . "/src/Views/supplier/create.php");
});
$ROUTER->map("GET", "/supplier/edit/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/supplier/edit.php");
});
$ROUTER->map("GET", "/supplier/download", function () {
  require(__DIR__ . "/src/Views/supplier/download.php");
});
$ROUTER->map("POST", "/supplier/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/supplier/action.php");
});

##################### CATEGORY #####################
$ROUTER->map("GET", "/category", function () {
  require(__DIR__ . "/src/Views/category/index.php");
});
$ROUTER->map("GET", "/category/create", function () {
  require(__DIR__ . "/src/Views/category/create.php");
});
$ROUTER->map("GET", "/category/edit/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/category/edit.php");
});
$ROUTER->map("GET", "/category/download", function () {
  require(__DIR__ . "/src/Views/category/download.php");
});
$ROUTER->map("POST", "/category/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/category/action.php");
});

##################### LOCATION #####################
$ROUTER->map("GET", "/location", function () {
  require(__DIR__ . "/src/Views/location/index.php");
});
$ROUTER->map("GET", "/location/create", function () {
  require(__DIR__ . "/src/Views/location/create.php");
});
$ROUTER->map("GET", "/location/edit/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/location/edit.php");
});
$ROUTER->map("GET", "/location/download", function () {
  require(__DIR__ . "/src/Views/location/download.php");
});
$ROUTER->map("POST", "/location/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/location/action.php");
});

##################### UNIT #####################
$ROUTER->map("GET", "/unit", function () {
  require(__DIR__ . "/src/Views/unit/index.php");
});
$ROUTER->map("GET", "/unit/create", function () {
  require(__DIR__ . "/src/Views/unit/create.php");
});
$ROUTER->map("GET", "/unit/edit/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/unit/edit.php");
});
$ROUTER->map("GET", "/unit/download", function () {
  require(__DIR__ . "/src/Views/unit/download.php");
});
$ROUTER->map("POST", "/unit/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/unit/action.php");
});

##################### BRAND #####################
$ROUTER->map("GET", "/brand", function () {
  require(__DIR__ . "/src/Views/brand/index.php");
});
$ROUTER->map("GET", "/brand/create", function () {
  require(__DIR__ . "/src/Views/brand/create.php");
});
$ROUTER->map("GET", "/brand/edit/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/brand/edit.php");
});
$ROUTER->map("GET", "/brand/download", function () {
  require(__DIR__ . "/src/Views/brand/download.php");
});
$ROUTER->map("POST", "/brand/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/brand/action.php");
});


##################### SETTING #####################
$ROUTER->map("GET", "/system", function () {
  require(__DIR__ . "/src/Views/system/index.php");
});
$ROUTER->map("POST", "/system/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/system/action.php");
});

##################### USER #####################
$ROUTER->map("GET", "/user", function () {
  require(__DIR__ . "/src/Views/user/index.php");
});
$ROUTER->map("GET", "/user/create", function () {
  require(__DIR__ . "/src/Views/user/create.php");
});
$ROUTER->map("GET", "/user/profile", function () {
  require(__DIR__ . "/src/Views/user/profile.php");
});
$ROUTER->map("GET", "/user/change", function () {
  require(__DIR__ . "/src/Views/user/change.php");
});
$ROUTER->map("GET", "/user/download", function () {
  require(__DIR__ . "/src/Views/user/download.php");
});
$ROUTER->map("GET", "/user/edit/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/user/edit.php");
});
$ROUTER->map("POST", "/user/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/user/action.php");
});

##################### AUTH #####################
$ROUTER->map("GET", "/", function () {
  require(__DIR__ . "/src/Views/home/login.php");
});
$ROUTER->map("GET", "/home", function () {
  require(__DIR__ . "/src/Views/home/index.php");
});
$ROUTER->map("GET", "/auth", function () {
  require(__DIR__ . "/src/Views/home/auth.php");
});
$ROUTER->map("GET", "/error", function () {
  require(__DIR__ . "/src/Views/home/error.php");
});
$ROUTER->map("POST", "/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/home/action.php");
});
$ROUTER->map("GET", "/[**:params]", function ($params) {
  require(__DIR__ . "/src/Views/home/action.php");
});


$MATCH = $ROUTER->match();

if (is_array($MATCH) && is_callable($MATCH["target"])) {
  call_user_func_array($MATCH["target"], $MATCH["params"]);
} else {
  header("HTTP/1.1 404 Not Found");
  require_once(__DIR__ . "/src/Views/home/error.php");
}