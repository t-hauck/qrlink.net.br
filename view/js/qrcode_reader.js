////
// LEITURA DE QRCODE
var lastResult, countResults = 0;
function onScanSuccess(decodedText, decodedResult) {
    if (decodedText !== lastResult) {
        ++countResults;
        lastResult = decodedText; // console.log(`Scan result ${decodedText}`, decodedResult);

        Swal.fire({
            icon: 'info',
            title: 'Resultado do Scan',
            html: `<a href="${decodedText}" target="_blank"><i>${decodedText}</i></a>`,
            footer: 'QRCode lido usando a biblioteca "html5-qrcode", foi tudo feito no seu navegador e nenhum dado salvo em nossos servidores.',
            confirmButtonText: 'Copiar'
        }).then((result) => {
            if (result.isConfirmed) clipboard_user("copy", decodedText);
        });
    };
}

function onScanFailure(error) { // handle scan failure, usually better to ignore and keep scanning.
    console.error(error);
};

let html5QrcodeScanner = new Html5QrcodeScanner( "qr-reader",
  { fps: 10, qrbox: {width: 250, height: 250} }, /* verbose= */ false);
html5QrcodeScanner.render(onScanSuccess, onScanFailure);
