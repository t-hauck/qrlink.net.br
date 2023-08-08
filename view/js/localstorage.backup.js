// https://pt.stackoverflow.com/questions/329223/armazenar-um-array-de-objetos-em-um-local-storage-com-js

////
// Verificação da última visita do usário ao site para apagar os links LOCAIS - VERIFICAR TEMPO NO ARQUIVO "db/autoDelete.php"
const now = new Date(); // cria um objeto com a data e hora atuais
const currentVisitString = now.toISOString(); // converte a data e hora atual para uma string no formato ISO 8601

const lastVisit = localStorage.getItem('lastVisit'); // pega a data da última visita no localStorage
if (lastVisit) {
  const visitDate = new Date(lastVisit); // cria um objeto com a data da última visita
  visitDate.setMonth(visitDate.getMonth() + 6); // adiciona 6 meses à data da última visita

  if (visitDate < now) { // Se a data da última visita mais 6 meses for anterior à data atual, significa que a última visita foi há mais de 6 meses.
    // console.log('Sua última visita foi há mais de 6 meses!');
    window.localStorage.clear();
  } // else { console.log('Sua última visita foi há menos de 6 meses.'); }
} else { // console.log('Esta é a sua primeira visita.');
    localStorage.setItem('lastVisit', currentVisitString); // se não existir, salva a data e hora da última visita no localStorage
}

localStorage.setItem('lastVisit', currentVisitString); // atualiza o registro no localStorage sempre que o site for acessado


// Adicionar item ao localStorage
function localStorage_link_save(item) {
    let link_codes = localStorage.getItem("links");
    if (link_codes === null) {
        link_codes = new Array();
    } else {
        link_codes = JSON.parse(link_codes);
    }

    // Verificação se o item atual já existe no localstorage. Se existe, não é salvo novamente.
    let itemLocalExiste;
    link_codes.forEach(function (element, index, array) {
        if (item == element) itemLocalExiste = true;
    });

    if (!itemLocalExiste){
        link_codes.push(item);
        localStorage.setItem("links", JSON.stringify(link_codes));
    }
}

// https://pt.stackoverflow.com/questions/344404/diferen%c3%a7a-entre-splice-e-slice#344419
function localStorage_link_remove(item) {
    let link_codes = localStorage.getItem("links");
    link_codes = JSON.parse(link_codes);

    // remove um único item do Array se o servidor responder que o short_code não existe 
    let index = link_codes.indexOf(item);
    link_codes.splice(index, 1);
    localStorage.setItem("links", JSON.stringify(link_codes));

    if (link_codes.length == 0){ // se não existir nenhum item/ o Array existe mas está vazio, então o registro será apagado do localstorage
        window.localStorage.removeItem("links"); 
    }
}





const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
const userTimeLang = Intl.DateTimeFormat().resolvedOptions().locale;

function convertDateTime(sqlDate){
	// if (sqlDate == "dateTime") { // 03/07/2023, 10:17
    //     return new Date().toLocaleString(userTimeLang, { timeZone: userTimeZone, dateStyle: "short", timeStyle: "short" });
    // }
    // if (sqlDate == "date") { // 03/07/2023
	// 	return new Date().toLocaleString(userTimeLang, { timeZone: userTimeZone, dateStyle: "short" });
	// }
    if (sqlDate == "time") { // 10:17:17
		function pad(s) {
		    return (s < 10) ? '0' + s : s;
		}
		var date = new Date();
		return [date.getHours(), date.getMinutes(), date.getSeconds()].map(pad).join(':');

	} else{ // CONVERSÃO DE DATA DO MySQL = "2023-05-01 00:00:00"
        return new Date(sqlDate).toLocaleString(userTimeLang, { timeZone: userTimeZone, dateStyle: "short", timeStyle: "short" });
    }

}










////
// OBTER DADOS DO SERVIDOR A PARTIR DO LOCALSTORAGE | PÁGINA DE ESTATÍSTICAS
let table_tbody = document.getElementById("table_tbody");
let table_tbody_ext = document.getElementById("table_tbody_export");
let table_tfoot = document.getElementById("table_tfoot");
let table_buttons = document.getElementById("local_buttons");

