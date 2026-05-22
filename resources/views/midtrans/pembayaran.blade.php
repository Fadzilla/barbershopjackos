<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" 
            data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
</head>
<body>
    <script type="text/javascript">
        window.onload = function() {
            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) { window.location.href = '/admin/pendapatans'; },
                onPending: function(result) { alert("Menunggu pembayaran..."); },
                onError: function(result) { alert("Pembayaran gagal!"); },
                onClose: function() { window.location.href = '/admin/pendapatans'; }
            });
        };
    </script>
</body>
</html>