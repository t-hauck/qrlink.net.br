////
// CRIAÇÃO DE QRCODE
function request_criarQR(text){ // click na classe ".fa-qrcode"  
    changeCursor_POST("wait");
    let requestConf = {
        method: "GET",
        headers: {"Content-type": "application/x-www-form-urlencoded; charset=UTF-8"}
    };

    let QRCode_URL = `https://chart.apis.google.com/chart?chs=500x500&cht=qr&chl=${text}&choe=UTF-8&chld=H`;
    fetch( QRCode_URL, requestConf )
        .then(res => res.blob() )
        .then(response => { // console.log(img);
            var img = URL.createObjectURL(response);    
            Swal.fire({
                title: 'Código QR',
                html: `<a href="${QRCode_URL}" target="_blank">${text}</a>`,
                footer: 'Este código QR foi criado usando o Google Charts, foi tudo feito no seu navegador e nenhum dado salvo em nossos servidores.',
                imageUrl: img,
                imageAlt: text
            });
            changeCursor_POST("default");
        })
    .catch( error => {
        changeCursor_POST("default");
        handleFetchResponse("generic-error", `Ocorreu um erro ao gerar o Código QR.`);
    });
}

////
// CRIAÇÃO DE QRCODE | PÁGINA PRINCIPAL
let submit_criarQRCode = document.getElementById("submit_criarQRCode");
if (submit_criarQRCode) {
    submit_criarQRCode.addEventListener('click', function (){
        if (input_qr.value) request_criarQR(input_qr.value);
        else input_qr.focus();
    });
}