function request_localData(link_codes){

    let _data = { short_code: link_codes };
    let requestConf = {
        method: "POST",
        body: JSON.stringify(_data),
        headers: {
            "CSRF-Token": token.value,
            "Content-Type": "application/json; charset=UTF-8"
        }
    };
    fetch( "/getUserStats", requestConf )
        .then(res => res.json() )
        .then(res => {
            document.getElementById("show_UserTimeZone").innerHTML = `<abbr class="abbr_noStyle" title="Os horários exibidos nesta página estão no seu fuso-horário">Seu fuso horário: &ensp; <b>${userTimeZone}</b></abbr>`;

            let countKey = 0;

            res[0].forEach(function(link) {
                res = link;

                if (!res.hasOwnProperty('status')) { // contar quantos elementos do Array NÃO possuem a chave "status"
                    countKey++; // status = usado pelo back-end para enviar um sinal de erro
                }

                if (res.status == "deleted"){ // table_tbody.innerHTML += "";
                    localStorage_link_remove(res.short_code);

                }else {
                    shortCode_url = window.location.protocol + "//" + window.location.host + "/" + res.short_code;
                    infAction_qr = "<a href='#!' class='qr_shortCode' short_code='" + shortCode_url + "'> <span class='icon'><i class='fas fa-qrcode' title='Gerar um QRCode para o link curto " + link["short_code"] + "'></i></span> </a>";

                    if (res.short_code_password === null) {
                            var hasPassword = false;
                            infAction = "<div class='right'>" + infAction_qr + "</div>";
                    }else if (res.short_code_password === "-") {
                            var hasPassword = true;
                            infAction = "<div class='right'> <span class='icon'><i class='fa-solid fa-lock' title='É necessária uma senha para acessar o link encurtado'></i></span>" + infAction_qr + "</div>";
                    }else  {
                        var hasPassword = false;
                        infAction = "<div class='right'>" + infAction_qr + "</div>";
                    }


                    table_tbody.innerHTML += `
                        <tr>
                            <td>${countKey}</td>
                            <td>
                                <a href="${res.url}" target="_blank">
                                    ${ res.url.length >= 80 ?  res.url.substr(0, 79) + "..." : res.url }
                                </a>
                            </td>
                            <td>${ infAction }</td>
                            <td>
                                <a href="${res.short_code}" target="_blank">${res.short_code}</a>
                            </td>
                            <td>${res.access}</td>
                            <td>${ res.last_access === null ? "-" : convertDateTime( res.last_access)}</td>
                            <td>
                                ${  // Número total de tentativas de acesso a links protegidos por senha
                                    hasPassword === false ? "-" : (hasPassword === true ? (res.password_access_attempts === null ? "0" : res.password_access_attempts) : console.error(res.password_access_attempts) ) }
                            </td>
                            <td>
                                ${  // Data e Horário do último acesso a links protegidos por senha
                                hasPassword === false ? "-" : (hasPassword === true ? (res.password_last_access_attempt === null ? "-" : convertDateTime(res.password_last_access_attempt)) : console.error(res.password_last_access_attempt) ) }
                            </td>
                        </tr>
                    `;

                    table_tbody_ext.innerHTML += `
                        <tr>
                            <td>${res.url}</td>
                            <td>${shortCode_url}</td>
                            <td>${ res.short_code_password === null ? "-" : "Possui Senha" }</td>
                            <td>${res.access}</td>
                            <td>${ res.last_access === null ? "-" : convertDateTime( res.last_access) }</td>
                            <td></td>
                            <td>
                                ${  // Número total de tentativas de acesso a links protegidos por senha
                                    hasPassword === false ? "-" : (hasPassword === true ? (res.password_access_attempts === null ? "0" : res.password_access_attempts) : console.error(res.password_access_attempts) ) }
                            </td>
                            <td>
                                ${  // Data e Horário do último acesso a links protegidos por senha
                                    hasPassword === false ? "-" : (hasPassword === true ? (res.password_last_access_attempt === null ? "-" : convertDateTime(res.password_last_access_attempt)) : console.error(res.password_last_access_attempt) ) }
                            </td>
                        </tr>
                    `;
                }
            }); // forEach

            if(countKey >= 14){ // lenght, se existirem 15 ou mais links, adicione o TFOOT da tabela abaixo dos links
                table_tfoot.innerHTML = `
                <tr>
                    <th><abbr title="Ordem do link na tabela">N.</abbr></th>
                    <th><abbr title="URL completa que foi encurtada">Link Original</abbr></th>
                    <th></th>
                    <th><abbr title="Código alfanumérico pequeno">Código</abbr></th> 
                    <th><abbr title="Número total de acessos">Acessos</abbr></th>
                    <th><abbr title="Data e horário do último acesso">Último Acesso</abbr></th>
                    <th><abbr title="Número total de tentativas de acessos mal sucedidas a links curtos protegidos por senha">Tentativas</abbr></th>
                    <th><abbr title="Data e horário da última tentativa de acesso mal sucedida a links curtos protegidos por senha">Última Tentativa</abbr></th>
                </tr>
                `; // table_tbody.innerHTML += `
            }
        }).catch( error => {
            console.error(error);
        });

    // console.warn("Dados Salvos no localStorage")
    // link_codes.forEach(function (element, index, array) {
    //     console.log(element, index, array);
    // });
}

