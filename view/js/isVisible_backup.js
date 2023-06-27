// SOBRE ESTE ARQUIVO
//// este arquivo deve conter codigos sobre partes visiveis da pagina
//// partes que somente serao vistas se uma condicao for satisfeita
//// - este arquivo controla a visibilidade de elementos HTML do site
/*
//  apenas um comentário: sites que podem ser úteis
    https://www.horadecodar.com.br/2021/10/30/como-detectar-se-a-aba-do-navegador-nao-esta-ativa-com-javascript/
*/


//// NAVEGAÇÃO PELO MENU DO BULMA-CSS EM TELAS PEQUENAS
// https://bulma.io/documentation/components/navbar/#navbar-menu
document.addEventListener('DOMContentLoaded', () => {
  // Get all "navbar-burger" elements
  const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

  // Add a click event on each of them
  $navbarBurgers.forEach( el => {
    el.addEventListener('click', () => {

      // Get the target from the "data-target" attribute
      const target = el.dataset.target;
      const $target = document.getElementById(target);

      // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
      el.classList.toggle('is-active');
      $target.classList.toggle('is-active');

    });
  });
});

// configuração de Modal/Toast do SweetAlert
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
// container/BOX
let container_qrcode = document.getElementById("container_qrcode");
let container_link = document.getElementById("container_link");

// Campo de Input
let input_link = document.getElementById("input_linkCompleto");

// Navegação pelos botões da página
document.querySelectorAll(".paraEstatisticas").forEach(btn => {
    btn.addEventListener("click", (e) => {
        window.location.href = "/links";
    });
});
document.querySelectorAll(".ler_qrcode").forEach(btn => {
    btn.addEventListener("click", (e) => { // console.error("Navegador não compatível com leitura de QRCode/Código de Barras.");

        container_qrcode.style.display = "";
        container_link.style.display = "none";
        if (!('BarcodeDetector' in window)) {
            console.error("\n\nSeu navegador não é compatível com leitura de Código QR usando uma câmera, ou não foram concedias as permissões necessárias para isso. \n\n\n");
        }
    });
});
document.querySelectorAll(".criar_qrLink").forEach(btn => {
    btn.addEventListener("click", (e) => {
        container_qrcode.style.display = "none";
        container_link.style.display = "";
        
        input_link.value = "";
        input_link.focus();
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