/*================================================================================
	Item Name: Materialize - Material Design Admin Template
	Version: 5.0
	Author: PIXINVENT
	Author URL: https://themeforest.net/user/pixinvent/portfolio
================================================================================

NOTE:
------
PLACE HERE YOUR OWN JS CODES AND IF NEEDED.
WE WILL RELEASE FUTURE UPDATES SO IN ORDER TO NOT OVERWRITE YOUR CUSTOM SCRIPT IT'S BETTER LIKE THIS. */

$(document).ready(function () {

	$.ajax({
		type: "POST",
		data: {
		  invoiceno:jobid
		},
		url: "../../../../../public_html/Nerdweb/Database.php",
		dataType: "html",
		async: false,
		success: function(data) {
		  result=data;
		}
	  }); 
                                                                
	//let noticias = new NoticiaCRUD();
	
	const lista = noticias.selecionaNoticias();
	
	const row = document.createElement("tr");
	
	lista.map(item => {
		const idCollumn = document.createElement("td");
		const id = document.createTextNode(item.id);
		const titleCollumn = document.createElement("td");
		const title = document.createTextNode(item.title);
		const dataCollumn = document.createElement("td");
		const data = document.createTextNode(item.data);
		const urlCollumn = document.createElement("td");
		const url = document.createTextNode(item.url_noticia);
		
		idCollumn.appendChild(id);
		titleCollumn.appendChild(title);
		dataCollumn.appendChild(data);
		urlCollumn.appendChild(url);
		row.appendChild(idCollumn);
		row.appendChild(titleCollumn);
		row.appendChild(dataCollumn);
		row.appendChild(urlCollumn);
	})

	const idCollumn = document.createElement("td");
		const id = document.createTextNode("teste");
		const titleCollumn = document.createElement("td");
		const title = document.createTextNode("oi");
		const dataCollumn = document.createElement("td");
		const data = document.createTextNode("olá");
		const urlCollumn = document.createElement("td");
		const url = document.createTextNode("tudo bem?");
		
		idCollumn.appendChild(id);
		titleCollumn.appendChild(title);
		dataCollumn.appendChild(data);
		urlCollumn.appendChild(url);
		row.appendChild(idCollumn);
		row.appendChild(titleCollumn);
		row.appendChild(dataCollumn);
		row.appendChild(urlCollumn);
	
	document.getElementById("lista-noticias").appendChild(row);
});