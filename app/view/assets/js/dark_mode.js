const themeColor_btn = document.getElementById("dark");
const themeColor_btnIcon = document.querySelector("#dark i");
let localStorage_dark = localStorage.getItem("darkMode");

function qrLinkColor_page(){
  if ( window.location.pathname.includes("admin") ){ // NÃO trocar cores nestas paginas
    return true;  }
    return false;
}

function qrLinkColor() {
  var html = document.querySelector("html");
  var localStorage_dark = localStorage.getItem('darkMode');

  if (localStorage_dark === 'true' || (localStorage_dark === null && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    html.classList.add('dark_mode');
    themeColor_btnIcon.classList.add("fa-sun");
    themeColor_btnIcon.classList.remove("fa-moon");
  } else {
    html.classList.remove('dark_mode');
    themeColor_btnIcon.classList.add("fa-moon");
    themeColor_btnIcon.classList.remove("fa-sun");
  }
}

if (qrLinkColor_page() === false) {
  if (themeColor_btn) {
    themeColor_btn.classList.toggle('active', localStorage_dark === 'true');

    themeColor_btn.addEventListener("click", function() {
      const isDarkMode = localStorage_dark !== 'true';
      localStorage.setItem("darkMode", isDarkMode);
      localStorage_dark = isDarkMode ? 'true' : 'false';
      qrLinkColor();

      themeColor_btn.classList.toggle('active', isDarkMode);
    });
  }

  if (window.matchMedia) { // alterar cores verificando a preferência do navegador
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
      qrLinkColor();
    });
  }

  // executar a função ao carregar a página
  qrLinkColor();
}else {
  if (themeColor_btn){
    themeColor_btnIcon.classList.add("fa-moon");
    themeColor_btn.addEventListener("click", function() {

      Toast.fire({
          icon: "error",
          title: "Admin: troca de cores desativada",
          position: "bottom",
          width: 410
      });
    });
  }
}
