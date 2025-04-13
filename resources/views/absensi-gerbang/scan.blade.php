@extends('templates')
@section('header', 'Scan QR Code Absensi Gerbang')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-light text-dark">
            <h5 class="mb-0">Scan QR Code</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- QR Code Scanner -->
            <div id="qr-reader" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>
            <div id="qr-reader-results" class="mt-3 text-center">
                <div id="loading-indicator" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memproses absensi...</p>
                </div>
            </div>

            <form id="qr-form" action="{{ route('absensi-gerbang.scan-process') }}" method="POST" style="display: none;">
                @csrf
                <input type="hidden" name="qr_code" id="qr-code-input">
            </form>
        </div>
    </div>
</div>

<!-- JavaScript untuk QR Code Scanner -->
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    function onScanSuccess(decodedText, decodedResult) {
        // Stop scanner after successful scan
        html5QrcodeScanner.clear();

        // Display loading indicator
        document.getElementById('loading-indicator').style.display = 'block';
        document.getElementById('qr-reader-results').innerHTML = '<p>QR Code terdeteksi! Memproses...</p>';

        // Set form value and auto-submit
        document.getElementById('qr-code-input').value = decodedText;
        document.getElementById('qr-form').submit();
    }

    function onScanFailure(error) {
        // Handle scan failure (optional)
        console.warn(`Error scanning QR Code: ${error}`);
    }

    const html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader", { fps: 10, qrbox: 250 }
    );
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>
@endsection
