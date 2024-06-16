/*
  https://phuoc.ng/collection/html-dom/show-or-hide-table-columns/
*/

// window.toggleColumnVisibility = function(action, type = false) {


function toggleColumnVisibility(action, type = false) { // (load|update, true|false)
  const table_html = document.getElementById('table-content-menu');
  const table_html_menu = document.getElementById('table-menu');

  const table_headers = Array.from(table_html.querySelectorAll('th'));
  const table_cells = Array.from(table_html.querySelectorAll('th, td'));
  const numColumns = table_headers.length;

  // Limpar HTML/esvaziar menu de contexto
  //// table_html_menu.innerHTML = "";

  // Recuperar o estado das colunas do localStorage
  const hiddenColumns = localStorage_link_get("hiddenColumns") || [];

  function localStorage_menu_change(index){ // Salvar preferências da tabela no localStorage
      const columnIndex = hiddenColumns.indexOf(index);

      if (type && columnIndex !== -1){ // mostrar colunas = remover item
        hiddenColumns.splice(columnIndex, 1);
      }
      else if (!type && columnIndex === -1) { // ocultar colunas = adicionar item
        hiddenColumns.push(index);
      }

    localStorage.setItem("hiddenColumns", JSON.stringify(hiddenColumns));
  }

  function showColumn(index) {
    table_cells
    .filter(cell => cell.getAttribute('data-column-index') === index + '')
    .forEach(cell => {
      cell.style.display = '';
      cell.setAttribute('data-shown', 'true');
    });

    table_html_menu.querySelectorAll('[type="checkbox"][disabled]').forEach(checkbox => {
      checkbox.removeAttribute('disabled');
    });

    if (action == "load"){
      localStorage_menu_change(index, true);
    }
  }

  function hideColumn(index) {
    table_cells
    .filter(cell => cell.getAttribute('data-column-index') === index + '')
    .forEach(cell => {
      cell.style.display = 'none';
      cell.setAttribute('data-shown', 'false');
    });

    const numHiddenCols = table_headers.filter(th => th.getAttribute('data-shown') === 'false').length;

    if (numHiddenCols === numColumns - 1) {
      const shownColumnIndex = table_headers.findIndex(th => th.getAttribute('data-shown') === 'true');
      const checkbox = table_html_menu.querySelector('[type="checkbox"][data-column-index="' + shownColumnIndex + '"]');
      checkbox.setAttribute('disabled', 'true');
    }

    if (action == "load"){
      localStorage_menu_change(index, true);
    }
  }

  if (action == "load"){ // Construir o Menu
    const thead = table_html.querySelector("thead");
    thead.addEventListener("contextmenu", function (e) {
      e.preventDefault();

      const rect = thead.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;

      table_html_menu.style.top = y + "px";
      table_html_menu.style.left = x + "px";
      table_html_menu.classList.toggle("table_container__menu--hidden");

      document.addEventListener("click", documentClickHandler);
    });

    const documentClickHandler = function (e) {
      const isClickedOutside = !table_html_menu.contains(e.target);

      if (isClickedOutside) {
        table_html_menu.classList.add("table_container__menu--hidden");
        document.removeEventListener("click", documentClickHandler);
      }
    };

    table_cells.forEach((cell, index) => {
      cell.setAttribute("data-column-index", index % numColumns);
      cell.setAttribute("data-shown", "true");
    });

    table_headers.forEach((th, index) => {
      const li = document.createElement("li");
      const label = document.createElement("label");
      const checkbox = document.createElement("input");
      checkbox.setAttribute("type", "checkbox");

      const isColumnHidden = hiddenColumns.includes(index);
      checkbox.checked = !isColumnHidden;

      checkbox.setAttribute("data-column-index", index);
      checkbox.style.marginRight = ".25rem";

      const text = document.createTextNode(th.textContent);

      label.appendChild(checkbox);
      label.appendChild(text);
      label.style.display = "flex";
      label.style.alignItems = "center";
      li.appendChild(label);
      table_html_menu.appendChild(li);

      checkbox.addEventListener("change", function (e) {
        e.target.checked ? showColumn(index) : hideColumn(index);
        table_html_menu.classList.add("table_container__menu--hidden");
      });
    });
  } // if (action == "load")

  // Ocultar as colunas salvas no localStorage na atualização da tabela & na inicialização
  hiddenColumns.forEach((columnIndex) => {
    if (type) {
      console.log("type", type);
      showColumn(columnIndex, type);
    } else {
       console.log("type", type);
      hideColumn(columnIndex, type);
    }
  });
} // function


const timer_toggleColumnVisibility = setInterval(() => {
  const table_tbody = document.getElementById('table_tbody');
  const admin_table_tbody = document.getElementById('admin_table_tbody');

  const hasData1 = table_tbody && table_tbody.children.length > 0;
  const hasData2 = admin_table_tbody && admin_table_tbody.children.length > 0;

  if (hasData1 || hasData2) { // o HTML da tabela possui dados?
    clearInterval(timer_toggleColumnVisibility);
    toggleColumnVisibility("load", false);
  }
}, 1000);
