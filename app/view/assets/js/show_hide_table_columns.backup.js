/*
  https://phuoc.ng/collection/html-dom/show-or-hide-table-columns/
*/
function hideCol_table() {
  const table_html_menu = document.getElementById('table-menu');
  const table_html = document.getElementById('table-content-menu');
  const table_headers = [].slice.call(table_html.querySelectorAll('th'));
  const table_cells = [].slice.call(table_html.querySelectorAll('th, td'));
  const numColumns = table_headers.length;

  // Recuperar o estado das colunas do localStorage
  const hiddenColumns = JSON.parse(localStorage.getItem('hiddenColumns')) || [];

  // Limpar HTML/esvaziar menu de contexto
  table_html_menu.innerHTML = "";

  const thead = table_html.querySelector('thead');
  thead.addEventListener('contextmenu', function (e) {
    e.preventDefault();

    const rect = thead.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    table_html_menu.style.top = y + 'px';
    table_html_menu.style.left = x + 'px';
    table_html_menu.classList.toggle('table_container__menu--hidden');

    var rectlog = table_html_menu.getBoundingClientRect();
    console.log("## contextmenu => ", table_html_menu, rectlog.top, rectlog.right, rectlog.bottom, rectlog.left);

    document.addEventListener('click', documentClickHandler);
  });

  // Hide the menu when clicking outside of it
  const documentClickHandler = function (e) {

    const isClickedOutside = !table_html_menu.contains(e.target);
    // console.log("## documentClickHandler => ", isClickedOutside);

    if (isClickedOutside) {
      table_html_menu.classList.add('table_container__menu--hidden');
      document.removeEventListener('click', documentClickHandler);
    }
  };

  const showColumn = function (index) {
    table_cells
    .filter(function (cell) {
      return cell.getAttribute('data-column-index') === index + '';
    })
    .forEach(function (cell) {
      cell.style.display = '';
      cell.setAttribute('data-shown', 'true');
    });

    table_html_menu.querySelectorAll('[type="checkbox"][disabled]').forEach(function (checkbox) {
      checkbox.removeAttribute('disabled');
    });

    // Salvar preferências no localStorage
    const columnIndex = hiddenColumns.indexOf(index);
    if (columnIndex !== -1) {
      hiddenColumns.splice(columnIndex, 1);
      localStorage.setItem('hiddenColumns', JSON.stringify(hiddenColumns));
    }
  };

  const hideColumn = function (index) {
    table_cells
    .filter(function (cell) {
      return cell.getAttribute('data-column-index') === index + '';
    })
    .forEach(function (cell) {
      cell.style.display = 'none';
      cell.setAttribute('data-shown', 'false');
    });
    // How many columns are hidden
    const numHiddenCols = table_headers.filter(function (th) {
      return th.getAttribute('data-shown') === 'false';
    }).length;
    if (numHiddenCols === numColumns - 1) {
      // There's only one column which isn't hidden yet
      // We don't allow user to hide it
      const shownColumnIndex = thead
      .querySelector('[data-shown="true"]')
      .getAttribute('data-column-index');

      const checkbox = table_html_menu.querySelector(
        '[type="checkbox"][data-column-index="' + shownColumnIndex + '"]'
      );
      checkbox.setAttribute('disabled', 'true');
    }

    // checkbox desmarcado = remover a coluna do array de colunas ocultas do localStorage
    const columnIndex = hiddenColumns.indexOf(index);
    if (columnIndex === -1) {
      hiddenColumns.push(index);
      localStorage.setItem('hiddenColumns', JSON.stringify(hiddenColumns));
    }
  };

  table_cells.forEach(function (cell, index) {
    cell.setAttribute('data-column-index', index % numColumns);
    cell.setAttribute('data-shown', 'true');
  });

  table_headers.forEach(function (th, index) {
    // Build the menu item
    const li = document.createElement('li');
    const label = document.createElement('label');
    const checkbox = document.createElement('input');
    checkbox.setAttribute('type', 'checkbox');

    // Verificar se a coluna está salva no localStorage e desabilitar seu checkbox
    const isColumnHidden = hiddenColumns.includes(index); // checkbox.setAttribute('checked', 'true');
    checkbox.checked = !isColumnHidden;

    checkbox.setAttribute('data-column-index', index);
    checkbox.style.marginRight = '.25rem';

    const text = document.createTextNode(th.textContent);

    label.appendChild(checkbox);
    label.appendChild(text);
    label.style.display = 'flex';
    label.style.alignItems = 'center';
    li.appendChild(label);
    table_html_menu.appendChild(li);

    // Handle the event
    checkbox.addEventListener('change', function (e) {

      console.log("## change => ", e);

      e.target.checked ? showColumn(index) : hideColumn(index);
      table_html_menu.classList.add('table_container__menu--hidden');
    });
  });



  // Ocultar as colunas salvas no localStorage na atualização da tabela & na inicialização
  hiddenColumns.forEach(function (index) {
    hideColumn(index);
  });
} // function


const hideCol_checkTableData = setInterval( function(){
  const table_tbody = document.getElementById('table_tbody');
  const admin_table_tbody = document.getElementById('admin_table_tbody');

  const hasData1 = table_tbody && table_tbody.children.length > 0;
  const hasData2 = admin_table_tbody && admin_table_tbody.children.length > 0;

  if (hasData1 || hasData2) { // o HTML da tabela possui dados?

    clearInterval(hideCol_checkTableData);
    hideCol_table();
  }
}, 1000);
