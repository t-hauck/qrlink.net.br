////
// CRIAÇÃO DE QRCODE
function request_criarQR(text){
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

////
// CRIAÇÃO DE QRCODE | PÁGINA DE ESTATISTICAS
let interval_tableQRIcon = setInterval(() => {
    const btn_tableQR = document.querySelectorAll(".qr_shortCode");
    if (btn_tableQR.length > 0) { // verifica se a classe existe no HTML: adicionada por JS via POST 
        
        btn_tableQR.forEach(btn => {
            btn.addEventListener("click", (e) => {
                request_criarQR(btn.getAttribute("short_code"));
            });
        });
    
        clearInterval(interval_tableQRIcon);
    }
}, 1000); // tempo MAIOR ou igual ao do localstorage
