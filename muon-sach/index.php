<?php
session_start();
require_once("dbcontroller.php");
$db_handle = new DBController();
if(!empty($_GET["action"])) {
switch($_GET["action"]) {
	case "add":
		if(!empty($_POST["quantity"])) {
			$productByCode = $db_handle->runQuery("SELECT * FROM tblproduct WHERE code='" . $_GET["code"] . "'");
			$itemArray = array($productByCode[0]["code"]=>array('name'=>$productByCode[0]["name"], 'code'=>$productByCode[0]["code"], 'quantity'=>$_POST["quantity"], 'price'=>$productByCode[0]["price"], 'image'=>$productByCode[0]["image"]));
			
			if(!empty($_SESSION["cart_item"])) {
				if(in_array($productByCode[0]["code"],array_keys($_SESSION["cart_item"]))) {
					foreach($_SESSION["cart_item"] as $k => $v) {
							if($productByCode[0]["code"] == $k) {
								if(empty($_SESSION["cart_item"][$k]["quantity"])) {
									$_SESSION["cart_item"][$k]["quantity"] = 0;
								}
								$_SESSION["cart_item"][$k]["quantity"] += $_POST["quantity"];
							}
					}
				} else {
					$_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);
				}
			} else {
				$_SESSION["cart_item"] = $itemArray;
			}
		}
	break;
	case "remove":
		if(!empty($_SESSION["cart_item"])) {
			foreach($_SESSION["cart_item"] as $k => $v) {
					if($_GET["code"] == $k)
						unset($_SESSION["cart_item"][$k]);				
					if(empty($_SESSION["cart_item"]))
						unset($_SESSION["cart_item"]);
			}
		}
	break;
	case "empty":
		unset($_SESSION["cart_item"]);
	break;	
}
}
?>
<HTML>
<HEAD>
<TITLE>Simple PHP Shopping Cart</TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link href="http://fonts.googleapis.com/css?family=Varela" rel="stylesheet" />
<link href="/default.css" rel="stylesheet" type="text/css" media="all" />
<link href="/fonts.css" rel="stylesheet" type="text/css" media="all" />
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.2/css/all.css" integrity="sha384-vSIIfh2YWi9wW0r9iZe7RJPrKwp6bG+s9QZMoITbCckVJqGCCRhc+ccxNcdpHuYu" crossorigin="anonymous">
<link href="style.css" type="text/css" rel="stylesheet" />
</HEAD>
<BODY>
	<div id="wrapper">
	<div id="header-wrapper">
	<div id="header" class="container">
		<div id="logo">
				<a href="/"><img src="/logonavbar.png" style="height: 50px"/></a>
		</div>
		<div id="menu">
			<ul>
				<li><a href="#" accesskey="1" title="">Trang chủ</a></li>
				<li><a href="#" accesskey="2" title="">Giới thiệu</a></li>
				<li><a href="#" accesskey="3" title="">Hoạt động</a></li>
				<li><a href="#" accesskey="4" title="">Tuyển dụng</a></li>
				<li><a href="#" accesskey="5" title="">Liên hệ</a></li>
			</ul>
		</div>
	</div>
	</div>
	<div class="container">
<div id="shopping-cart">
<div class="txt-heading">Giỏ hàng</div>

<a id="btnEmpty" href="index.php?action=empty">Làm trống giỏ</a>
<?php
if(isset($_SESSION["cart_item"])){
    $total_quantity = 0;
    $total_price = 0;
?>	
<table class="tbl-cart" cellpadding="10" cellspacing="1">
<tbody>
<tr>
<th style="text-align:left;">Tên sách</th>
<th style="text-align:left;">Mã sách</th>
<th style="text-align:right;" width="10%">Số lượng</th>
<th style="text-align:right;" width="10%">Đơn giá</th>
<th style="text-align:right;" width="10%">Giá</th>
<th style="text-align:center;" width="10%">Xoá bỏ</th>
</tr>	
<?php		
    foreach ($_SESSION["cart_item"] as $item){
        $item_price = $item["quantity"]*$item["price"];
		?>
				<tr>
				<td><img src="<?php echo $item["image"]; ?>" class="cart-item-image" /><?php echo $item["name"]; ?></td>
				<td><?php echo $item["code"]; ?></td>
				<td style="text-align:right;"><?php echo $item["quantity"]; ?></td>
				<td  style="text-align:right;"><?php echo $item["price"]."₫"; ?></td>
				<td  style="text-align:right;"><?php echo number_format($item_price,2)."₫"; ?></td>
				<td style="text-align:center;"><a href="index.php?action=remove&code=<?php echo $item["code"]; ?>" class="btnRemoveAction"><img src="icon-delete.png" alt="Remove Item" /></a></td>
				</tr>
				<?php
				$total_quantity += $item["quantity"];
				$total_price += ($item["price"]*$item["quantity"]);
		}
		?>

<tr>
<td colspan="2" align="right">Tổng cộng:</td>
<td align="right"><?php echo $total_quantity; ?></td>
<td align="right" colspan="2"><strong><?php echo number_format($total_price, 2)."₫"; ?></strong></td>
<td></td>
</tr>
</tbody>
</table>		
  <?php
} else {
?>
<div class="no-records">Giỏ hàng của bạn đang trống</div>
<?php 
}
?>
</div>

<div id="product-grid">
	<div class="txt-heading">Sách</div>
	<?php
	$product_array = $db_handle->runQuery("SELECT * FROM tblproduct ORDER BY id ASC");
	if (!empty($product_array)) { 
		foreach($product_array as $key=>$value){
	?>
		<div class="product-item">
			<form method="post" action="index.php?action=add&code=<?php echo $product_array[$key]["code"]; ?>">
			<div class="product-image"><img src="<?php echo $product_array[$key]["image"]; ?>" style="
    width: 250px;
    height: 155px;
"></div>
			<div class="product-tile-footer">
			<div class="product-title"><?php echo $product_array[$key]["name"]; ?></div>
			<div class="product-price"><?php echo $product_array[$key]["price"]."₫"; ?></div>
			<div class="cart-action"><input type="hidden" class="product-quantity" name="quantity" value="1" size="2" /><input type="submit" value="Thêm vào giỏ hàng" class="btnAddAction" /></div>
			</div>
			</form>
		</div>
	<?php
		}
	}
	?>
</div>
</div>
</div>
<div id="copyright" class="container">
	<p style="color: black">&copy; 2021 Sách và Hành động THPT Chuyên Biên Hoà. Bảo lưu mọi quyền. | Designed by <a href="http://facebook.com/tunnaduong" style="color: black">Dương Tùng Anh</a> | Hosted by <a href="http://fattiesoftware.cóm" rel="nofollow" style="color: black">Fatties Software</a>.</p>
</div>
</BODY>
</HTML>