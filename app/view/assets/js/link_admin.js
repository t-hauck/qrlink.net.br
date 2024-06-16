setInterval( function(){
    document.getElementById('admin_dash_currentTime').innerText = convertDateTime("dateTime");
}, 1000);



let admin_table_thead = document.getElementById("admin_table_thead");
let admin_table_tbody = document.getElementById("admin_table_tbody");
let admin_table_tfoot = document.getElementById("admin_table_tfoot");

let table_update = document.getElementById("table_update");

let table_search = document.getElementById("table_search");
let table_search_btnRequest = document.getElementById("table_search_btn");
let table_search_ClearInput = document.getElementById("table_search_ClearInput");

let table_RowsLimit = document.getElementById("select_table_rows"); // Quantidade de registros exibidos por página
let table_sort = document.getElementById("select_table_sort");      // Ordenação dos itens

let pagination_list = document.getElementById("pagination_list");
let prevButton = document.getElementById("table_pagination_prev");
let nextButton = document.getElementById("table_pagination_next");
let page_list_MaxBtnToShow = 7; // numero máximo de botões a serem mostrados na paginação

let btn_block = document.getElementById("bloquear_link_list");
let btn_block_byDate = document.getElementById("bloquear_link_agendar");

let user_search = false;
let user_search_input = "";
let table_current_page = 1;

let requestRes;




let admin_table_colNames = `
    <tr>
        <th class="has-text-centered"> <input type="checkbox" class="checkbox check_links_all"> </th>
        <th class="has-text-centered"><abbr title="Ordem do link na tabela">N.</abbr></th>
        <th class="has-text-centered"><abbr title="URL completa que foi encurtada">Link Original</abbr></th>
        <th></th> <!-- SENHA/Icones = nome da coluna em branco -->
        <th class="has-text-centered"><abbr title="Código alfanumérico pequeno">Código</abbr></th>
        <th class="has-text-centered"><abbr title="Número total de acessos">Acessos</abbr></th>
        <th class="has-text-centered"><abbr title="Data e horário do último acesso">Último Acesso</abbr></th>
        <th class="has-text-centered"><abbr title="Número total de tentativas de acessos mal sucedidas a links curtos protegidos por senha">Tentativas</abbr></th>
        <th class="has-text-centered"><abbr title="Data e horário da última tentativa de acesso mal sucedida a links curtos protegidos por senha">Última Tentativa</abbr></th>
        <th class="has-text-centered"><abbr title="Data e horário do último bloqueio ou desbloqueio do link no sistema">Último Bloqueio</abbr></th>
        <th class="has-text-centered"><abbr title="Data e horário do próximo bloqueio ou desbloqueio automático do link, os agendamentos são executados sempre á meia-noite">Próximo Bloqueio</abbr></th>
        <th class="has-text-centered"><abbr title="Data e horário em que o link foi cadastrado no sistema">Cadastro</abbr></th>
    </tr>
`;

admin_table_thead.innerHTML = admin_table_colNames;







function calc_tableTotalPages() {
    const server_total_records = requestRes.total_records; // Total de registros salvos servidor
    const records = requestRes.rows.length;                // Total de registros recebidos na requisição
    return Math.ceil(Math.max(records, server_total_records) / table_RowsLimit.value); // Math.ceil(server_total_records / records);
}

// #---  BOTOES PARA TROCA DE PAGINA DA TABELA  ---# //
async function handleButtonClickPages(btn) { // removeEventListener("click")
    (btn == "page-prev") ? table_current_page = table_current_page - 1 : ((btn == "page-next") ? table_current_page = table_current_page + 1 : console.error("btn não definido =>", btn));
    await admin_RequestData();
}

