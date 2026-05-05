// MOCK DATA (remplacé plus tard par API PHP/MySQL)
const data = Array.from({ length: 120 }, (_, i) => ({
  id: i + 1,
  conversation_id: Math.floor(Math.random() * 50) + 1
}));

let currentPage = 1;
let limit = 10;

document.getElementById("limitSelect").addEventListener("change", (e) => {
  limit = parseInt(e.target.value);
  currentPage = 1;
  renderTable();
});

function renderTable() {
  const start = (currentPage - 1) * limit;
  const end = start + limit;

  const pageData = data.slice(start, end);

  const tbody = document.getElementById("tableBody");
  tbody.innerHTML = "";

  pageData.forEach(row => {
    tbody.innerHTML += `
      <tr>
        <td>${row.id}</td>
        <td>${row.conversation_id}</td>
      </tr>
    `;
  });

  renderPagination();
}

function renderPagination() {
  const totalPages = Math.ceil(data.length / limit);
  const pagination = document.getElementById("pagination");

  pagination.innerHTML = "";

  // PREVIOUS BUTTON
  pagination.innerHTML += `
    <button onclick="prevPage()" ${currentPage === 1 ? "disabled" : ""}>
      ◀ Prev
    </button>
  `;

  // CURRENT PAGE INFO
  pagination.innerHTML += `
    <span style="margin: 0 10px;">
      Page ${currentPage} / ${totalPages}
    </span>
  `;

  // NEXT BUTTON
  pagination.innerHTML += `
    <button onclick="nextPage()" ${currentPage === totalPages ? "disabled" : ""}>
      Next ▶
    </button>
  `;
}

function goToPage(page) {
  currentPage = page;
  renderTable();
}

// INIT
renderTable();


function prevPage() {
  if (currentPage > 1) {
    currentPage--;
    renderTable();
  }
}

function nextPage() {
  const totalPages = Math.ceil(data.length / limit);

  if (currentPage < totalPages) {
    currentPage++;
    renderTable();
  }
}