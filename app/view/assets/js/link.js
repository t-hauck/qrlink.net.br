////
// Verificar se URL é válida antes de enviar ao servidor
const isValidUrl = (string) => {
    var a = document.createElement("a");
    a.href = string;
    return a.host && a.host != window.location.host;
};


////
// CÓPIA-COLA DE TEXTO
// https://developer.mozilla.org/en-US/docs/Web/API/Clipboard/readText#browser_compatibility
async function clipboard_user(action, text){
    // Navegador Firefox não permite ler o a área de transferência, o IF abaixo verifica apenas se o navegador SUPORTA esta API e o Firefox irá passar nesta validação.
    if (!navigator.clipboard) return; // Clipboard API not available

    if (action == "read") { // LEITURA |  APENAS NA PÁGINA PRINCIPAL
        const permissionStatus = await navigator.permissions.query({ name: 'clipboard-read' });
        if (permissionStatus.state === 'granted' || permissionStatus.state === 'prompt') { // denied
            try {
                const clipboardText = await navigator.clipboard.readText();
                if (clipboardText && isValidUrl(clipboardText)){

                    Toast.fire({
                        icon: 'info', title: 'Texto copiado da área de transferência'
                    });

                    setTimeout( function(){
                        input_link.value = clipboardText;
                        // input_link.focus(); // sem efeito, HTML tem o atributo autofocus
                    }, 1000)
                }
            
            } catch (err) {
                console.error("Falha ao ler o conteúdo da área de transferência. \n", "=>           ", err);
            }
        } else console.error("Permissão Negada para acessar o conteúdo da área de transferência.");

        // Escuta mudanças ao estado de permissão
        //permissionStatus.onchange = () => { console.log(permissionStatus.state); };
    }
    else if (action == "copy" && text !== ""){ // CÓPIA-ESCRITA
        try {
            await navigator.clipboard.writeText(text); // input_link.textContent = 'Copied to clipboard';
            return true;
        } catch (err) {
            console.error("Falha ao copiar o texto para a área de transferência. \n", "=>           ", err);
            return false;
        }
    }
}
// Leitura da área de transferência do usuário ao abrir a página
setTimeout( async function(){
    if ( checkCurrentPage("/") ) clipboard_user("read", " ");
}, 1000);









////
// ENCURTAMENTO DE LINKS
let submit_criarLink = document.getElementById("submit_criarLink");
if (submit_criarLink) {
    submit_criarLink.addEventListener('click', function (){
        if ( checkCurrentPage("/admin") ){
            input_link = prompt(`Informe a URL completa:`);
            input_lnk = input_link; // para pegar texto do "prompt"
            input_lnk_senha = ""; //// a página ADMIN não tem campo para salvar senha
            if (input_lnk == null || input_lnk == undefined || input_lnk == "") return;
        } else {
            input_lnk = input_link.value;
            input_lnk_senha = input_link_senha.value;
        }

        if (input_lnk) { // verifica se foi digitado algo
            if (/^\s*$/.test(input_lnk)){ // ignorar campo apenas com espaços em branco
                input_link.focus();
                return;
            }
            if (!isValidUrl(input_lnk)){ // ignorar URL inválida
                if ( !checkCurrentPage("/admin") ){
                    input_link.style.border = "1px solid";
                    input_link.style.borderColor = "red";
                    input_link.focus();
                }
                Toast.fire({ icon: 'error', title: 'URL Inválida'  });
                return;
            }

            changeCursor_POST("wait");

            let _data = { // submitToken: token.value
                linkURL: input_lnk,
                linkPasswd: input_lnk_senha,
            };
            let requestConf = {
                method: "POST",
                body: JSON.stringify(_data),
                headers: {
                    "CSRF-Token": token.value,
                    "Content-Type": "application/json; charset=UTF-8"
                }
            };
            fetch("/save", requestConf) // fetch( window.location.host,
                .then(handleFetchResponse())
                .then((res) => {

                    handleFetchResponse();

                    localStorage_link_save({"short_code": res.short_code, "original_url": input_lnk});

                        // res[0].forEach(function(item){
                        let link_curto = window.location.origin + "/" + res.short_code;
                        let cp_text = clipboard_user("copy", link_curto);
            
                            userReturn = "Compartilhe este endereço: <br><b>" + link_curto + "</b>";

                            if (cp_text) { // + (se um texto foi informado no campo de *SENHA* MOSTRE um aviso sobre a senha na tela; senão coloque uma string vazia no lugar)
                                userReturn += "<br><br>O link curto foi copiado, cole o texto em algum lugar." + ( input_lnk_senha ? "<br><br> Caso a URL seja nova no sistema, a <span style='color:red;'>senha</span> escolhida será necessária para todos os acessos ao link curto " + res.short_code + "." : "");
                                Swal.fire({
                                    title: "Link Encurtado!",
                                    html: userReturn,
                                    icon: "success",
                                });
                            } else { // alert(userReturn);
                                Swal.fire({
                                    title: "Link Encurtado!",
                                    html: userReturn,
                                    icon: "success",
                                });
                            }
                        // }); // forEach

                changeCursor_POST("default");

                if ( checkCurrentPage("/admin")){
                    admin_RequestData();
                    toTableHead();
                }

                hideCol_table();            // ocultar colunas seguindo preferência do usuario
            })
            .catch( error => {
                changeCursor_POST("default");
                handleFetchResponse("generic-error", `Ocorreu um erro ao encurtar o seu link.`);
            });
        } else {
            input_link.focus();
        }

        document.body.style.cursor = "default";
    }); // addEventListener
}