function controlClickButtonHTML(action, btn, buttonHTML) { // console.log("=> controlClickButtonHTML \n  ", action, btn, buttonHTML);
    if (action) {
        if (btn == "page-numbers") {
            // Lidar com evento de click dos botões dos números de páginas
            document.querySelectorAll(".pagination-link").forEach((button) => { // button.classList.remove("is-current");
                const pageIndex = Number(button.getAttribute("page-index"));
                if (pageIndex == table_current_page) {
                    button.classList.add("is-current");
                    button.setAttribute("aria-current", pageIndex);
                    table_current_page = pageIndex;
                    button.addEventListener("click", () => handleButtonClickPages(btn));
                }else {
                    button.addEventListener("click", () => handleButtonClickPages(btn));
                }
            });
        }else if (btn == "page-prev" || btn == "page-next") {
            // Lidar com evento de click dos botões de página Anterior & Próxima
            buttonHTML.removeAttribute("disabled");
            buttonHTML.removeEventListener("click", () => handleButtonClickPages());
            buttonHTML.addEventListener("click", () => handleButtonClickPages(btn));
        }else if (btn == "btn-block") {
            // Lidar com evento de click do botão de bloqueio
            buttonHTML.removeAttribute("disabled");
            buttonHTML.removeEventListener("click", () => request_blockLink());
            buttonHTML.addEventListener("click", () => request_blockLink());
        }else if (btn == "btn-block-byDate") {
            // Lidar com evento de click do botão de bloqueio automático com Agendamento
            // buttonHTML.removeAttribute("disabled");
            buttonHTML.removeAttribute("disabled");
            buttonHTML.removeEventListener("click", () => confirm_blockLink_byDate());
            buttonHTML.addEventListener("click", () => confirm_blockLink_byDate());
        }else { console.error("=> controlClickButtonHTML \n     btn não identificado", btn); }
    } else {
        if (btn == "page-numbers") {
            document.querySelectorAll(".pagination-link").forEach((button) => {
                const pageIndex = Number(button.getAttribute("page-index"));
                if (pageIndex == table_current_page) {
                    button.classList.remove("is-current");
                    button.removeAttribute("aria-current");
                    button.removeEventListener("click", () => handleButtonClickPages(btn));
                }else {
                    button.addEventListener("click", () => handleButtonClickPages(btn));
                }
            });
        }else if (btn == "page-prev" || btn == "page-next") {
            buttonHTML.setAttribute("disabled", true);
            buttonHTML.removeEventListener("click", () => handleButtonClickPages(btn));
        }else if (btn == "btn-block") {
            buttonHTML.setAttribute("disabled", true);
            buttonHTML.removeEventListener("click", () => request_blockLink());
        }else if (btn == "btn-block-byDate") {
            buttonHTML.setAttribute("disabled", true);
            buttonHTML.removeEventListener("click", () => confirm_blockLink_byDate());
        }else { console.error("=> controlClickButtonHTML \n     btn não identificado", btn); }
    }
}

function pagination_PageButtonsStatus(){ // Controle de botões da paginação nos estados Ativo|Desabilitado
    let total_pages = calc_tableTotalPages();

    console.log("=> pagination_PageButtonsStatus \n     ", table_current_page, total_pages);


    if (table_current_page === 1) {
        controlClickButtonHTML(false, "page-prev", prevButton);
    }else   controlClickButtonHTML(true, "page-prev", prevButton);

    if (total_pages === table_current_page) {
        controlClickButtonHTML(false, "page-next", nextButton);
    }else   controlClickButtonHTML(true, "page-next", nextButton);

    const prevRange = (table_current_page - 1) * table_RowsLimit.value;
    const currRange = table_current_page * table_RowsLimit.value;

    let page_list = pagination_list.querySelectorAll("li");
    page_list.forEach((item, index) => {
        // console.error("=> ATENÇÃO: loop infinito de requisições, incrementação da variavel 'table_current_page' \nFunção pagination_CreatePageButtons é executada pelo admin_CreateTable, e admin_CreateTable também executa ela. Verificar todas as chamadas das funções pagination_CreatePageButtons e admin_CreateTable");
        console.log("=> page_list.forEach \n    ", item, index);

        // isso aqui fazia parte da função "pagination_CreatePageButtons"
        // foi movido para outra e resolvido um problema de multiplas requisições
        // agora o click tem que funcionar!

        if (index >= prevRange && index < currRange) {
            controlClickButtonHTML(false, "page-numbers", item);  // desabilitar botoes de Numeros das paginas
        }else   controlClickButtonHTML(true, "page-numbers", item);   // HABILITAR botoes de Numeros das paginas
    });
}