const timer_Geral = setInterval( function(){
    var msgLocalData = document.getElementById("msgLocalData");
    var link_codes = localStorage.getItem("links");

    if (link_codes === null) {
        link_codes = new Array();
        if (msgLocalData) msgLocalData.style.display = "none";
    } else {
        var link_codes = JSON.parse(link_codes);

        // Atualização automática dos dados da tabela [EM TESTES] 
        if ( checkCurrentPage("/links", "") ){
            clearInterval(timer_Geral); // PARAR 'setInterval' principal
            table_buttons.style.display = "";

            var local_autoupdate = document.getElementById("local_autoupdate");
            local_autoupdate.addEventListener('click', () => {

                if (local_autoupdate.checked == true){                
                    timer_LocalUpdate = setInterval( function(){
                        table_tbody.innerHTML = "";
                        table_tbody_ext.innerHTML = "";
                        table_tfoot.innerHTML = "";

                        request_localData(link_codes);
                        console.log("Dados atualizados às", convertDateTime("time"));

                    }, 5000);
                } else{
                    clearInterval(timer_LocalUpdate);
                }
            });

            // preenchimento da tabela ao abrir a página
            request_localData(link_codes);
        }



        // Exibição de notificação com a quantidade de links do usuário salvos      
        function check_HTML(count){
            if (count == 1) { return count + " link"; }
            if (count >= 2) { return count + " links"; }
        }

        if (checkCurrentPage("/", "") ){
            var codeLenght = link_codes.length;
            if (codeLenght == 0) { msgLocalData.style.display = "none" }
            else {
                msgLocalData.style.display = "";
                msgLocalData.innerHTML = `
                    <article class="message is-success">
                        <div class="message-body">
                            Você encurtou ${check_HTML(codeLenght)}! &nbsp; Acompanhe seus acessos na página de &nbsp; <a href="/links">estatísticas</a>.
                        </div>
                    </article>
                `;
            } // else
        } // checkCurrentPage

        if (checkCurrentPage("/sobre", "") ){   
            var codeLenght = link_codes.length;
            if (codeLenght == 0) { msgLocalData.style.display = "none" }
            else {
                msgLocalData.style.display = "";
                msgLocalData.innerHTML = `
                    <article class="message">
                        <div class="message-body">
                            Você encurtou ${check_HTML(codeLenght)}! &nbsp;  Acompanhe seus acessos na página de &nbsp; <a href="/links">estatísticas</a>.
                        </div>
                    </article>
                `;
            } // else
        } // checkCurrentPage
    
    } // else
}, 1000);