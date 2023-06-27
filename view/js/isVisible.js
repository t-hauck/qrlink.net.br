// SOBRE ESTE ARQUIVO
//// este arquivo deve conter codigos sobre partes visiveis da pagina
//// partes que somente serao vistas se uma condicao for satisfeita
//// - este arquivo controla a visibilidade de elementos HTML do site
let token = document.getElementById("formToken");
let links_exportBtn = document.getElementById('export');

// Funções utilizadas pela maioria dos arquivos de script
function changeCursor_POST(type){
    if (type == "wait") {
        document.body.style.cursor = "wait";
    }
    // else if (type == "progress") document.body.style.cursor = "progress"; 
    else if (type == "default") {
        document.body.style.cursor = "default";
    } else console.error("'cursor' não reconhecido");
}

function checkCurrentPage(page, action){ // window.location.pathname.indexOf(page) === 0
    if (page ===  window.location.pathname){ // se a URL começa com o endereço 
        if (action == "reload") setTimeout(() => {
            location.reload();
        }, 1000);

            return true;
    } else  return false;
}


//// NAVEGAÇÃO PELO MENU DO BULMA-CSS EM TELAS PEQUENAS
// https://bulma.io/documentation/components/navbar/#navbar-menu
// Get all "navbar-burger" elements
const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
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
  

////
// apagar/Remover um bloco HTML da tela usando a classe DELETE do Bulma CSS
// esta classe é usada em apenas um lugar, ao abrir a página o JavaScript não conhece esta classe
// por isso, esta função é chamada depois que o conteúdo é colocado na tela
let input_statsResult = document.getElementById("input_statsResult");
function detectDeleteAction(){
    document.querySelectorAll(".delete").forEach(btn => {
        btn.addEventListener("click", (e) => {

            input_statsResult.classList.add("fadeOut"); // input_statsResult.remove();
            setTimeout(() => { input_statsResult.innerHTML = ""; }, 1000);
        });
    });
}

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
    if ( checkCurrentPage("/", "") == false ){ // !checkCurrentPage
        window.location.href = "/?tool=" + urlParam_nav; // Vá para a página principal se não estiver nela
    }

    if (urlParam_nav == "qrscan") {
        container_qrScan.style.display = "";
        container_criarLink.style.display = "none";
        container_criarQRCode.style.display = "none";
        if (!('BarcodeDetector' in window)) console.error("\n\nSeu navegador não é compatível com leitura de Código QR usando uma câmera, ou não foram concedias as permissões necessárias para isso. \n\n\n");
    }
    if (urlParam_nav == "criarlink") {
        container_qrScan.style.display = "none";
        container_criarLink.style.display = "";
        container_criarQRCode.style.display = "none";
        input_link.value = "";       // limpar campo de texto
        input_link_senha.value = ""; // limpar campo de texto
        input_link.focus(); // focalizar campo
    }
    if (urlParam_nav == "criarqrcode") {
        container_qrScan.style.display = "none";
        container_criarLink.style.display = "none";
        container_criarQRCode.style.display = "";
        input_qr.value = ""; // limpar campo de texto
        input_qr.focus(); // focalizar campo
    }
}

// Navegação pelos botões da página usando CLASSES
document.querySelectorAll(".paraEstatisticas").forEach(btn => {
    btn.addEventListener("click", (e) => {
        if ( checkCurrentPage("/links", "") ) return;
        else window.location.href = "/links";
    });
});
document.querySelectorAll(".qrScan").forEach(btn => {
    btn.addEventListener("click", (e) => {
        if ( checkCurrentPage("/", "") ) {
            // console.warn("Você jé está na página principal.");
            container_qrScan.style.display = "";
            container_criarLink.style.display = "none";
            container_criarQRCode.style.display = "none";

            if (!('BarcodeDetector' in window)) console.error("\n\nSeu navegador não é compatível com leitura de Código QR usando uma câmera, ou não foram concedias as permissões necessárias para isso. \n\n\n");
        } else { // o IF acima será executado agora, e verificará esta URL abaixo
            window.location.href = "?tool=qrscan";
        }
    });
});
document.querySelectorAll(".criarLink").forEach(btn => {
    btn.addEventListener("click", (e) => {
        if ( checkCurrentPage("/", "") ) {
            container_qrScan.style.display = "none";
            container_criarLink.style.display = "";
            container_criarQRCode.style.display = "none";
            input_link.value = "";
            input_link_senha.value = "";
            input_link.focus();

        } else { // o IF acima será executado agora, e verificará esta URL abaixo
            window.location.href = "?tool=criarlink";
        }
    });
});
document.querySelectorAll(".criarQRCode").forEach(btn => {
    btn.addEventListener("click", (e) => {
        if ( checkCurrentPage("/", "") ) {
            container_qrScan.style.display = "none";
            container_criarLink.style.display = "none";
            container_criarQRCode.style.display = "";
            input_qr.value = "";
            input_qr.focus();
        } else { // o IF acima será executado agora, e verificará esta URL abaixo
            window.location.href = "?tool=criarqrcode";
        }
    });
});
document.querySelectorAll(".sobreSistema").forEach(btn => {
    btn.addEventListener("click", (e) => {
        if ( checkCurrentPage("/sobre", "") ) return;
        else { // o IF acima será executado agora, e verificará esta URL abaixo
            window.location.href = "/sobre";
        }
    });
});


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
// CÓDIGO UTILIZADO APENAS NA PÁGINA DE ERRO 404
let notFound_div = document.getElementById("404_redirectHome");
if (notFound_div) {
    let countTime = 10;

    setInterval( function(){ 
        countTime--;
        notFound_div.innerHTML = countTime;

        if (countTime == 0) location.replace("/");

    }, 1000)
}
