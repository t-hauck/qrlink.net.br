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
                if (clipboardText) { // verifica SE EXISTE conteúdo copiado
                    Toast.fire({
                        icon: 'info',
                        title: 'Texto copiado da área de transferência'
                    });
        
                    setTimeout( function(){ 
                        input_link.value = clipboardText; // input_link == Importação feita no 'isVisible.js' 
                        // input_link.focus(); // LINHA SEM EFEITO => HTML tem o atributo autofocus
                    }, 1000)
                }
            
            } catch (err) {
                console.error("Falha ao ler o conteúdo da área de transferência. \n", "=>           ", err);
            }
        } else console.error("Permissão Negada para acessar o conteúdo da área de transferência.");

        //permissionStatus.onchange = () => { // Escuta mudanças ao estado de permissão
        //    console.log(permissionStatus.state);
        //};
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
//// Leitura da área de transferência do usuário ao abrir a página => DESABILITADO
// setTimeout( async function(){
//     if ( checkCurrentPage("/", "") )  clipboard_user("read", " ");
// }, 1000);









////
// ENCURTAMENTO DE LINKS
let submit_criarLink = document.getElementById("submit_criarLink");
if (submit_criarLink) {
    submit_criarLink.addEventListener('click', function (){
        if ( checkCurrentPage("/admin", "") ){
            input_link = prompt(`Informe a URL completa:`);
            input_lnk = input_link; // para pegar texto do "prompt"
            input_lnk_senha = ""; //// a página ADMIN não tem campo para salvar senha
            if (input_lnk == null || input_lnk == undefined || input_lnk == "") return;
        } else {
            input_lnk = input_link.value;
            input_lnk_senha = input_link_senha.value;
        }

        if (input_lnk) { // verifica se foi digitado algo, a URL é validada apenas pelo back-end
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
            // fetch("/link/save", requestConf) // fetch( window.location.host,
            fetch("/save", requestConf) // fetch( window.location.host,
                .then(handleFetchResponse())
                .then((res) => { // console.log(res[0]);
        
                    handleFetchResponse();

                    localStorage_link_save(res.short_code);

                        // res[0].forEach(function(item){
                        let link_curto = window.location.origin + "/" + res.short_code;
                        let cp_text = clipboard_user("copy", link_curto);
            
                        if (checkCurrentPage("/admin", "")) { // a página administrativa é recarregada ao salvar um link
                            if (cp_text) {
                                    alert("Compartilhe este endereço: \n" + link_curto + "\n\n" + "O link curto foi copiado para a área de transferência, cole o texto em algum lugar.");
                            } else  alert("Compartilhe este endereço: \n" + link_curto);
                                checkCurrentPage("/admin", "reload") 
                        } else {
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
                        } // else
                        // }); // forEach

                changeCursor_POST("default");
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
                

                input_statsResult.classList.remove("fadeOut_toTop");
                input_statsResult.classList.add("fadeIn_toBottom");

                input_statsResult.innerHTML = `
                    <article class="message is-warning">
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
                                hasPassword === false ? "" : (hasPassword === true ? "<br><br><span class='icon-text'><span class='icon'><figure class='image'><img class='image' src='/view/img/icons8-password.svg' alt='' title='É necessária uma senha para acessar o link encurtado'></figure></span><span>Link Protegido</span></span>" : console.error(res.short_code_password) )
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

                // isVisible.js
                detectDeleteAction();

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
