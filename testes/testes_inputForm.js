/////// METÓDO DE POST 1
curl -X POST -F 'nome=ei' -F 'oi=oi' http://localhost:9000/qrlink/




/////// METÓDO DE POST 2
//////
// => SUBSTITUIR O VALOR DE "submitToken" PARA O ATUAL USADO NA PÁGINA
let time = 0;
let tms_Max = 50;
let response;
async function criarLinks(){
	try {
		response = await fetch("https://l.hauck.net.br/link/save", {
                    "credentials": "include",
                    "headers": {
                        "User-Agent": "Mozilla/5.0 (X11; Linux x86_64; rv:102.0) Gecko/20100101 Firefox/102.0",
                        "Accept": "*/*",
                        "Accept-Language": "pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3",
                        "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
                        "Sec-Fetch-Dest": "empty",
                        "Sec-Fetch-Mode": "cors",
                        "Sec-Fetch-Site": "same-origin",
                        "Sec-GPC": "1",
                        "Pragma": "no-cache",
                        "Cache-Control": "no-cache"
                    },
                    "body": `inputURL=https%3A%2F%2Fl.hauck.net.br/${tms}&submitToken=ac285017b8de73df2dea9640ba5a9185`,
                    "method": "POST",
                    "mode": "cors"
                });
		
		    console.warn("link: ", tms);
		    console.log(response);
	    } catch(err) {
		    console.error(err);
	    }
}

setInterval(function() {
	tms = time++;
	if (tms == tms_Max){
	    alert(`Atingido número de ${tms} links. \nA página será recarregada.`);
	    window.location.reload();
	}
	
	criarLinks();
}, 200 );
