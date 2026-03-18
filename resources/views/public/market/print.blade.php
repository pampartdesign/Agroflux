<h1>Order #{{ $order->id }}</h1>
<p>{{ $order->customer_name }} - {{ $order->customer_email }}</p>
<p>Total: €{{ $order->total }}</p>
<script>window.print()</script>
