// input_qrcode = document.getElementById("");

// Função para gerar uma string aleatória
let tamanho_string = 3;
function geraStringAleatoria(tamanho) { // console.log(geraStringAleatoria(8)); // PQjN0tnQ
    var stringAleatoria = '';
    var caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    for (var i = 0; i < tamanho; i++) {
        stringAleatoria += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
    }
    return stringAleatoria;
} let link_curto = geraStringAleatoria(tamanho_string);



import {
    onGetLinks,
    saveLink,
    getLink,
    getLinks,
  } from "./firebase.js";
  
  let link_completo = document.getElementById("link_inputURL");
  let link_curtoCriado = document.getElementById("link_URLmenor_gerada");
  let linkCurto_FQDN =  window.location.href + "?" + link_curto;

  let btn_copiarURL = document.getElementById("btn_copiarURL");
  let btn_diminuirLink = document.getElementById("btn_linkMenor");
  
  window.addEventListener("DOMContentLoaded", async (e) => { // load
/*  const querySnapshot = await getLinks();
    querySnapshot.forEach((doc) => {
      console.log(doc.data());
    });
    */
    // Pega o número total de dados salvos no banco
    onGetLinks((querySnapshot) => { // console.log(querySnapshot.size);
      
      let link_QTDdadoSalvos = document.getElementById("link_mostrarQTD_dadoSalvos");
      link_QTDdadoSalvos.style = "display: block;";

      link_QTDdadoSalvos.innerHTML = "<h1>" + querySnapshot.size + "</h1> <p>links salvos</p>";
    }) // onGetLinks

  }); // window.addEventListener
  

// Cadastrar novo link
  btn_diminuirLink.addEventListener("click", async (e) => {
    console.log("\
link_completo: ", link_completo.value + "\n\
link_curto: " + link_curto + "\n\n" + linkCurto_FQDN);
    
      e.preventDefault();
    
      try {
        await saveLink(link_completo.value, link_curto);

        link_curtoCriado.innerHTML = linkCurto_FQDN; 
        link_curtoCriado.style = "display: block;";

        link_completo.style = "display: none;";
        btn_copiarURL.style = "display: block;";
    
      } catch (error) {
        console.log(error);
      }
    });

    