function pagination_CreatePageButtons() { // https://codepen.io/tutsplus/pen/poaQEeq
    let total_pages = calc_tableTotalPages();

    let startPage = Math.max(1, table_current_page - Math.floor(page_list_MaxBtnToShow / 2));
    let endPage = Math.min(total_pages, startPage + page_list_MaxBtnToShow - 1);
    if ((endPage - startPage + 1) < page_list_MaxBtnToShow) {
        startPage = Math.max(1, endPage - page_list_MaxBtnToShow + 1);
    }

    pagination_list.innerHTML = ""; // apagar botoes antes de adicionar novamente
    for (let index = startPage; index <= endPage; index++) { // for (let index = 1; index <= total_pages; index++)
        const listItem = document.createElement("li");
        const link = document.createElement("a");
        link.className = "pagination-link";
        link.innerHTML = index;
        link.setAttribute("aria-label", "Page " + index);
        link.setAttribute("page-index", index);
        listItem.appendChild(link);
        pagination_list.appendChild(listItem);
    }
};
// #---  BOTOES PARA TROCA DE PAGINA DA TABELA  ---# //

async function admin_CreateTable() {

    // toggleColumnVisibility("update", true); // MOSTRAR colunas da tabela seguindo preferência do usuario
    console.log("toggleColumnVisibility UPDATE", toggleColumnVisibility);

    const records = requestRes.rows.length; // NO PHP => $this->tableCalcTotalPages($this->contarSalvos(), $limit),
    const rows = requestRes.rows;

    console.warn(" ");

    // Calcular o número do primeiro item na página atual
    let total_pages = calc_tableTotalPages();
    let admin_count_TableLinks = (total_pages - 1) * records;  // + 1;
    console.log("total de páginas com", parseInt(table_RowsLimit.value), "itens em cada:", total_pages, "| total salvo no banco:", admin_count_TableLinks);


    let current_page = Math.ceil(records / table_RowsLimit.value,);
    let admin_count_TableLinks2 = (current_page - 1) * table_RowsLimit.value;  // + 1;
    console.log("pagina atual:", current_page, "| total na tela agora:", admin_count_TableLinks2);

    console.warn(" ");
    pagination_CreatePageButtons();

    // Limpar tabela a cada requisição
    admin_table_tbody.innerHTML = "";

    if (records > 0) {
        // var debugger_LoopExecTime = 0;

        rows.forEach(function(res){
            admin_count_TableLinks++;

            // debugger_LoopExecTime++;


            var shortCode_URL = window.location.protocol + "//" + window.location.host + "/" + res.short_code;

            if (res.blocked === 1) { // true // console.warn("=> URL BLOQUEADA" , res);
            BlockedLink_table = `class="has-link-blocked" title='O link curto ` + res.short_code + ` está bloqueado no sistema, acessos a ele serão negados e apresentada página de erro 404. Ao tentar cadastrar sua URL original novamente, será retornada a mensagem de que este "este endereço não é permitido".' ` // CSS com 50% de transparência
            ActionIcon_BlockLink = "<span class='icon'><i class='fa-solid fa-ban' title='O link curto " + res.short_code + " está bloqueado no sistema, acessos a ele serão negados'></i></span>";
            BlockedLink_DeleteClass = "blocked";
            }else {
                BlockedLink_table = "";
                ActionIcon_BlockLink = "<a href='#!'> <span class='icon'><i class='fas fa-qrcode' title='Gerar um QRCode para o link curto " + res.short_code + "' createQRCode_ShortURL='" + shortCode_URL + "'></i></span> </a>";
                BlockedLink_DeleteClass = "";
            }

            if (res.short_code_password === null) {
                var hasPassword = false;
                var ActionIcon = ActionIcon_BlockLink;
            }else if (res.short_code_password === "-") {
                var hasPassword = true;
                var ActionIcon = "<span class='icon'><i class='fa-solid fa-lock' title='É necessária uma senha para acessar o link encurtado'></i></span>" + ActionIcon_BlockLink;
            }

            admin_table_tbody.innerHTML += `
            <tr ${BlockedLink_table}>
            <td class="has-text-centered"><input type="checkbox" class="checkbox check_link ${BlockedLink_DeleteClass}" value="${res.short_code}"></td>
            <td class="has-text-centered">${admin_count_TableLinks}</td>
            <td>
            <a href="${res.url}" target="_blank">
            ${ res.url.length >= 100 ?  res.url.substr(0, 99) + "..." : res.url }
            </a>
            </td>
            <td class="icons"> <!-- classe usada no CSS junto com 'has-link-blocked' -->
            <div class="right">
            ${ActionIcon}
            <a href="#!">
            <span class="icon">
            <i class="fa-regular fa-trash-can apagar_link" item-shortCode="${res.short_code}"></i>
            </span>
            </a>
            </div>
            </td>
            <td class="has-text-centered">
            <a href="${res.short_code}" target="_blank">${res.short_code}</a>
            </td>
            <td class="has-text-centered">${res.access}</td>
            <td class="has-text-centered">
            ${ res.last_access === null ? "-" : convertDateTime(res.last_access) }
            </td>
            <td class="has-text-centered">
            ${  // Número total de tentativas de acesso a links protegidos por senha
                hasPassword === false ? "-" : (hasPassword === true ? (res.password_access_attempts === null ? "0" : res.password_access_attempts) : console.error(res.password_access_attempts) ) }
                </td>
                <td class="has-text-centered">
                ${  // Data e Horário da última tentativa de acesso fracasada a links protegidos por senha
                    hasPassword === false ? "-" : (hasPassword === true ? (res.password_last_access_attempt === null ? "-" : convertDateTime(res.password_last_access_attempt)) : console.error(res.password_last_access_attempt) ) }
                    </td>

            <td class="has-text-centered">${ res.last_block === null ? "-" : convertDateTime(res.last_block) }</td>
            <td class="has-text-centered">${ res.blocking_date === null ? "-" : convertDateTime(res.blocking_date) }</td>
            <td class="has-text-centered">${convertDateTime(res.added_date)}</td>
            </tr>
            `;


                    // mais de 1.000 dados por página
                    // console.log("debugger_LoopExecTime [FORA DO IF] => ", debugger_LoopExecTime);

                    // if (table_RowsLimit.value >= 3000 && debugger_LoopExecTime > 2900) {
                    //     console.log("debugger_LoopExecTime => ", debugger_LoopExecTime);
                    //     debugger;
                    // }

        }); // forEach

        // Adicione um evento de clique a cada checkbox individual com a classe "check_link"
        admin_table_tbody.querySelectorAll(".check_link").forEach(btn => {
            btn.addEventListener("click", function(event) {
                select_itensTable(event);
            });
        });
    } else {
        admin_table_tbody.innerHTML += '<tr><td colspan="10" class="has-text-centered">Nenhum Resultado Encontrado</td></tr>';
    }

    if (records > 30) {
        admin_table_tfoot.innerHTML = admin_table_colNames; // table_tbody.innerHTML += `
    }else { admin_table_tfoot.innerHTML = ""; }

    toggleColumnVisibility("update", false); // OCULTAR colunas da tabela seguindo preferência do usuario
    console.log("toggleColumnVisibility UPDATE", toggleColumnVisibility);
}

