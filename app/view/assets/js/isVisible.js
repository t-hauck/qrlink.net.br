let token = document.getElementById("formToken"); // CSRF
let input_statsResult = document.getElementById("input_statsResult"); // /links

function changeCursor_POST(type){
    if (type == "wait") {
        document.body.style.cursor = "wait";
    }
    // else if (type == "progress") document.body.style.cursor = "progress"; 
    else if (type == "default") {
        document.body.style.cursor = "default";
    } else console.error("'cursor' não reconhecido");
}

function checkCurrentPage(page){
    if (page ===  window.location.pathname){ // window.location.pathname.indexOf(page) === 0 // => se a URL começa com o endereço
            return true;
    } else  return false;
}

//// BULMA-CSS
// Navegação pelo menu do Bulma em telas pequenas
const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
if ($navbarBurgers.length > 0) { // Check if there are any navbar burgers
    $navbarBurgers.forEach( el => { // Add a click event on each of them
        el.addEventListener('click', () => {

        // Get the target from the "data-target" attribute
        const target = el.dataset.target;
        const $target = document.getElementById(target);

        // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
        el.classList.toggle('is-active');
        $target.classList.toggle('is-active');

        });
    });
}

// Troca de conteúdo com as "tabs" do Bulma
// function openTab(evt, tabName) {
//   var i, x, tablinks;
//   x = document.getElementsByClassName("content-tab");
//   for (i = 0; i < x.length; i++) {
//       x[i].style.display = "none";
//   }
//   tablinks = document.getElementsByClassName("tab");
//   for (i = 0; i < x.length; i++) {
//       tablinks[i].className = tablinks[i].className.replace(" is-active", "");
//   }
//   document.getElementById(tabName).style.display = "block";
//   evt.currentTarget.className += " is-active";
// }

//   tabs_content_about
  
//// SweetAlert
// configuração do script de Modal/Toast
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 5000, // 5 Segundos
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

////
// NAVEGAÇÃO PELO SITE
// containers HTML das ferramentas
let container_qrScan = document.getElementById("container_qrScan");
let container_criarLink = document.getElementById("container_criarLink");
let container_criarQRCode = document.getElementById("container_criarQRCode");

// Campo de Input utilizado apenas na página de redireionamento de links com senha
let input_link_senha = document.getElementById("input_linkSenha");

// Campos de Input utilizados apenas na página principal
let input_link = document.getElementById("input_linkCompleto");
let input_qr = document.getElementById("input_qrOriginal");

// Navegação por queryString ao abrir a página
let urlParams = new URLSearchParams(window.location.search);
let urlParam_nav = urlParams.get("tool");


if ( urlParam_nav && urlParam_nav != "" ){
        if (urlParam_nav == "qrscan") {
            if ( checkCurrentPage("/") ) {
                container_qrScan.style.display = "";
                container_criarLink.style.display = "none";
                container_criarQRCode.style.display = "none";
                if (!('BarcodeDetector' in window)) console.error("\n\nSeu navegador não é compatível com leitura de Código QR usando uma câmera, ou não foram concedias as permissões necessárias para isso. \n\n\n");
            } else window.location.href = "/?tool=" + urlParam_nav;
        }
        else if (urlParam_nav == "criarlink") {
            if ( checkCurrentPage("/") ) {
                container_qrScan.style.display = "none";
                container_criarLink.style.display = "";
                container_criarQRCode.style.display = "none";
                input_link.value = "";       // limpar campo de texto
                input_link_senha.value = ""; // limpar campo de texto
                input_link.focus(); // focalizar campo
            } else window.location.href = "/?tool=" + urlParam_nav;
        }
        else if (urlParam_nav == "criarqrcode") {
            if ( checkCurrentPage("/") ) {
                container_qrScan.style.display = "none";
                container_criarLink.style.display = "none";
                container_criarQRCode.style.display = "";
                input_qr.value = ""; // limpar campo de texto
                input_qr.focus(); // focalizar campo
            } else window.location.href = "/?tool=" + urlParam_nav;
        }
        else{   // ?tool=nao_identificado
            if ( checkCurrentPage("/") ) container_criarLink.style.display = "";
        }
}else {         // ?tool=
    if ( checkCurrentPage("/") ) container_criarLink.style.display = "";
}

