// https://pt.stackoverflow.com/questions/329223/armazenar-um-array-de-objetos-em-um-local-storage-com-js

////
// Verificação da última visita do usuário ao site para apagar os códigos curtos do localstorage, VERIFICAR TEMPO NO ARQUIVO "db/autoDelete.php"
const now = new Date();
const currentVisitString = now.toISOString(); // converte a data e hora atual para uma string no formato ISO 8601

const lastVisit = localStorage.getItem('lastVisit'); // pega a data da última visita no localStorage
if (lastVisit) {
  const visitDate = new Date(lastVisit); // cria um objeto com a data da última visita
  visitDate.setMonth(visitDate.getMonth() + 3); // adiciona 3 meses à data da última visita

  if (visitDate < now) { // Se a data da última visita mais 3 meses for anterior à data atual, significa que a última visita foi há mais de 3 meses.
    // console.log('Sua última visita foi há mais de 3 meses!');
    window.localStorage.clear();
  } // else { console.log('Sua última visita foi há menos de 3 meses.'); }
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
    if (sqlDate == "time") { // 10:17
        return new Date().toLocaleString(userTimeLang, { timeZone: userTimeZone, timeStyle: "short" });
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

let local_autoupdate = document.getElementById("local_autoupdate");
let local_autoupdate_timer = document.getElementById("local_autoupdate_timer");

let count_TableLinks = 0;
let count_TableLinksOnUpdate = 0;
let MoreData = [];

let count_tableUpdate = 0; // Contagem do número de atualizações da tabela feitas
let timer_tableUpdate = 2; // TEMPO EM MINUTOS para "setInterval"
let LastTableUpdateOnSuccess = "";

function minutosParaMilissegundos(minutos) {
    return minutos * 60000; // 1 minuto = 60000 milissegundos
}

function NextTableUpdate(action, error) { // 19:47
    const [hora, minutos] = convertDateTime("time").split(':').map(Number);
    const horarioObj = new Date();
    horarioObj.setHours(hora);
    horarioObj.setMinutes(minutos);
  
    horarioObj.setMinutes(horarioObj.getMinutes() + timer_tableUpdate); // + N minutos
    const horarioSomaFormat = horarioObj.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    if (error) {
        local_autoupdate_timer.innerText = "(Última Atualização: " + LastTableUpdateOnSuccess + " | ERRO: atualize a página)";
        local_autoupdate.disabled = true;
    }else {
        if (action == "load") {
            local_autoupdate_timer.innerText = "(Dados atualizados às " + LastTableUpdateOnSuccess + ")";
        }else if (action == "update"){
            local_autoupdate_timer.innerText = "(Próxima Atualização às " + horarioSomaFormat + ")";
        }
    }
}


function local_CreateInitialTable(res, count_TableLinks) {
    var shortCode_URL = window.location.protocol + "//" + window.location.host + "/" + res.short_code;
    var infAction_qr = "<a href='#!' class='shortCode_qr'> <span class='icon'><i class='fas fa-qrcode' title='Gerar um QRCode para o link curto" + res.short_code + "' shortCode_qrURL='" + shortCode_URL + "'></i></span> </a>";

    if (res.short_code_password === null) {
        var hasPassword = false;
        var infAction = "<div class='right'>" + infAction_qr + "</div>";
    }else if (res.short_code_password === "-") {
        var hasPassword = true;
        var infAction = "<div class='right'> <span class='icon'><i class='fa-solid fa-lock' title='É necessária uma senha para acessar o link encurtado'></i></span>" + infAction_qr + "</div>";
    }else  {
        var hasPassword = false;
        var infAction = "<div class='right'>" + infAction_qr + "</div>";
    }

    table_tbody.innerHTML += `
        <tr>
            <td>${count_TableLinks}</td>
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
            <td>${ res.last_access === null ? "-" : convertDateTime(res.last_access)}</td>
            <td>
                ${  // Número total de tentativas de acesso a links protegidos por senha
                    hasPassword === false ? "-" : (hasPassword === true ? (res.password_access_attempts === "-" ? "0" : res.password_access_attempts) : console.error(res.password_access_attempts) ) }
            </td>
            <td>
                ${  // Data e Horário do último acesso a links protegidos por senha
                    hasPassword === false ? "-" : (hasPassword === true ? (res.password_last_access_attempt === "-" ? "-" : convertDateTime(res.password_last_access_attempt)) : console.error(res.password_last_access_attempt) ) }
            </td>
        </tr>
    `;

    table_tbody_ext.innerHTML += `
        <tr>
            <td>${res.url}</td>
            <td>${shortCode_URL}</td>
            <td>${ res.short_code_password === null ? "-" : "Possui Senha" }</td>
            <td>${res.access}</td>
            <td>${ res.last_access === null ? "-" : convertDateTime( res.last_access) }</td>
            <td></td>
            <td>
                ${  // Número total de tentativas de acesso a links protegidos por senha                === null
                    hasPassword === false ? "-" : (hasPassword === true ? (res.password_access_attempts === "-" ? "0" : res.password_access_attempts) : console.error(res.password_access_attempts) ) }
            </td>
            <td>
                ${  // Data e Horário do último acesso a links protegidos por senha                         === null
                    hasPassword === false ? "-" : (hasPassword === true ? (res.password_last_access_attempt === "-" ? "-" : convertDateTime(res.password_last_access_attempt)) : console.error(res.password_last_access_attempt) ) }
            </td>
        </tr>
    `;
}

function local_RequestData(action, link_codes){
    let table_tbody_rows = table_tbody.getElementsByTagName("tr");
    let table_tbody_ext_rows = table_tbody_ext.getElementsByTagName("tr");

    let table_tbody_rows_array = Array.from(table_tbody_rows);

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
        // .then(res => res.json() )
        .then(handleFetchResponse("localstorage-error")) // 405
        .then(response => {
            response = response[0];

            count_tableUpdate++;
            if (count_tableUpdate >= 2){
                LastTableUpdateOnSuccess = convertDateTime("time");
                NextTableUpdate("update", false); // atualizar horário em cada requisição feita para ATUALIZAÇÃO
            }


            var count_ServerLinks = response.length;

            document.getElementById("show_UserTimeZone").innerHTML = `<abbr class="abbr_noStyle" title="Os horários exibidos nesta página estão no seu fuso-horário">Seu fuso horário: &ensp; <b>${userTimeZone}</b></abbr>`;

            response.forEach(function(res){ //  { res = link;

                if (res.hasOwnProperty("status") && res.status == "deleted"){ // table_tbody.innerHTML += "";
                    localStorage_link_remove(res.short_code);

                    // action = update => identificar na tela os links apagados
                    // APENAS será executado se o item for apagado com esta página aberta em outra aba do navegador 

                    for (let row of table_tbody_rows) {
                        if (row.cells[3].innerText == res.short_code) {
                            row.setAttribute("title", "Item não encontrado no servidor, este link curto não está mais disponível para acessos.");
                            row.classList.add("update_deleted", "fadeIn"); // adiciona efeito CSS, e remove apenas o ícone do qrcode da tela
                            row.querySelector(".shortCode_qr").remove();
                        }
                    };
                    for (let row of table_tbody_ext_rows) {
                        if (row.cells[0].innerText == res.url) { // row.cells[3].innerText =~ res.short_code
                            row.remove(); // table_tbody_ext.removeChild(row);
                        }
                    };
                }else {
                    table_buttons.style.display = "";

                    // CRIAÇÃO DA TABELA INICIAL
                    if (action == "load"){
                        count_TableLinks++;
                        local_CreateInitialTable(res, count_TableLinks);
                    }

                    // ATUALIZAÇÃO DOS DADOS DA TABELA
                    else if (action == "update"){
                        for (let row of table_tbody_rows) {
                            var t_code = row.cells[3];
                            var t_access = row.cells[4];
                            var t_last_access = row.cells[5];
                            var t_password_access_attempts = row.cells[6];
                            var t_password_last_access_attempt = row.cells[7];
                            // console.log(res.short_code, res.access, convertDateTime(res.last_access));

                            // Atualizar apenas algumas colunas específicas 
                            if (res.last_access !== null) { // convertDateTime() = 31/12/1969, 21:00
                                if (t_code.innerText == res.short_code && t_access.innerText !== res.access) {
                                    t_access.innerText = res.access;
                                    t_last_access.innerText = convertDateTime(res.last_access);
                                }
                            }
                            if (res.short_code_password === "-") { // var hasPassword = true;
                                if (t_code.innerText == res.short_code && t_password_access_attempts.innerText !== res.password_access_attempts) {                          
                                    t_password_access_attempts.innerText = res.password_access_attempts;
                                    t_password_last_access_attempt.innerText = res.password_last_access_attempt == "-" ? "-" : convertDateTime(res.password_last_access_attempt);
                                }
                            }
                        };
                        for (let row of table_tbody_ext_rows) {
                            var t_url = row.cells[0]; // esta tabela tem URL COMPLETA, e a coluna do código tem o endereço COMPLETO para acesso  
                            var t_access = row.cells[3];
                            var t_last_access = row.cells[4];
                            var t_password_access_attempts = row.cells[6];
                            var t_password_last_access_attempt = row.cells[7];

                            if (res.last_access !== null) { // convertDateTime() = 31/12/1969, 21:00
                                if (t_url.innerText == res.url && t_access.innerText !== res.access) {
                                    t_access.innerText = res.access;
                                    t_last_access.innerText = convertDateTime(res.last_access);
                                }
                            }
                            if (res.short_code_password === "-") { // var hasPassword = true;
                                if (t_url.innerText == res.url && t_password_access_attempts.innerText !== res.password_access_attempts) {
                                    t_password_access_attempts.innerText = res.password_access_attempts;
                                    t_password_last_access_attempt.innerText =  res.password_last_access_attempt == "-" ? "-" : convertDateTime(res.password_last_access_attempt);
                                }
                            }
                        };

                        // action = update => adicionar novos links na tabela recebidos do servidor
                        // APENAS será executado se um novo item for adicionado em outra aba do navegador com esta página aberta 
                        var existeNaNovaVariavel = MoreData.some((item) => item.short_code === res.short_code); // ou o LocalStorage for editado
                        var existeNaTabela = table_tbody_rows_array.some((item) => {
                            const cells = item.getElementsByTagName("td");
                            const urlCelula = cells[3].innerText;
                            return urlCelula === res.short_code;
                        });

                        // Se a url do item atual não existir nos dadosAntigos e ainda não estiver na novaVariavel, adicioná-lo à novaVariavel
                        //// console.log("existeNaTabela [", existeNaTabela, "] existeNaNovaVariavel [", existeNaNovaVariavel, "]", res.short_code, res.url);
                        if (!existeNaTabela && !existeNaNovaVariavel) {
                            MoreData.push(res);
                        }
                    }
                } // else, status == deleted
                
                // carregamento da pagina || adição de novos dados na tabela
                if(count_TableLinks >= 15 || count_TableLinksOnUpdate >= 15){
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
            }); // forEach pela resposta do servidor


            MoreData.forEach(function(item){
                var count_TableTotalLinks = table_tbody.rows.length;
                var lastTableRowNum = table_tbody_rows[count_TableTotalLinks -1].childNodes[0].innerText;
                count_TableLinksOnUpdate = lastTableRowNum;
                count_TableLinksOnUpdate++;

                local_CreateInitialTable(item, count_TableLinksOnUpdate);

                // console.warn(" ");
                // console.table(MoreData);
                // console.log("count_ServerLinks [", count_ServerLinks, "] count_TableTotalLinks [", count_TableTotalLinks, "]");
                // console.log("count_TableLinks [", count_TableLinks, "] count_TableLinksOnUpdate [", count_TableLinksOnUpdate, "]");
            });

        }).catch( error => {
            handleFetchResponse("localstorage-error");
        });

    // console.warn("Dados Salvos no localStorage"); link_codes.forEach(function (element, index, array) { console.log(element, index, array); });
}

const timer_Geral = setInterval( function(){
    var msgLocalData = document.getElementById("msgLocalData");
    var link_codes = localStorage.getItem("links");

    if (link_codes === null) {
        link_codes = new Array();
        if (msgLocalData) msgLocalData.style.display = "none";
    } else {

        // Atualização automática dos dados da tabela
        if ( checkCurrentPage("/links", "") ){
            clearInterval(timer_Geral); // PARAR 'setInterval' principal

            LastTableUpdateOnSuccess = convertDateTime("time"); // string ""

            local_autoupdate.addEventListener('click', () => {

                if (local_autoupdate.checked == true){
                    NextTableUpdate("update", false);

                    timer_LocalUpdate = setInterval( function(){
                        var link_codes = JSON.parse(localStorage.getItem("links"));
                        local_RequestData("update", link_codes);
                    }, minutosParaMilissegundos(timer_tableUpdate)); // TESTES use 5 Segundos == 5000
                } else{
                    NextTableUpdate("load", false);
                    clearInterval(timer_LocalUpdate);
                }
            });

            // preenchimento da tabela ao abrir a página
            NextTableUpdate("load", "", false);
            local_RequestData("load", JSON.parse(link_codes));
        }



        // Exibição de notificação com a quantidade de links salvos do usuário      
        function check_HTML(count){
            if (count == 1) { return count + " link"; }
            if (count >= 2) { return count + " links"; }
        }

        var link_codes = JSON.parse(link_codes);
        
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