async function admin_RequestData(){ // apenas atualizar os dados da tabela, sem executar a pesquisa
    changeCursor_POST("wait");

    let _data = {
        page: table_current_page,
        rows: table_RowsLimit.value,
        sort: table_sort.value,
        search: user_search,
        search_term: user_search_input
    };

    let requestConf = {
        method: "POST",
        body: JSON.stringify(_data),
        headers: {
            "CSRF-Token": token.value,
            "Content-Type": "application/json; charset=UTF-8"
        }
    };

    try {
        const response = await fetch("/admin", requestConf);
        const data = await response.json();
        requestRes = data; // resposta da requisição

        handleFetchResponse();

        await admin_CreateTable();    // criar tabela com dados recebidos do servidor

        changeCursor_POST("default");
    } catch (error) {
        changeCursor_POST("default");
        handleFetchResponse("generic-error", "Ocorreu um erro ao obter os dados.");
    }
}


function toTableHead(){
    location.href = "#admin_table_thead";
}

async function executeTableSearch(){
    if (table_search.value){
        user_search = true;
        user_search_input = table_search.value;
    } else {
        user_search = false;
        user_search_input = "";
    }

    if (table_search.value && (/^\s*$/.test(user_search_input))){ // ignorar campo COM algum texto e apenas com espaços em branco
        changeCursor_POST("default");
        return;
    }

    await admin_RequestData();
    pagination_PageButtonsStatus();
    toTableHead();
}

