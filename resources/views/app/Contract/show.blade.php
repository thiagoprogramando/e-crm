<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <title>{{ 'Contrato de '.$contract->sale->customer_name }}</title>
        <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
        <style>
            body {
                font-family: 'Times New Roman', Times, serif;
                background-color: '#F7F7F9';
            }

            .container {
                background-color: #fff !important;
            }

            .floating-button {
                position: fixed;
                bottom: 30px;
                left: 50%;
                transform: translateX(-50%);
                background-color: #007bff;
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease-in-out;
                z-index: 9999;
            }

            canvas {
                width: 100%;
                height: auto;
                display: block;
                background-color: white;
                touch-action: none;
            }
            @media print {
                .floating-button {
                    display: none !important;
                }
            }
        </style>
    </head>
    <body>

        <div class="container mt-5 mb-5 p-5">
            {!! $content  !!}
        </div>
        <div class="text-center">
            @if ($signatureRequired)
                <button id="floatingButton" class="floating-button btn btn-primary">
                    <i class="ri-add-line"></i> Assinar Contrato
                </button>
            @else
                <img src="{{ $contract->signature }}" alt="Assinatura" /> <br> 
                {{ \Carbon\Carbon::parse($contract->signed_at)->format('d/m/Y') }} <br><br>
                <button type="button" onclick="window.print()" class="floating-button btn btn-primary" media="screen">
                    <i class="ri-add-line"></i> Imprimir
                </button>
            @endif
        </div>

        <div class="modal fade" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="signatureModalLabel">Assinatura Digital</h5>
                    </div>
                    <div class="modal-body text-center">
                        <canvas id="signaturePad" width="400" height="200" style="border: 1px solid #000; touch-action: none;"></canvas>
                    </div>
                    <div class="modal-footer btn-group">
                        <button type="button" id="clearSignature" class="btn btn-danger text-light">Limpar</button>
                        <button type="button" id="saveSignature" class="btn btn-primary">Assinar</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="{{ asset('assets/js/jquery.js') }}"></script>
        <script src="{{ asset('assets/js/popper.js') }}"></script>
        <script src="{{ asset('assets/js/sweetalert.js')}}"></script>
        <script src="{{ asset('assets/js/signature_pad.js') }}"></script>

        <script>
            @if(session('error'))
                Swal.fire({
                    title: 'Erro!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    timer: 2000
                })
            @endif

            @if(session('info'))
                Swal.fire({
                    title: 'Atenção!',
                    text: '{{ session('info') }}',
                    icon: 'info',
                    timer: 2000
                })
            @endif
            
            @if(session('success'))
                Swal.fire({
                    title: 'Sucesso!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    timer: 2000
                })
            @endif


            document.addEventListener("DOMContentLoaded", function () {
                var canvas = document.getElementById('signaturePad');
                var ctx = canvas.getContext("2d");
                var isDrawing = false;
                var lastX = 0;
                var lastY = 0;

                function resizeCanvas() {
                    var modalBody = document.querySelector('.modal-body');
                    var width = modalBody.clientWidth - 40;
                    canvas.width = width > 400 ? 400 : width;
                    canvas.height = 200;
                    ctx.fillStyle = "white";
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                }

                document.getElementById('floatingButton').addEventListener('click', function () {
                    var modal = new bootstrap.Modal(document.getElementById('signatureModal'));
                    modal.show();
                });

                document.getElementById('signatureModal').addEventListener('shown.bs.modal', resizeCanvas);

                function startDrawing(e) {
                    isDrawing = true;
                    [lastX, lastY] = [e.offsetX, e.offsetY];
                }

                function draw(e) {
                    if (!isDrawing) return;
                    ctx.beginPath();
                    ctx.moveTo(lastX, lastY);
                    ctx.lineTo(e.offsetX, e.offsetY);
                    ctx.strokeStyle = "#000";
                    ctx.lineWidth = 2;
                    ctx.lineCap = "round";
                    ctx.stroke();
                    [lastX, lastY] = [e.offsetX, e.offsetY];
                }

                function stopDrawing() {
                    isDrawing = false;
                }

                canvas.addEventListener("mousedown", startDrawing);
                canvas.addEventListener("mousemove", draw);
                canvas.addEventListener("mouseup", stopDrawing);
                canvas.addEventListener("mouseout", stopDrawing);
                canvas.addEventListener("touchstart", function (e) {
                    var touch = e.touches[0];
                    var rect = canvas.getBoundingClientRect();
                    lastX = touch.clientX - rect.left;
                    lastY = touch.clientY - rect.top;
                    isDrawing = true;
                });

                canvas.addEventListener("touchmove", function (e) {
                    if (!isDrawing) return;
                    var touch = e.touches[0];
                    var rect = canvas.getBoundingClientRect();
                    var x = touch.clientX - rect.left;
                    var y = touch.clientY - rect.top;

                    ctx.beginPath();
                    ctx.moveTo(lastX, lastY);
                    ctx.lineTo(x, y);
                    ctx.strokeStyle = "#000";
                    ctx.lineWidth = 2;
                    ctx.lineCap = "round";
                    ctx.stroke();
                    [lastX, lastY] = [x, y];

                    e.preventDefault();
                });

                canvas.addEventListener("touchend", function () {
                    isDrawing = false;
                });

                document.getElementById('clearSignature').addEventListener('click', function () {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                });

                document.getElementById('saveSignature').addEventListener('click', function () {
                    if (canvas.toDataURL() === ctx.fillStyle) {
                        Swal.fire({
                            title: 'Atenção!',
                            text: 'É necessário Assinar o Contrato!',
                            icon: 'info',
                            timer: 2000
                        });
                        return;
                    }

                    var signatureData = canvas.toDataURL("image/png");
                    var contractHtml = document.querySelector('.container.mt-5.mb-5.p-5').innerHTML;

                    fetch("{{ route('updated-contract', ['uuid' => $contract->uuid]) }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            signature   : signatureData,
                            content     : contractHtml
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Sucesso!',
                                text: 'Contrato Assinado com sucesso!',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Não foi possível assinar o contrato!',
                                icon: 'info',
                                timer: 5000
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Não foi possível assinar o contrato!',
                            icon: 'info',
                            timer: 5000
                        });
                    });
                });

            });
        </script>
	</body>
</html>