////
// REDIRECIONAMENTO DE LINK PROTEGIDO POR SENHA
let submit_checarSenha = document.getElementById("submit_checarSenha");
if (submit_checarSenha) {

    submit_checarSenha.addEventListener("click", evento => {
        evento.preventDefault();

        input_lnk_senha = input_link_senha.value; // existe apenas um ID para input de SENHA
        if (input_lnk_senha) { // verifica se foi digitado algo
            changeCursor_POST("wait");

            let _data = { linkPasswd: input_lnk_senha };
            let requestConf = {
                method: "POST",
                body: JSON.stringify(_data),
                headers: {
                    "CSRF-Token": token.value,
                    "Content-Type": "application/json; charset=UTF-8"
                }
            };
            
            fetch(`/checkPassword${window.location.pathname}`, requestConf)
                .then(handleFetchResponse())
                .then((res) => { // console.log(res[0]);

                    handleFetchResponse();

                    // o JAVASCRIPT redireciona o usuario
                    window.location.href = res.original_url;
                    
                    changeCursor_POST("default");
            })
            .catch( error => {
                changeCursor_POST("default");
                handleFetchResponse("generic-error", `Ocorreu um erro ao verificar a senha.`);
            });

        } else {
            input_link_senha.focus();
        }
    }); // addEventListener
}









////
// ESTATÍSTICAS
let input_linkEstatisticas = document.getElementById("input_linkEstatisticas");
let submit_obterEstatisticas = document.getElementById("submit_obterEstatisticas");

