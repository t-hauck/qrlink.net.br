let date = ("0" + now.getDate()).slice(-2) + "-" + ("0"+(now.getMonth()+1)).slice(-2) + "-" + now.getFullYear(); // + " " + ("0" + now.getHours()).slice(-2) + ":" + ("0" + now.getMinutes()).slice(-2);
// 16-05-2015 09:50




let table = document.getElementById('export_table');
let links_exportBtn = document.getElementById('export');

// links_exportBtn == isVisible.js
links_exportBtn.addEventListener('click', function () {
    const csv = toCsv(table); // Export to csv
    download(csv, 'qrlink.net.br_' + date + '.csv'); // Download it
});


const toCsv = function (table) {
    // Query all rows
    const rows = table.querySelectorAll('tr');

    return [].slice
        .call(rows)
        .map(function (row) {
            // Query all cells
            const cells = row.querySelectorAll('th,td');
            return [].slice
                .call(cells)
                .map(function (cell) {
                    return cell.textContent;
                })
                .join(',');
        })
        .join('\n');
};

const download = function (text, fileName) {
    const link = document.createElement('a');
    link.setAttribute('href', `data:text/csv;charset=utf-8,${encodeURIComponent(text)}`);
    link.setAttribute("download", fileName);

    link.style.display = 'none';
    document.body.appendChild(link);

    link.click();

    document.body.removeChild(link);
};