table_search.addEventListener("keydown", async function(event) { if (event.key === "Enter") await executeTableSearch(); });
table_search_btnRequest.addEventListener("click", async () => { await executeTableSearch(); });
table_search_ClearInput.addEventListener("click", async function () {
    table_search.value = "";    // recarregar tabela SEM a pesquisa
    await executeTableSearch();
    pagination_PageButtonsStatus();
});

table_update.addEventListener("click", async (event) => {
    await admin_RequestData();
    pagination_PageButtonsStatus();
    toTableHead();
});

document.addEventListener("DOMContentLoaded", async function () {
    await admin_RequestData();
    pagination_PageButtonsStatus();
});










////
// Controle de seleção de link e adição em Array
function check_disableBtnBlock(){
    let checkboxes = document.querySelectorAll(".check_link");
    let hasBlocked = false;     //     está bloqueado
    let hasUnblocked = false;   // não está bloqueado

    checkboxes.forEach(function(check) {
        const value = check.getAttribute("value");
        if (tableSelectedItens.includes(value)) {
            if (check.classList.contains("blocked")) {
                hasBlocked = true;
            } else  hasUnblocked = true;
        }
        // console.log(hasBlocked, hasUnblocked, check);
    });

    // Verifique se ambas as condições foram satisfeitas
    btn_block_status = btn_block.getAttribute("disabled");
    if (hasBlocked && hasUnblocked) { // controlClickButtonHTML(click True|False, referencia ao HTML no codigo, HTML do elemento);
        if (btn_block_status == "true"){
            console.warn("IF-1 => ", hasBlocked, hasUnblocked);
            controlClickButtonHTML(false, "btn-block", btn_block);
            controlClickButtonHTML(false, "btn-block-byDate", btn_block_byDate);
        }else {
            console.warn("IF-2 => ", hasBlocked, hasUnblocked);
            controlClickButtonHTML(false, "btn-block", btn_block);
            controlClickButtonHTML(false, "btn-block-byDate", btn_block_byDate);
        }
    }else {
        if (btn_block_status == "true"){
            console.warn("ELSE-1 => ", hasBlocked, hasUnblocked);
            controlClickButtonHTML(true, "btn-block", btn_block);
            controlClickButtonHTML(true, "btn-block-byDate", btn_block_byDate);
        }else {
            console.warn("ELSE-2 => ", hasBlocked, hasUnblocked);
            controlClickButtonHTML(true, "btn-block", btn_block);
            controlClickButtonHTML(true, "btn-block-byDate", btn_block_byDate);
        }
    }

    // console.warn(hasBlocked, hasUnblocked);
}

var tableSelectedItens = [];
function select_itensTable(event) { // console.warn(" ");
    if (event.target.classList.contains("check_links_all")) { // Verifica se o evento é proveniente do "check_links_all"
        // Encontre os checkboxes individuais dentro da mesma seção da tabela
        var tableSection = event.target.closest("table").querySelectorAll(".check_link");
        if (event.target.checked) { // Marca todos os checkboxes e adiciona seus valores ao array para obter todos os itens selecionados
            tableSection.forEach(btn => {
                btn.checked = true;
                if (!tableSelectedItens.includes(btn.value)) {
                    tableSelectedItens.push(btn.value);
                }
            });
            console.log("\n\n=> ALL (Check)\n\n");
        } else { // Desmarca todos os checkboxes individuais e remove seus valores do array
            tableSection.forEach(btn => {
                btn.checked = false;
                tableSelectedItens = tableSelectedItens.filter(value => value !== btn.value);
            });
            console.log("\n\n=> ALL (Uncheck)\n\n");
        }
    } else { // Trata o clique em um checkbox individual
        if (event.target.checked) {
            tableSelectedItens.push(event.target.value);
        } else {
            tableSelectedItens = tableSelectedItens.filter(value => value !== event.target.value);
        } // console.log("\n\n=> SINGLE ITEM \n\n");
    }

    check_disableBtnBlock();

    // if (tableSelectedItens.length === 0) console.warn("=> Nenhum Item Selecionado");
    // console.log(tableSelectedItens);
}

