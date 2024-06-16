function handleFetchResponse(type, text) {
    return function(response) {
        if (type == "generic-error"){ // catch, erros gerais na requisição
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                html: text + `<br>Mensagem de erro: <br><br>${response}`,
                showConfirmButton: true,
            });
        }else {

            var SwalTimer = 10000; // 10 segundos

            if (response.status === 405) {
                if (type == "localstorage-error"){
                    NextTableUpdate("", true);
                }else {
                    Swal.fire({
                        icon: "warning",
                        title: "Atenção",
                        html: "Este site não exige cadastro, mas para garantir sua segurança online, você deve recarregar manualmente esta página para continuar.",
                        showConfirmButton: true,
                        timer: SwalTimer,
                    });
                }
                throw error;

            } else if (response.status === 408) {
                Swal.fire({
                    icon: "warning",
                    title: "Atenção",
                    html: "Sua solicitação demorou mais que o esperado, tente novamente mais tarde.",
                    showConfirmButton: true,
                    timer: SwalTimer,
                });
                throw error;

            } else if (response.status === 429) {
                Swal.fire({
                    icon: "warning",
                    title: "Atenção",
                    html: "Você está fazendo muitas solicitações, tente novamente mais tarde.",
                    showConfirmButton: true,
                    timer: SwalTimer,
                });
                throw error;

            } else if (response.status === 500) {
                Swal.fire({
                    icon: "error",
                    title: "Erro Interno",
                    html: "Ocorreu um erro interno no servidor. Não há nada que você possa fazer, tente novamente mais tarde.",
                    showConfirmButton: true,
                    timer: SwalTimer,
                });
                throw error;
            } else if (response.status === 503) {
                SwalTimer = 20000; // 20 segundos

                return response.text().then((res) => { // .text = HTML
                    let server_message  = res ? res : "desconhecido";
                    console.error("[ QR-Link ]", response.status, server_message);

                    Swal.fire({
                        icon: "error",
                        title: "Recurso Indisponível",
                        html: "Sistema indisponível para manutenção. Se este erro persistir, entre em contato conosco. <br><u>Mensagem de erro</u>: <br><br>" + server_message,
                        showConfirmButton: true,
                        timer: SwalTimer,
                    });
                    throw error;
                }); // return

            }else if (response.status === 200) {
                    return response.json().then((res) => {

                        if (res.status == "error" && res.message){ // fetch, LinkController.php
                            Swal.fire({
                                icon: "error",
                                title: "Erro!",
                                html: res.message,
                                showConfirmButton: true,
                            });
                            throw res;
                        } else if (res.status == "error-redirect" && res.message){ // fetch, LinkController.php >>  POST /checkPassword
                            Toast.fire({ // error-redirect == status diferente apenas para usar a notificação "toast" do SweetAlert
                                icon: 'error',
                                title: res.message
                            });
                            throw res; 
                        } else{
                            return res; // RETORNAR REPOSTA EM CASO DE SUCESSO
                        }
                    }); // return
            }else if (response.status === 204) { // sucesso na requisição e nenhuma resposta enviada
                return;
            }else {     // erros não tratados que passam por essa função irão cair aqui
                console.error(response);
            }
        }
    };
}
