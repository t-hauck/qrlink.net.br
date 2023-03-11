////
// QRCODE
//
//// Criador de qrcode usando o Google Charts
//function GerarQRCode(){
//    var inputUsuario = document.querySelector('textarea').value;
//    var GoogleChartAPI = 'https://chart.googleapis.com/chart?cht=qr&chs=500x500&chld=H&chl=';
//    var conteudoQRCode = GoogleChartAPI + encodeURIComponent(inputUsuario);
//    document.querySelector('#QRCodeImage').src = conteudoQRCode;
//}
if (!('BarcodeDetector' in window)) console.error("Navegador não compatível com leitura de QRCode/Código de Barras.");

var resultContainer = document.getElementById('qr-reader-results');
var lastResult, countResults = 0;

function onScanSuccess(decodedText, decodedResult) {
    if (decodedText !== lastResult) {
        ++countResults;
        lastResult = decodedText;
        // console.log(`Scan result ${decodedText}`, decodedResult);
        alert(`Texto escaneado do QRCode: \n${decodedText} \n\nAnote este texto em algum lugar, ele não irá aparecer novamente.`)
    
    }
}

var html5QrcodeScanner = new Html5QrcodeScanner(
    "qr-reader", { fps: 10, qrbox: 250 });
html5QrcodeScanner.render(onScanSuccess);
