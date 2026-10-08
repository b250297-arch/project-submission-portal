const STORAGE_KEY = 'aj_project_portal_data';

function getProjects() {
    const stored = localStorage.getItem(STORAGE_KEY);
    return stored ? JSON.parse(stored) : [];
}

function saveProjects(projects) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(projects));
}

function renderRecentProjects() {
    const list = document.getElementById('recentSubmissions');
    if (!list) return;

    const projects = getProjects().slice().reverse();
    list.innerHTML = '';

    if (!projects.length) {
        list.innerHTML = '<li>No project submissions yet.</li>';
        return;
    }

    projects.slice(0, 5).forEach((project) => {
        const item = document.createElement('li');
        item.innerHTML = `<strong>${project.project_title}</strong><span>by ${project.name}</span><small>${project.status || 'Pending'}</small>`;
        list.appendChild(item);
    });
}

function renderAdminProjects() {
    const container = document.getElementById('adminContent');
    if (!container) return;

    const projects = getProjects();
    const search = (document.getElementById('searchInput')?.value || '').toLowerCase();
    const status = document.getElementById('statusFilter')?.value || 'all';

    const filtered = projects.filter((project) => {
        const matchesSearch = !search || [project.name, project.student_id, project.project_title].join(' ').toLowerCase().includes(search);
        const matchesStatus = status === 'all' || (project.status || 'Pending') === status;
        return matchesSearch && matchesStatus;
    });

    if (!filtered.length) {
        container.innerHTML = '<p>No submissions found.</p>';
        return;
    }

    const rows = filtered.map((project) => `
        <tr>
            <td>${project.name}</td>
            <td>${project.student_id}</td>
            <td>${project.project_title}</td>
            <td>${project.department}</td>
            <td>${project.technology}</td>
            <td>${project.status || 'Pending'}</td>
            <td>${project.submitted_at}</td>
            <td>
                <select class="statusSelect" data-id="${project.id}">
                    <option value="Pending" ${project.status === 'Pending' ? 'selected' : ''}>Pending</option>
                    <option value="Approved" ${project.status === 'Approved' ? 'selected' : ''}>Approved</option>
                    <option value="Rejected" ${project.status === 'Rejected' ? 'selected' : ''}>Rejected</option>
                </select>
            </td>
        </tr>
    `).join('');

    container.innerHTML = `
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Student</th><th>Roll No.</th><th>Project</th><th>Department</th><th>Technology</th><th>Status</th><th>Submitted</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
        </div>
    `;

    container.querySelectorAll('.statusSelect').forEach((select) => {
        select.addEventListener('change', (event) => {
            const projectsData = getProjects();
            const targetId = event.target.getAttribute('data-id');
            const project = projectsData.find((item) => item.id === targetId);
            if (project) {
                project.status = event.target.value;
                saveProjects(projectsData);
                renderAdminProjects();
            }
        });
    });
}

function handleSubmission(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const project = Object.fromEntries(formData.entries());
    project.id = 'project_' + Date.now();
    project.status = 'Pending';
    project.submitted_at = new Date().toLocaleString();

    const projects = getProjects();
    projects.push(project);
    saveProjects(projects);

    const message = document.getElementById('formMessage');
    if (message) {
        message.textContent = 'Project submitted successfully.';
        message.className = 'message success';
        message.style.display = 'block';
    }
    form.reset();
    renderRecentProjects();
}

function handleLogin(event) {
    event.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const message = document.getElementById('loginMessage');

    if (username === 'admin' && password === 'admin123') {
        sessionStorage.setItem('adminLoggedIn', 'true');
        window.location.href = 'admin.html';
    } else {
        if (message) {
            message.textContent = 'Invalid admin credentials.';
            message.className = 'message error';
            message.style.display = 'block';
        }
    }
}

function protectAdminPage() {
    if (window.location.pathname.includes('admin.html') && sessionStorage.getItem('adminLoggedIn') !== 'true') {
        window.location.href = 'admin_login.html';
    }
}

function handleLogout() {
    sessionStorage.removeItem('adminLoggedIn');
    window.location.href = 'index.html';
}

document.addEventListener('DOMContentLoaded', () => {
    renderRecentProjects();
    protectAdminPage();
    renderAdminProjects();

    const form = document.getElementById('submissionForm');
    if (form) form.addEventListener('submit', handleSubmission);

    const loginForm = document.getElementById('loginForm');
    if (loginForm) loginForm.addEventListener('submit', handleLogin);

    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', (event) => {
            event.preventDefault();
            renderAdminProjects();
        });
    }

    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) logoutBtn.addEventListener('click', (event) => {
        event.preventDefault();
        handleLogout();
    });
});
