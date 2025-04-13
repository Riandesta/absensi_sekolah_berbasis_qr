<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - {{ $siswaData['nama'] }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 10px;
        }
        .header {
            margin-bottom: 15px;
        }
        .header h2 {
            font-size: 20px;
            margin: 10px 0;
        }
        .qr-container {
            width: 100%;
            text-align: center;
            margin-bottom: 15px;
        }
        .qr-code {
            width: 250px;
            height: 250px;
            margin: 0 auto;
        }
        .qr-code img {
            width: 100%;
            height: auto;
        }
        .employee-info {
            margin-top: 15px;
            font-size: 14px;
        }
        .employee-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
            margin-right: 5px;
        }
        .footer {
            margin-top: 15px;
            font-size: 12px;
            color: #666;
        }
        /* Mobile-specific styles */
        @media print {
            .container {
                padding: 5px;
            }
            .qr-code {
                width: 200px;
                height: 200px;
            }
            .header h2 {
                font-size: 16px;
            }
            .employee-name {
                font-size: 14px;
            }
            .employee-info {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>QR Code Siswa</h2>
        </div>

        <div class="qr-container">
            <div class="qr-code">
                <img src="data:image/svg+xml;base64,{{ $siswaData['qrBase64'] }}" alt="QR Code">
            </div>
        </div>

        <div class="employee-info">
            <div class="employee-name">{{ $siswaData['nama'] }}</div>
            <p><span class="label">NIS:</span> {{ $siswaData['nis'] }}</p>
            <p><span class="label">Kelas:</span> {{ $siswaData['kelas'] }}</p>
        </div>

        <div class="footer">
            <p>Scan QR code untuk absensi siswa</p>
        </div>
    </div>
</body>
</html>
