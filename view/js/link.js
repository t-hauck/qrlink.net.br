let tms1    = 1000; // setTimeout



////
// CÓPIA-COLA DE TEXTO
// https://developer.mozilla.org/en-US/docs/Web/API/Clipboard/readText#browser_compatibility
let input_link = document.getElementById("input_linkCompleto");
let token = document.getElementById("formToken");

let info_textoLido = document.getElementById("info_textoLido");
 
async function clipboard_user(action, text){
    if (!navigator.clipboard) return; // Clipboard API not available

    if (action == "read") { // LEITURA |  APENAS NA PÁGINA PRINCIPAL
        const permissionStatus = await navigator.permissions.query({ name: 'clipboard-read' });
        if (permissionStatus.state === 'granted' || permissionStatus.state === 'prompt') { // denied
            try {
                const clipboardText = await navigator.clipboard.readText();

                info_textoLido.innerText = "Texto da Área de Transferência";
                info_textoLido.classList.add("fadeIn");

                input_link = clipboardText;                
                setTimeout( function(){ input_link.focus(); }, tms1)
                
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
setTimeout( async function(){
    if ( !isAdminPage(0) )  clipboard_user("read", " ");
}, tms1);


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
    return formBody.join("&");
}


function isAdminPage(action){
    if ( window.location.pathname === "/qrlink/admin" ){
        if (action == "reload") setTimeout(() => { location.reload(); }, tms1);
            return true;
    } else  return false;
}










////
// PESQUISAR-LINK | INTERFACE ADMINISTRATIVA
var tabela = document.getElementById('filtro_tabela');
var admin_input_filtro = document.getElementById('pesquisa_input');
var pesquisa_apagarItens = document.getElementById("pesquisa_apagarItens");

if (tabela) {
    admin_input_filtro.addEventListener('input', () => {
        var input_filtro = admin_input_filtro.value;

        for (var i = 1; i < tabela.rows.length; i++) {
            var conteudoCelula = tabela.rows[i].cells[1].innerText; // cells[1] == primeira coluna
            var corresponde = conteudoCelula.toLowerCase().indexOf(input_filtro) >= 0;
            tabela.rows[i].style.display = corresponde ? '' : 'none';

            // icone-APAGAR apenas aparece quando houver texto no campo, e resultados para a pesquisa
            if (input_filtro !== "" && corresponde) {
                    pesquisa_apagarItens.style.display = "block";
            } else  pesquisa_apagarItens.style.display = "none";

            console.log(corresponde);
            
        } // for
    });
}









////
// LINK | NA PÁGINA PRINCIPAL e INTERFACE ADMINISTRATIVA
let submitLink_btn = document.getElementById("submitLink_btn");
submitLink_btn.addEventListener('click', function (){ // ATENÇÃO: este código foi adaptado para ser usado em duas páginas
    if ( isAdminPage(0) ){
        input_link = prompt(`Informe a URL completa:`);
        input = input_link;
        if (input == null || input == undefined || input == "") return;
    } else { input = input_link.value; }

    if (validURL(input) || validURL(input_link)) { // if (input ) { //// para NÃO validar a URL pelo JavaScript, descomente esta linha 

        let _data = {inputURL: input, submitToken: token.value};
        let requestConf = {
            method: "POST",
            body: encodeText(_data), // body: formBody,
            headers: {"Content-type": "application/x-www-form-urlencoded; charset=UTF-8"}
        };
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
                    }, tms1);
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
// APAGAR-LINK | INTERFACE ADMINISTRATIVA
document.querySelectorAll(".apagar_link").forEach(btn => {
    btn.addEventListener("click", (e) => {
        let short_code = btn.getAttribute("item-shortCode");

        if( short_code === "links" ) { // Apagados todos os itens salvos
            msgError = `Erro ao Apagar Todos os registros do banco de dados `;
            let confirm_Delete = prompt(`Esta ação irá excluir TODOS os links salvos. Para confirmar, digite "links".`);
            if (confirm_Delete !== "links") return;
        } else { // Apagado apenas um único link
            msgError = `Erro ao Apagar Link "${short_code}" `;
            let confirm_Delete = prompt(`Para confirmar a exclusão do item, digite seu link curto: `);
            if (confirm_Delete !== short_code) return;
        } ////

        let _data = {short_code: short_code, submitToken: token.value};
        let requestConf = {
            method: "POST", // DELETE
            body: encodeText(_data),
            headers: {"Content-type": "application/x-www-form-urlencoded; charset=UTF-8"},
            ////
            // body: JSON.stringify(_data), // 
            // headers: {"Content-Type": "application/json; charset=UTF-8"},
        };
        fetch( "/link/delete", requestConf )
            .then(response => response.json() )
            .then(response => {

                if (response.status === "delete") isAdminPage("reload");
                // delete == OK/Sucesso //// setTimeout(() => { btn.remove; }, 1000);
            })
        .catch( error => {
            console.error(error);
            alert(msgError);
        });

    });

});









////
// APAGAR-LINKs PELA PESQUISA | INTERFACE ADMINISTRATIVA
if (pesquisa_apagarItens) {
    pesquisa_apagarItens.addEventListener("click", (e) => { // Apagados apenas os itens vísiveis na tela
        msgError = `Erro ao Apagar os itens selecionados do banco de dados `;
        let confirm_Delete = prompt(`Esta ação irá excluir TODOS os links salvos que tenham relação com o texto "${admin_input_filtro.value}". \nPara confirmar, digite novamente o termo pesquisado.`);
        if (confirm_Delete !== admin_input_filtro.value) return;

        let _data = {search: admin_input_filtro.value, submitToken: token.value};
        let requestConf = {
            method: "POST", // DELETE
            body: encodeText(_data),
            headers: {"Content-type": "application/x-www-form-urlencoded; charset=UTF-8"},
        };
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
}
