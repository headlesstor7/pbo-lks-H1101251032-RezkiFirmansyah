import { renderDashboard } from './pages/dashboard.js';
import { renderBooksPage } from './pages/books.js';
// import { renderCategoriesPage } from './pages/categories.js'; // aktifkan di Langkah 19
import { escapeHtml } from './utils.js';

const routes = {
  dashboard: renderDashboard,
  books: renderBooksPage,
  // categories: renderCategoriesPage, // aktifkan di Langkah 19
};

async function router() {
  const hash = (window.location.hash.slice(1) || 'dashboard').split('?')[0];
  const renderFn = routes[hash] || renderDashboard;

  document.querySelectorAll('.nav-link').forEach(link => {
    const linkHash = link.getAttribute('href').slice(1);
    if (linkHash === hash) {
      link.classList.add('bg-blue-700');
    } else {
      link.classList.remove('bg-blue-700');
    }
  });

  const container = document.getElementById('app');
  container.innerHTML = '<div class="text-center py-16 text-gray-400">Memuat...</div>';

  try {
    await renderFn(container);
  } catch (error) {
    console.error(error);
    container.innerHTML = `
      <div class="text-center py-16">
        <p class="text-red-600 font-medium">Terjadi kesalahan</p>
        <p class="text-sm text-gray-500 mt-2">${escapeHtml(error.message)}</p>
      </div>
    `;
  }
}

window.addEventListener('hashchange', router);
window.addEventListener('DOMContentLoaded', router);