////
// Detectar click em qualquer local do site
document.body.addEventListener("click", (e) => {
    element = e.target;

    // Remover bloco de informações sobre um link = /links
    if (element.classList.contains("delete")) {
        input_statsResult.innerHTML = "";
        input_statsResult.classList.remove("fadeIn_toBottom");
        // input_statsResult.classList.add("fadeOut_toTop");
        // setTimeout( function(){ input_statsResult.innerHTML = ""; }, 1000) // input_statsResult.remove();
    }

    // Criação de QRCode pela tabela da página = /links
    if (element.classList.contains("fa-qrcode")){
        var qrURL = element.getAttribute("createQRCode_ShortURL");
        request_criarQR(qrURL);
    }

    // Apagar um único link pela tabela da página = /admin
    if (element.classList.contains("apagar_link")){
        request_deleteSingleLink(element);
    }

    // Navegação pelos botões da página usando CLASSES
    if (element.classList.contains("paraEstatisticas") || element.closest(".paraEstatisticas")) {
        if ( checkCurrentPage("/links") ) return;
        else window.location.href = "/links";
    }
    if (element.classList.contains("qrScan") || element.closest(".qrScan")) {
        if ( checkCurrentPage("/") ) { // console.warn("Você jé está na página principal.");
            container_qrScan.style.display = "";
            container_criarLink.style.display = "none";
            container_criarQRCode.style.display = "none";
            window.location.href = "#container_qrScan"; // ID HTML, focalizar elemento

            if (!('BarcodeDetector' in window)) console.error("\n\nSeu navegador não é compatível com leitura de Código QR usando uma câmera, ou não foram concedias as permissões necessárias para isso. \n\n\n");
        } else {
            window.location.href = "/?tool=qrscan";
        }
    }
    if (element.classList.contains("criarLink") || element.closest(".criarLink")) {
        if ( checkCurrentPage("/") ) {
            container_qrScan.style.display = "none";
            container_criarLink.style.display = "";
            container_criarQRCode.style.display = "none";
            input_link.value = "";
            input_link_senha.value = "";
            input_link.focus();
        } else {
            window.location.href = "/?tool=criarlink";
        }
    }
    if (element.classList.contains("criarQRCode") || element.closest(".criarQRCode")) {
        if ( checkCurrentPage("/") ) {
            container_qrScan.style.display = "none";
            container_criarLink.style.display = "none";
            container_criarQRCode.style.display = "";
            input_qr.value = "";
            input_qr.focus();
        } else {
            window.location.href = "/?tool=criarqrcode";
        }
    }
    if (element.classList.contains("sobreSistema") || element.closest(".sobreSistema")) {
        if ( checkCurrentPage("/sobre") ) return;
        else { window.location.href = "/sobre"; }
    }
}); // document.body.addEventListener("click")


//// Detectar se o navegador suporta Touch, usado pela tag HTML <abbr>
// https://htmldom.dev/detect-mobile-browsers/
function isMobile() {
  const match = window.matchMedia('(pointer:coarse)');
  return match.matches;
};
document.querySelectorAll(".info_Touch").forEach(btn => {
    btn.addEventListener("click", (e) => {
        if (isMobile()) {
            Swal.fire({
                icon: 'info',
                html: 'LocalStorage é um pequeno espaço de armazenamento de dados dentro do seu navegador web. Ele permite salvar informações, como preferências do usuário e favoritos. Os dados armazenados nele estão sob total controle seu. <br><br>Se você apagar os dados do LocalStorage, o sistema QR-Link não poderá identificar os links que você encurtou para mostrar dados sobre eles. O QR-Link não possui login e cadastro de conta.'
                // footer: '<a href="">Why do I have this issue?</a>'
            })
        } /// else console.log("não suporta touch");
    });
});









////
// Página de Redirecionamento de Links com Senha: ocultar/Exibir a senha 
let showPasswordLink = document.getElementById('showPassword');
let showPasswordIcon = document.getElementById('showPassword_icon');
function change_PassIcon(action){
    let placeholder_pass = input_link_senha.placeholder;
    if (action === 'show') { // (input_link_senha.type === 'password')
        input_link_senha.type = 'text';
        input_link_senha.placeholder = "insira sua senha aqui";
        showPasswordIcon.classList.remove('fa-eye');
        showPasswordIcon.classList.add('fa-eye-slash');
    } else {
        input_link_senha.type = 'password';
        input_link_senha.placeholder = placeholder_pass; // de volta ao valor padrão do HTML
        showPasswordIcon.classList.remove('fa-eye-slash');
        showPasswordIcon.classList.add('fa-eye');
    }
}
if (showPasswordLink) {
    showPasswordLink.addEventListener('click', function () {
        change_PassIcon();
    });
    showPasswordLink.addEventListener('mousedown', function() {
        change_PassIcon("show");
    });
    showPasswordLink.addEventListener('mouseup', function() {
        change_PassIcon();
    });
    showPasswordLink.addEventListener('mousemove', function() {
        change_PassIcon();
    });
}









////
// CÓDIGO UTILIZADO APENAS NA PÁGINA DE ERRO 404 e outros status Http
let notFound_div = document.getElementById("HttpStatus_countdown");
if (notFound_div) {
    let countTime = 10;

    setInterval( function(){ 
        countTime--;
        notFound_div.innerHTML = countTime;
        if (countTime == 0) location.replace("/");

    }, 1000)
}
