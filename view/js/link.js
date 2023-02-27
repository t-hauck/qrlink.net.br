function isAdminPage(action){
    if ( window.location.pathname === "/qrlink/admin" ){
        if (action == "reload") setTimeout(() => { location.reload(); }, 1000);
            return true;
    } else  return false;
}

////
// CÓPIA-COLA DE TEXTO
// https://developer.mozilla.org/en-US/docs/Web/API/Clipboard/readText#browser_compatibility
let input_link = document.getElementById("input_linkCompleto");
let token = document.getElementById("formToken");

let info_textoLido = document.getElementById("info_textoLido");
 
async function clipboard_user(action, text){
    if (!navigator.clipboard) return; // Clipboard API not available

    if (action == "read") { // LEITURA | [uma] APENAS NA PÁGINA PRINCIPAL
        const permissionStatus = await navigator.permissions.query({ name: 'clipboard-read' });
        if (permissionStatus.state === 'granted' || permissionStatus.state === 'prompt') { // denied
            try {
                const clipboardText = await navigator.clipboard.readText();

                info_textoLido.innerText = "Texto da Área de Transferência";
                info_textoLido.classList.add("fadeIn");

                input = clipboardText;                
                setTimeout( function(){ input_link.focus(); }, 1500)
                
            } catch (err) {
                console.error('Falha ao ler o conteúdo da área de transferência. \n', '=>           ', err);
            }
        } // else console.error("Permissão Negada para acessar o conteúdo da área de transferência.");

        //permissionStatus.onchange = () => { // Escuta mudanças ao estado de permissão
        //    console.log(permissionStatus.state);
        //};
    }
    else if (action == "copy" && text !== ""){ // CÓPIA-ESCRITA | [todas] NA PÁGINA PRINCIPAL e INTERFACE ADMINISTRATIVA
        try {
            await navigator.clipboard.writeText(text); // input_link.textContent = 'Copied to clipboard';
            return true;
        } catch (err) { // console.error('Falha ao copiar o texto para a área de transferência. \n', err);
            console.error(err);
            return false;
        }
    }
}


////
// Execução direta do código no carregamento da página
// window.addEventListener("DOMContentLoaded", function(){ oi }); 
setTimeout( async function(){ // LEITURA | [uma] APENAS NA PÁGINA PRINCIPAL
    if ( !isAdminPage(0) ){
        clipboard_user("read", " ");
    }
}, 1000) // 1500


////
// Função para validação de texto, se é um link válido ou não
function validURL(str) {
    var pattern = new RegExp('^(https?:\\/\\/)?'+           // protocol
      '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+   // domain name
      '((\\d{1,3}\\.){3}\\d{1,3}))'+                        // OR ip (v4) address
      '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+                    // port and path
      '(\\?[;&a-z\\d%_.~+=-]*)?'+                           // query string
      '(\\#[-a-z\\d_]*)?$','i');                            // fragment locator
    return pattern.test(str);
}


function encodeText(data){
    var formBody = [];
    for (var property in data) {
        var encodedKey = encodeURIComponent(property);
        var encodedValue = encodeURIComponent(data[property]);
        formBody.push(encodedKey + "=" + encodedValue);
    }
    // formBody = formBody.join("&");
    return formBody.join("&");
}

////
// LINK | [todas] NA PÁGINA PRINCIPAL e INTERFACE ADMINISTRATIVA
let btn_submit = document.getElementById("btn_submit");
btn_submit.addEventListener('click', function (){ // ATENÇÃO: este código foi adaptado para ser usado em duas páginas
    if ( isAdminPage(0) ){
        input_link = prompt(`Informe a URL completa:`);
        input = input_link;
        if (input == null || input == undefined || input == "") return;
    } else { input = input_link.value; }

    if (validURL(input) || validURL(input_link)) { // if (input ) { //// para NÃO validar a URL pelo JavaScript, descomente esta linha 

        let _data = {
            inputURL: input,
            submitToken: token.value
        }
        let requestConf = {
            method: "POST",
            body: encodeText(_data), // body: formBody,
            headers: {"Content-type": "application/x-www-form-urlencoded; charset=UTF-8"}
        }
        fetch( "/link/save", requestConf ) // fetch( window.location.host,
            .then(res => res.json() )
            .then(res => { // console.log(res[0]);
            
                // res[0].forEach(function(item){
                    let link_completo = input; // window.location.href === ... /qrlink/admin
                    let link_curto = window.location.origin + "/" + res.short_code;

                    input = link_curto;

                    let cp_text = clipboard_user("copy", link_curto); 

                    setTimeout(() => {
                        userReturn = "Link informado: " + link_completo + "\nLink curto: " + link_curto;
                        if ( cp_text ) {
                            if (info_textoLido) info_textoLido.style.display = "none";
                            userReturn += "\n\nO link curto foi copiado para a área de transferência, cole o texto em algum lugar.";
                            alert(userReturn);
                        } else {
                            alert(userReturn);
                        }

                        isAdminPage("reload");
                    }, 2000);
                // }); // forEach
            })
        .catch( error => { // alert("Erro ao cadastrar Link");
            console.error(error);
        });
        
    } else {
        alert(`Informe um link na caixa de texto e selecione uma das duas opções 'Encurtar Link' ou 'QRCode'. \nUm endereço válido pode começar com "http://" "https://"
                \nExperimente!
        `);
    }
});









////
// APAGAR-LINK | [uma] APENAS INTERFACE ADMINISTRATIVA
document.querySelectorAll(".apagar_link").forEach(btn => {
    btn.addEventListener("click", (e) => {
        let short_code = btn.getAttribute("item-shortCode");

        if( short_code === "links" ) {
            msgError = `Erro ao Apagar Todos os registros do banco de dados `;
            let confirm_Delete = prompt(`Esta ação irá excluir TODOS os links salvos. Para confirmar, digite "links".`);
            if (confirm_Delete !== "links") return;
        } else {
            msgError = `Erro ao Apagar Link "${short_code}" `;
            let confirm_Delete = prompt(`Para confirmar a exclusão do item, digite seu link curto: `);
            if (confirm_Delete !== short_code) return;
        }
        ////

        let _data = {
            short_code: short_code, submitToken: token.value
        }
        let requestConf = {
            method: "POST", // DELETE
            body: encodeText(_data),
            headers: {"Content-type": "application/x-www-form-urlencoded; charset=UTF-8"}
            ////
            // body: JSON.stringify(_data), // 
            // headers: {"Content-Type": "application/json; charset=UTF-8"}
        }
        fetch( "/link/delete", requestConf )
            .then(response => response.json() )
            .then(response => {

                if (response.status === "delete") isAdminPage("reload");
                // delete == OK/Sucesso
            })
        .catch( error => {
            console.error(error);
            alert(msgError);
        });

    });
    
});