if (submit_obterEstatisticas) {
    submit_obterEstatisticas.addEventListener('click', function (){
        input = input_linkEstatisticas.value;

        if (input) {
            if (/^\s*$/.test(input)){ // ignorar campo apenas com espaços em branco
                input_linkEstatisticas.focus();
                return;
            }

            changeCursor_POST("wait");

            let _data = { linkCode: input };
            let requestConf = {
                method: "POST",
                body: JSON.stringify(_data),
                headers: {
                    "CSRF-Token": token.value,
                    "Content-Type": "application/json; charset=UTF-8"
                }
            };
            
            fetch("/getStats", requestConf)
            .then(handleFetchResponse()) // .then((res) => res.json())
            .then((res) => {
                
                handleFetchResponse();
                
                // continua.. EM CASO DE SUCESSO, O LINK EXISTE NO BANCO DE DADOS
                res = res[0];
                let link_curto = window.location.origin + "/" + res.short_code;

                if (res.short_code_password === null) {
                        var hasPassword = false;
                }else if (res.short_code_password === "-") {
                    var hasPassword = true;
                }else {
                    var hasPassword = false;
                }
                
                // Animação 'fade' no HTML
                if (input_statsResult.querySelector("article")) { // DIV Html contém o conteúdo

                    input_statsResult.classList.remove("fadeIn_toBottom");
                    input_statsResult.classList.add("fadeOut");

                    setTimeout( function(){
                        input_statsResult.classList.remove("fadeOut");
                        input_statsResult.classList.add("fadeIn");
                    }, 500);
                } else{
                    input_statsResult.classList.add("fadeIn_toBottom");
                }

                // Adicionar HTML na tela
                input_statsResult.innerHTML = `
                    <article class="message is-link">
                        <div class="message-header">
                            <p>
                                <a style="text-decoration:none;" href="${link_curto}" target="_blank">${link_curto}</a>
                            </p>
                            <button class="delete" aria-label="delete"></button>
                        </div>

                        <div class="message-body">
                            Link Original: &emsp;             <a style="text-decoration:none;" href="${res.url}" target="_blank">${res.url}</a>
                            <br>
                            Código Curto: &nbsp;&nbsp;&nbsp;  <a href="${link_curto}" target="_blank">${res.short_code}</a>
                            <br>
                            Número de Acessos: &nbsp; <b>${res.access}</b>
                            <br>
                            Último Acesso: &nbsp; ${ // NULL = sem acessos                      // - = link do próprio sistema
                            res.last_access === null ? "-" : (res.last_access == "-" ? res.last_access : convertDateTime(res.last_access) ) }

                            ${  // Número total de tentativas de acesso a links protegidos por senha
                                hasPassword === false ? "" : (hasPassword === true ? (res.password_access_attempts === null ? "<br> Número de Tentativas: &nbsp; 0" : "<br> Número de Tentativas: &nbsp;" + res.password_access_attempts) : console.error(res.password_access_attempts) ) }

                            ${  // Data e Horário do último acesso a links protegidos por senha
                                hasPassword === false ? "" : (hasPassword === true ? (res.password_last_access_attempt === null ? "<br> Última Tentativa: &nbsp; -" : "<br> Última Tentativa: &nbsp;" + convertDateTime(res.password_last_access_attempt)) : console.error(res.password_last_access_attempt) ) }

                            ${  // Indicador de link protegido por senha
                                hasPassword === false ? "" : (hasPassword === true ? "<br><br><span class='icon-text'><span class='icon'><figure class='image'><img class='image' src='/view/assets/img/icons8-password.svg' alt='' title='É necessária uma senha para acessar o link encurtado'></figure></span><span>Link Protegido</span></span>" : console.error(res.short_code_password) )
                            }
                        </div>
                    </article>
                `;

/*
<!--
                            <br><br>
                            <div class="field has-addons mt-1" style="justify-content: flex-end;">
                                <div class="control">
                                    <button class="button is-small">Atualizar</button>
                                </div>
                            </div>
-->
*/

                /* Comentário sobre o Último Acesso recebido do banco de dados

                //  Verifica se é NULL, se for, mostra ZEROS na tela e se não for converte o horário para o fuso do usuário e exibe na tela
                    Último Acesso: &nbsp; ${ res.last_access === null ? "0000-00-00 00:00:00" : convertDateTime(res.last_access) }

                //  Verifica se é NULL, se for, mostra ZEROS na tela e se não for verifica se é IGUAL a zeros e caso não seja converte o horário para o fuso do usuário e exibe na tela
                    Último Acesso: &nbsp; ${ res.last_access === null ? "0000-00-00 00:00:00" : (res.last_access == "0000-00-00 00:00:00" ? res.last_access : convertDateTime(res.last_access) ) }
                */

                changeCursor_POST("default");
            })
            .catch( error => {
                changeCursor_POST("default");
                handleFetchResponse("generic-error", `Falha ao buscar as informações sobre o link ${input}`); // , error
            });
        } else {
            input_linkEstatisticas.focus();
        } // else
    });
}









