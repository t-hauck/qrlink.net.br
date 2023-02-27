////
// APAGAR-LINK
let token = document.getElementById("formToken");
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
            body: JSON.stringify(_data),
            headers: {"Content-Type": "application/json; charset=UTF-8"}
        }
        fetch( "/link/delete", requestConf )
            .then(response => response.json() )
            .then(response => {

                if (response.status === "delete") { // delete == OK/Sucesso
                    setTimeout(() => { location.reload(); }, 1000);
                }
            })
        .catch( error => {
            console.error(error);
            alert(msgError);
        });

    });
    
});
