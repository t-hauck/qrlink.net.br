//// Navegação

// container/BOX
let container_qrcode = document.getElementById("container_qrcode");
let container_link = document.getElementById("container_link");

// Botões do Menu
let click_qrcode = document.getElementById("menuBtn_qrcode");
let click_link = document.getElementById("menuBtn_link");


// NÃO IMPORTA em qual botão clicar, será alternado entre o HTML ocultando o que estiver vísivel
function hide(html, newDisplay) {
    if (html.style.display ===   "none") {
            html.style.display = newDisplay;    // block
    } else  html.style.display = "none";
}

click_qrcode.addEventListener("click", (e) => {
    hide(container_qrcode, "flex");
    hide(container_link, "flex");
});
click_link.addEventListener("click", (e) => {
    hide(container_qrcode, "flex");
    hide(container_link, "flex");
});
