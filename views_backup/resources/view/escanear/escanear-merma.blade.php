<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Escanear y Registrar Merma</title>
    @vite(['resources/css/app.css'])
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        body { background: #0a0c0c; font-family: 'Inter', sans-serif; margin: 0; padding: 0; }
        #reader { width: 100%; max-width: 500px; margin: 0 auto; border-radius: 20px; overflow: hidden; }
        .result-box { background: rgba(0,0,0,0.7); backdrop-filter: blur(10px); border-radius: 16px; padding: 12px; margin-top: 16px; text-align: center; }
        .btn { background: #e7c095; color: black; font-weight: bold; padding: 12px; border-radius: 40px; width: 100%; border: none; margin-top: 12px; }
        .btn-secondary { background: rgba(255,255,255,0.1); color: white; }
    </style>
</head>
<body class="p-4">
    <div class="max-w-md mx-auto">
        <h1 class="text-2xl font-bold text-[#e7c095] text-center mb-4">Registrar merma rápida</h1>
        <p class="text-gray-400 text-sm text-center mb-6">Escanee el código de barras del producto empaquetado</p>

        <div id="reader"></div>

        <div id="result" class="result-box hidden">
            <div id="message" class="text-white"></div>
            <div class="flex gap-2 mt-3">
                <button id="acceptBtn" class="btn">Registrar (1 ud)</button>
                <button id="cancelBtn" class="btn btn-secondary">Cancelar</button>
            </div>
            <div class="mt-2">
                <label class="text-xs text-gray-400">Cantidad:</label>
                <input type="number" id="cantidad" value="1" min="0.1" step="any" class="w-full bg-white/10 border border-white/20 rounded-lg px-3 py-1 text-white">
            </div>
        </div>

        <button onclick="window.location.href='/inventario'" class="btn-secondary w-full mt-4 py-2 rounded-lg text-center">Volver</button>
    </div>

    <script>
        let html5QrCode;
        let lastScannedCode = null;
        let scannedProductData = null;

        function startScanner() {
            html5QrCode = new Html5Qrcode("reader");
            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 300, height: 300 } },
                (decodedText) => {
                    if (decodedText === lastScannedCode) return;
                    lastScannedCode = decodedText;
                    buscarProducto(decodedText);
                },
                (error) => {}
            ).catch(err => {
                document.getElementById('reader').innerHTML = '<p class="text-red-400">Error al acceder a la cámara. Permita el acceso.</p>';
            });
        }

        function buscarProducto(codigo) {
            fetch(`/buscar-producto?codigo=${encodeURIComponent(codigo)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.id) {
                        scannedProductData = data;
                        document.getElementById('message').innerHTML = `✅ Producto: <strong>${data.nombre}</strong><br>SKU: ${data.sku}`;
                        document.getElementById('result').classList.remove('hidden');
                    } else {
                        document.getElementById('message').innerHTML = '❌ Producto no encontrado. Verifique el código.';
                        document.getElementById('result').classList.remove('hidden');
                        document.getElementById('acceptBtn').style.display = 'none';
                        setTimeout(() => {
                            document.getElementById('result').classList.add('hidden');
                            document.getElementById('acceptBtn').style.display = 'block';
                            lastScannedCode = null;
                            startScanner();
                        }, 2000);
                    }
                })
                .catch(() => {
                    document.getElementById('message').innerHTML = 'Error de conexión.';
                    document.getElementById('result').classList.remove('hidden');
                });
        }

        document.getElementById('acceptBtn').addEventListener('click', () => {
            if (!scannedProductData) return;
            const cantidad = document.getElementById('cantidad').value;
            if (!cantidad || cantidad <= 0) {
                alert('Ingrese una cantidad válida');
                return;
            }
            fetch('/empaquetado/merma', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    producto_id: scannedProductData.id,
                    cantidad: cantidad,
                    causa: 'Sellado defectuoso',
                    lote: '',
                    observaciones: 'Registro rápido por escáner'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Merma registrada correctamente');
                    document.getElementById('result').classList.add('hidden');
                    lastScannedCode = null;
                    scannedProductData = null;
                    document.getElementById('cantidad').value = '1';
                    startScanner();
                } else {
                    alert('Error: ' + (data.message || 'No se pudo registrar'));
                }
            })
            .catch(err => alert('Error al registrar merma'));
        });

        document.getElementById('cancelBtn').addEventListener('click', () => {
            document.getElementById('result').classList.add('hidden');
            lastScannedCode = null;
            scannedProductData = null;
            startScanner();
        });

        startScanner();
    </script>
</body>
</html>