document.querySelectorAll(".check_links_all").forEach(btn => {
    btn.addEventListener("click", function(event) {
        select_itensTable(event);
    });
});








////
// APAGAR um ou mais links pelo checkbox na tabela
document.getElementById("apagar_link_list").addEventListener("click", (e) => {
    if (tableSelectedItens === undefined || tableSelectedItens.length == 0) {
        alert("Selecione ao menos um link usando a caixa de seleção ao lado do Link Original.");
        return;
    }

    // Seleciona todos os checkboxes com as classes "check_link" e "blocked"
    var checkboxBlockedLink = document.querySelectorAll('.check_link.blocked');
    var totalBlockedLinks = Array.from(checkboxBlockedLink).filter(function(checkbox) {
        return checkbox.checked;
    }).length;
    if (totalBlockedLinks > 0) { // Pergunta ao usuário se deseja confirmar a remoção dos valores e dos itens da página
        var confirmDeleteBlocked = window.prompt("Deseja apagar os links bloqueados? \n- para não apagar digite 'n' \n\nTotal de bloqueados selecionados: " + totalBlockedLinks);
        if (confirmDeleteBlocked === "n") { // Remove os valores correspondentes do array
            checkboxBlockedLink.forEach(function(checkbox) {
                var valueToRemove = checkbox.value;
                tableSelectedItens = tableSelectedItens.filter(function(value) {
                    return value !== valueToRemove;
                });
                checkbox.checked = false;
            }); //  console.log("Array Atualizado, sem os links BLOQUEADOS:", tableSelectedItens);
        }
    } ///

    if (tableSelectedItens.length == 1) {
        let confirm_Delete = prompt("Para confirmar a exclusão do item, digite seu link curto: ");
        if (!tableSelectedItens.includes(confirm_Delete)) return;
    }else {
        let confirm_Delete = prompt("Para confirmar a exclusão dos links selecionados, digite 'delete': ");
        if (confirm_Delete !== "delete") return;
    }
    changeCursor_POST("wait");

    let _data = JSON.stringify(tableSelectedItens);
    let requestConf = {
        method: "POST",
        body: _data,
        headers: {
            "CSRF-Token": token.value,
            "Content-Type": "application/json; charset=UTF-8"
        }
    };
    fetch("/admin/delete", requestConf )
    .then(handleFetchResponse())
    .then(async response => {
        handleFetchResponse();

        Toast.fire({
            icon: "success",
            title: "Sucesso!",
        });

        await admin_RequestData();  // recarregar tabela
    })
    .catch( error => {
        changeCursor_POST("default");
        handleFetchResponse("generic-error", `Erro ao Apagar os links selecionados do banco de dados`);
    });
});

////
// APAGAR um link unico pelo icone na tabela - EM USO NO ARQUIVO "isVisible.js"
function request_deleteSingleLink(btn){ // click na classe ".apagar_link"
    let confirm_Delete = prompt("Para confirmar a exclusão do item, digite seu link curto: ");
    let short_code = btn.getAttribute("item-shortCode");
    if (confirm_Delete !== short_code) return;

    changeCursor_POST("wait");

    let requestConf = {
        method: "DELETE", // POST
        headers: {
            "CSRF-Token": token.value,
            "Content-Type": "application/json; charset=UTF-8"
        }
    };
    fetch(`/admin/delete/${short_code}`, requestConf )
    .then(handleFetchResponse())
    .then(async response => {
        handleFetchResponse();

        Toast.fire({
            icon: "success",
            title: "Sucesso!",
        });

        await admin_RequestData();  // recarregar tabela

    })
    .catch( error => {
        changeCursor_POST("default");
        handleFetchResponse("generic-error", `Erro ao Apagar o link selecionado do banco de dados`);
    });
}









