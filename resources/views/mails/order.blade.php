<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body style="max-width: 720px;width: 100%;margin:auto;margin-bottom: 1em;margin-top: 1em;border: 1px solid #cdcdcd;padding-bottom: 2em;padding-right: 2em;padding-left: 2em;font-family: sans-serif;">
	<div style="text-align: center; padding:2em;">
		<img src="http://api.lokaldatph.com/img/logo-gold.png" style="margin: auto; height: 50px;width: auto;object-fit: contain;">
	</div>
	<div style="width: 100%;text-align: center">
		<span style="font-size: 24px;color:#1682ba">Thank you for shopping!</span>
	</div>
	<div style="width: 100%;text-align: justify;margin-top: 1em;margin-bottom:1em;color:#4a4a4a">
		<span style="font-size: 18px">Hi {{$client_name}} ,</span>
		<p>We received your <strong>{{$order_no}}</strong> on <strong>{{$order_date}}</strong> and you'll be paying for this via <strong>{{$payment_method}}</strong>. We’re getting your order ready and will let you know once it’s on the way. We wish you enjoy shopping with us and hope to see you again real soon!</p>
	</div>
	<div style="height: 3px;background-color: #cdcdcd;margin-top: 1em;margin-bottom: 1em"></div>
	<div style="width: 100%;color:#4a4a4a">
		<img src="http://api.lokaldatph.com/img/pin.png" style="width: 40px; height: 40px; object-fit: contain;margin-top: 1em">
		<span style="font-size: 18px;margin-top: 27px;position: absolute;margin-left: 0.5em;">Your delivery details</span>
		<table width="100%" cellpadding="5" style="margin-top:1em">
			<tr>
				<td width="25%" style="color:#1682ba">Name</td>
				<td>{{$client_name}}</td>
			</tr>
			<tr>
				<td width="25%" style="color:#1682ba">Address</td>
				<td>{{$client_address}}</td>
			</tr>
			<tr>
				<td width="25%" style="color:#1682ba">Contact</td>
				<td>{{$client_contact}}</td>
			</tr>
			<tr>
				<td width="25%" style="color:#1682ba">Email</td>
				<td style="color:blue">{{$client_email}}</td>
			</tr>
		</table>
	</div>
	@foreach($order_data as $order)
	<div style="height: 3px;background-color: #cdcdcd;margin-top: 1em;margin-bottom: 1em"></div>
	<div style="width: 100%;color:#4a4a4a;margin:0.5em">
		<img src="http://api.lokaldatph.com/img/cart.png" style="width: 40px; height: 40px; object-fit: contain;margin-top: 15px">
		<span style="font-size: 18px;margin-top: 27px;position: absolute;margin-left: 0.5em;">Item(s) {{number_format($order['total_qty'], 0)}}</span>
	</div>
	<div style="width: 100%;color:#4a4a4a">
		<span style="">Sold By: {{$order['seller']}}</span>
		<table width="100%" cellpadding="5">
			@foreach($order['items'] as $items)
			<tr>
				<td width="35%">
					<img src="{{$items['product_image']}}" style="width: 200px;height: 200px;object-fit: contain;">
				</td>
				<td style="padding:2em;vertical-align: top">
					<span>{{$items['product_name']}}</span><br>
					<span style="color:red">₱ {{number_format($items['selling_price'], 2)}}</span><br>
					<span>Qty : {{$items['order_qty']}}</span>
				</td>
			</tr>
			@endforeach
		</table>
	</div>
	@endforeach
	<div style="height: 3px;background-color: #cdcdcd;margin-top: 1em;margin-bottom: 15px"></div>
	<div style="width: 100%;color:#4a4a4a">
		<table width="100%" cellpadding="5">
			<tr>
				<td width="75%">Subtotal</td>
				<td width="5%">₱</td>
				<td style="text-align: right">{{number_format($subtotal, 2)}}</td>
			</tr>
			<tr>
				<td width="75%">Shipping Fee</td>
				<td width="5%">₱</td>
				<td style="text-align: right">{{number_format($total_shipping, 2)}}</td>
			</tr>
			<tr>
				<td width="75%">Total Discounts</td>
				<td width="5%">₱</td>
				<td style="text-align: right">{{number_format($total_discount, 2)}}</td>
			</tr>
			<tr>
				<td width="75%">Total (Tax Included)</td>
				<td width="5%">₱</td>
				<td style="text-align: right;background-color: #efc501;color: red">{{number_format($total, 2)}}</td>
			</tr>
		</table>
	</div>
	<div style="height: 1px;background-color: #cdcdcd;margin-top: 1em;margin-bottom: 1em"></div>
	<div style="width: 100%;color:#4a4a4a">
		<table width="100%" cellpadding="5">
			<tr>
				<td width="50%">Courier</td>
				<td style="text-align: right;">{{$courier}}</td>
			</tr>
			<tr>
				<td width="50%">Payment Method</td>
				<td style="text-align: right;">{{$payment_method}}</td>
			</tr>
		</table>
	</div>
</body>
</html>