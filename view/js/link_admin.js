////
// PESQUISAR-LINK
var tabela = document.getElementById('admin_table_filter');
var admin_input_filtro = document.getElementById('pesquisa_input');
// var pesquisa_apagarItens = document.getElementById("pesquisa_apagarItens");

if (tabela) {
    admin_input_filtro.addEventListener('input', () => {
        let input_filtro = admin_input_filtro.value;

        for (var i = 1; i < tabela.rows.length; i++) {
            var conteudoCelula = tabela.rows[i].cells[2].innerText; // cells[0] == primeira coluna
            var corresponde = conteudoCelula.toLowerCase().indexOf(input_filtro) >= 0;
            tabela.rows[i].style.display = corresponde ? '' : 'none';

            // // icone-APAGAR apenas aparece quando houver texto no campo, e resultados para a pesquisa
            // if (input_filtro !== "" && corresponde) {
            //         pesquisa_apagarItens.style.display = "block";
            // } else  pesquisa_apagarItens.style.display = "none";            
        } // for
    });
}


////
// Controle de seleção de link e adição em Array
// Apagar todos os links pelo checkbox
var ArrayCheck_delete = [];
document.getElementById("delete_links_all").addEventListener("click", (e) => {		
    var marcar = e.target.checked;
    document.querySelectorAll(".delete_link").forEach(item => {
        if (item.closest('tr').style.display !== 'none') { // Verifica se o item está vísivel, para NÃO selecionar os que foram ocultados pela pesquisa
            item.checked = marcar;
            ArrayCheck_delete = Array.from(document.querySelectorAll('input[type=checkbox].delete_link:checked')).map(checkedItem => checkedItem.getAttribute("item-shortCode"));
            console.log(ArrayCheck_delete);
        }
    });
});


// Apagar um único link pelo checkbox
document.querySelectorAll(".delete_link").forEach(item => {
    item.addEventListener("click", (e) => {
        ArrayCheck_delete = Array.from(document.querySelectorAll('input[type=checkbox].delete_link:checked')).map(checkedItem => checkedItem.getAttribute("item-shortCode"));
        console.log(ArrayCheck_delete);
    });
});


////
// APAGAR-LINK pelo checkbox com UM ou VARIOS links
document.getElementById("apagar_link_list").addEventListener("click", (e) => {
    if (ArrayCheck_delete === undefined || ArrayCheck_delete.length == 0) {
        alert("Selecione ao menos um link para apagar usando a caixa de seleção ao lado do Link Original.");
        return;
    }
    let confirm_Delete = prompt(`Para confirmar a exclusão dos links selecionados, digite 'delete': `);
    if (confirm_Delete !== "delete") return;

    changeCursor_POST("wait");

    let _data = JSON.stringify(ArrayCheck_delete);
    let requestConf = {
        method: "POST",
        body: _data,
        headers: {
            "CSRF-Token": token.value,
            "Content-Type": "application/json; charset=UTF-8"
        }
    };
    fetch("/delete", requestConf )
        .then(handleFetchResponse())
        .then(response => {

            handleFetchResponse();
            checkCurrentPage("/admin", "reload");

            // if (response.status != "error") { checkCurrentPage("/admin", "reload") }
        })
    .catch( error => {
        changeCursor_POST("default");
        handleFetchResponse("generic-error", `Erro ao Apagar os links selecionados do banco de dados`);
    });
});

////
// APAGAR-LINK pelo botão na tabela ou TODOS os registros salvos
// document.querySelectorAll(".apagar_link").forEach(btn => {
//     btn.addEventListener("click", (e) => {
//         let short_code = btn.getAttribute("item-shortCode");
//         if( short_code === "links" ) { // Apagados todos os itens salvos
//             msgError = `Erro ao Apagar Todos os registros do banco de dados `;
//             let confirm_Delete = prompt(`Esta ação irá excluir TODOS os links salvos. Para confirmar, digite "links".`);
//             if (confirm_Delete !== "links") return;
//         } else { // Apagado apenas um único link
//             msgError = `Erro ao Apagar Link "${short_code}" `;
//             let confirm_Delete = prompt(`Para confirmar a exclusão do item, digite seu link curto: `);
//             if (confirm_Delete !== short_code) return;
//         } ////

//         changeCursor_POST("wait");

//         let requestConf = {
//             method: "DELETE", // POST
//             headers: {
//             "CSRF-Token": token.value,
//             "Content-Type": "application/json; charset=UTF-8"
//             }
//         };
//         fetch(`/delete/${short_code}`, requestConf )
//             .then(response => response.json() )
//             .then(response => {
//                 if (response.status != "error") { checkCurrentPage("/admin", "reload") }
//             })
//         .catch( error => {
//             changeCursor_POST("default");
//             console.error(error);
//             alert(msgError);
//         });

//     });
// });









////
// APAGAR-LINKs PELA PESQUISA == NÃO SERA MAIS USADO
// if (pesquisa_apagarItens) {
//     pesquisa_apagarItens.addEventListener("click", (e) => { // Apagados apenas os itens vísiveis nos resultados da pesquisa
//         msgError = `Erro ao Apagar os itens selecionados do banco de dados`;
//         let confirm_Delete = prompt(`Esta ação irá excluir TODOS os links salvos que tenham relação com o texto "${admin_input_filtro.value}". \nPara confirmar, digite novamente o termo pesquisado.`);
//         if (confirm_Delete !== admin_input_filtro.value) return;

//         changeCursor_POST("wait");

//         let _data = { search: admin_input_filtro.value };
//         let requestConf = {
//             method: "POST", // DELETE
//             body: JSON.stringify(_data),
//             headers: {
//                 "CSRF-Token": token.value,
//                 "Content-Type": "application/json; charset=UTF-8"
//             }
//         };
//         fetch("/delete", requestConf )
//         .then(response => response.json() )
//         .then(response => {
//             if (response.status != "error") { checkCurrentPage("/admin", "reload") }
//             changeCursor_POST("default");
//         })
//         .catch( error => {
//             changeCursor_POST("default");
//             console.error(error);
//             alert(msgError);
//         });
//     });
// }
