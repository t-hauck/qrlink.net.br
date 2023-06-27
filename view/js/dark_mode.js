function qrLinkColor() {
  var html = document.querySelector("html");
  var localStorage_dark = localStorage.getItem('darkMode');

  if (localStorage_dark === 'true' || (localStorage_dark === null && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    html.classList.add('dark_mode');
  } else {
    html.classList.remove('dark_mode');
  }
}

let localStorage_dark = localStorage.getItem('darkMode');

const themeColor_btn = document.getElementById("dark");
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

if (window.matchMedia) { // Alterar cores verificando a preferência do navegador
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    qrLinkColor();
  });
}

// executar a função ao carregar a página
qrLinkColor();