////
// des-BLOQUEAR um ou mais links pelo checkbox na tabela
function request_blockLink(){ // btn_block.addEventListener("click"
    if (tableSelectedItens === undefined || tableSelectedItens.length == 0) {
        alert("Selecione ao menos um link usando a caixa de seleção ao lado do Link Original.");
        return;
    }
    changeCursor_POST("wait");

    let _data = {
        link: tableSelectedItens, // Array
        date: null
    };

    let requestConf = {
        method: "POST",
        body: JSON.stringify(_data),
        headers: {
            "CSRF-Token": token.value,
            "Content-Type": "application/json; charset=UTF-8"
        }
    };
    fetch("/admin/block", requestConf )
    .then(handleFetchResponse())
    .then(async response => {
        handleFetchResponse();
        // console.log(tableSelectedItens);
        tableSelectedItens = []; // Limpar array de itens selecionados, requisição REPETIDA
        // console.log(tableSelectedItens);

        Toast.fire({
            icon: "success",
            title: "Sucesso!",
        });

        await admin_RequestData();  // recarregar tabela

    })
    .catch( error => {
        changeCursor_POST("default");
        handleFetchResponse("generic-error", `Erro ao executarã operação`);
    });
}









////
// CONFIRMAR des-BLOQUEIO de um ou mais links pelo checkbox na tabela com Agendamento Automático
async function confirm_blockLink_byDate() {
    const { value: date } = await Swal.fire({
        title: "Selecione uma data",
        input: "date",
        didOpen: async () => {
            const today = (new Date()).toISOString();
            Swal.getInput().min = today.split("t")[0];
        }
    });
    if (date) {
        Swal.fire({
            title: "Confirme a data escolhida:",
            html: "A data selecionada não poderá ser alterada. <br><br><strong>" + date + "</strong>",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Agendar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) { // após confirmação da data

                request_blockLink_byDate(date);
            }
        });
    }
}









////
// des-BLOQUEAR um ou mais links pelo checkbox na tabela com Agendamento Automático
function request_blockLink_byDate(userDate){
    if (tableSelectedItens === undefined || tableSelectedItens.length == 0) {
        alert("Selecione ao menos um link usando a caixa de seleção ao lado do Link Original.");
        return;
    }
    changeCursor_POST("wait");

    let _data = {
        link: tableSelectedItens,   // Array
        date: userDate              // String
    };

    let requestConf = {
        method: "POST",
        body: JSON.stringify(_data),
        headers: {
            "CSRF-Token": token.value,
            "Content-Type": "application/json; charset=UTF-8"
        }
    };
    fetch("/admin/block/schedule", requestConf )
    .then(handleFetchResponse())
    .then(async response => {
        handleFetchResponse();
        // console.log(tableSelectedItens);
        tableSelectedItens = []; // Limpar array de itens selecionados, requisição REPETIDA
        // console.log(tableSelectedItens);

        Toast.fire({
            icon: "success",
            title: "Sucesso!",
        });

        await admin_RequestData();  // recarregar tabela

    })
    .catch( error => {
        changeCursor_POST("default");
        handleFetchResponse("generic-error", `Erro ao executarã operação`);
    });
}


////
// PESQUISAR-LINK pelo JavaScript - CÓDIGO NÃO MAIS USADO
// var tabela = document.getElementById('admin_table_filter');
// var admin_input_filtro = document.getElementById('pesquisa_input');
// // var pesquisa_apagarItens = document.getElementById("pesquisa_apagarItens");

// if (tabela) {
//     admin_input_filtro.addEventListener('input', () => {
//         let input_filtro = admin_input_filtro.value;

//         for (var i = 1; i < tabela.rows.length; i++) {
//             var conteudoCelula = tabela.rows[i].cells[2].innerText; // cells[0] == primeira coluna
//             var corresponde = conteudoCelula.toLowerCase().indexOf(input_filtro) >= 0;
//             tabela.rows[i].style.display = corresponde ? '' : 'none';
//         } // for
//     });
// }
