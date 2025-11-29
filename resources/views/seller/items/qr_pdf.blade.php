<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code for {{ $item->name }}</title>
    <style>
        body { 
            font-family: sans-serif; 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            text-align: center;
        }
        .container {
            border: 2px dashed #ccc;
            padding: 20px;
            display: inline-block;
        }
        h1 {
            font-size: 20px;
            margin-top: 0;
            margin-bottom: 15px;
        }
        p {
            font-size: 12px;
            color: #555;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ $item->name }}</h1>
        <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::format('svg')->size(200)->generate(route('rental.form', $item->unique_code))) }}" alt="QR Code">
        <p>Scan untuk menyewa</p>
    </div>
</body>
</html>