////
// OBTENÇÃO DE DADOS DE ESTATÍSTICAS DO SERVIDOR
var serverstats_html_records_total = document.getElementById("serverstats_html_records_total");
var serverstats_html_records_today = document.getElementById("serverstats_html_records_today");
var serverstats_html_records_active = document.getElementById("serverstats_html_records_active");
var serverstats_html_records_blocked = document.getElementById("serverstats_html_records_blocked");

var serverstats_html_access_total = document.getElementById("serverstats_html_access_total");
var serverstats_html_access_today = document.getElementById("serverstats_html_access_today");
var serverstats_html_access_password_protected = document.getElementById("serverstats_html_access_password_protected");
var serverstats_html_access_password_attempts = document.getElementById("serverstats_html_access_password_attempts");

function serverstats_updateNotification(){
    if ( checkCurrentPage("/admin")){
        Toast.fire({
            icon: "success",
            title: "Estatísticas Atualizadas",
            position: "bottom"
        });
    }
}


if (checkCurrentPage("/") || checkCurrentPage("/admin")) {
    var serverstats_dashUpdated = false;

    function serverstats_getData(action){
        fetch("/serverstats")
            .then(handleFetchResponse())
            .then((res) => {
                var records_total = res.records.total;
                var records_today = res.records.saved_today;
                var records_active = res.records.active;
                var records_blocked = res.records.blocked;

                var access_total = res.access.total;
                var access_today = res.access.accesses_today;
                var access_password_attempts = res.access.password_access_attempts;
                var access_password_protected = res.access.password_protected_links;
                //console.warn("- Atualização de estatísticas de cadastro e acessos de links \n" + convertDateTime(new Date()));
                //console.log(res.records, res.access);


                if (serverstats_html_records_total){
                    if(parseInt(serverstats_html_records_total.innerText) !== records_total) serverstats_dashUpdated = true;
                    serverstats_html_records_total.innerText = records_total;
                }
                if (serverstats_html_records_today){
                    if (parseInt(serverstats_html_records_today.innerText) !== records_today) serverstats_dashUpdated = true;
                    serverstats_html_records_today.innerText = records_today;
                }
                if (serverstats_html_records_active){
                    if (parseInt(serverstats_html_records_active.innerText) !== records_active) serverstats_dashUpdated = true;
                    serverstats_html_records_active.innerText = records_active;
                }
                if (serverstats_html_records_blocked){
                    if (parseInt(serverstats_html_records_blocked.innerText) !== records_blocked) serverstats_dashUpdated = true;
                    serverstats_html_records_blocked.innerText = records_blocked;
                }
    //  /////////   //
                if (serverstats_html_access_total){
                    if (parseInt(serverstats_html_access_total.innerText) !== access_total) serverstats_dashUpdated = true;
                    serverstats_html_access_total.innerText = access_total;
                }
                if (serverstats_html_access_today){
                    if (parseInt(serverstats_html_access_today.innerText) !== access_today) serverstats_dashUpdated = true;
                    serverstats_html_access_today.innerText = access_today;
                }
                if (serverstats_html_access_password_protected){
                    if (parseInt(serverstats_html_access_password_protected.innerText) !== access_password_protected) serverstats_dashUpdated = true;
                    serverstats_html_access_password_protected.innerText = access_password_protected;
                }
                if (serverstats_html_access_password_attempts){
                    if (parseInt(serverstats_html_access_password_attempts.innerText) !== access_password_attempts) serverstats_dashUpdated = true;
                    serverstats_html_access_password_attempts.innerText = access_password_attempts;
                }

                if (serverstats_dashUpdated === true && action === "update") { // executar se for TRUE, e apenas na atualização dos dados: não no carregamento da pagina
                    serverstats_dashUpdated = false;
                    serverstats_updateNotification();
                }
        })
        .catch( error => {
            console.error(error);
        });
    } // function


    // executar função na abertura da página
    serverstats_getData("load");

     // executar função a cada 120 segundos / 2 Minutos
    setInterval( function(){
        serverstats_getData("update");
    }, 120000);
}
