 

document.addEventListener('DOMContentLoaded', async () => {
    
    const sessionRes = await fetch('../includes/session_api.php');
    const session = await sessionRes.json();

    const accessDenied = document.getElementById('access-denied');

    if (!session.loggedIn || (session.user.role !== 'admin' && session.user.role !== 'staff')) {
        if (accessDenied) {
            accessDenied.style.display = 'flex';
        } else {
            
            alert('Unauthorized access. Redirecting to login.');
        }
        setTimeout(() => window.location.href = '../login.html', 1500);
        return;
    }

    
    const adminName = document.getElementById('admin-name');
    const adminRole = document.getElementById('admin-role');
    
    if (adminName) adminName.textContent = session.user.name;
    if (adminRole) adminRole.textContent = session.user.role.toUpperCase();

   
    const logoutBtn = document.getElementById('admin-logout');
    if (logoutBtn) {
        logoutBtn.onclick = async (e) => {
            e.preventDefault();
            const confirmLogout = confirm('Are you sure you want to log out?');
            if (confirmLogout) {
                await fetch('../includes/auth_handler.php?action=logout');
                window.location.href = '../index.php';
            }
        };
    }

    
    const currentPath = window.location.pathname.split('/').pop();
    document.querySelectorAll('.sidebar-nav a').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
});


function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}


function getStatusBadge(status) {
    return `<span class="status-badge status-${status.toLowerCase()}">${status}</span>`;
}

document.addEventListener('DOMContentLoaded', async () => {
    
    setTimeout(async () => {
        
        if (document.getElementById('admin-dashboard-stats-grid')) { 
            const statsRes = await fetch('../php/api_stats.php'); 
            const statsData = await statsRes.json();
            if (statsData.success) {
                document.getElementById('stat-revenue').textContent = '$' + statsData.stats.revenue;
                if (document.getElementById('stat-collected')) document.getElementById('stat-collected').textContent = '$' + statsData.stats.collected;
                document.getElementById('stat-bookings').textContent = statsData.stats.bookings;
                document.getElementById('stat-pending').textContent = statsData.stats.pending;
                if (document.getElementById('stat-unpaid')) document.getElementById('stat-unpaid').textContent = statsData.stats.unpaid;
                document.getElementById('stat-queries').textContent = statsData.stats.queries;
                document.getElementById('stat-users').textContent = statsData.stats.users; // New stat
                document.getElementById('stat-packages').textContent = statsData.stats.packages; // New stat
            }

            const bookingsRes = await fetch('api_bookings.php');
            const bookingsData = await bookingsRes.json();
            if (bookingsData.success) {
                const table = document.getElementById('recent-bookings-table');
                bookingsData.data.slice(0, 5).forEach(b => {
                    const row = `
                        <tr>
                            <td>
                                <strong>${b.customer_name}</strong><br>
                                <span class="admin-customer-email">${b.customer_email}</span>
                            </td>
                            <td>${b.package_id}</td>
                            <td>${formatDate(b.travel_date)}</td>
                            <td>$${b.total_price}</td>
                            <td>${getStatusBadge(b.status)}</td>
                        </tr>
                    `;
                    table.innerHTML += row;
                });
            }

            const queriesRes = await fetch('api_queries.php');
            const queriesData = await queriesRes.json();
            if (queriesData.success) {
                const table = document.getElementById('recent-queries-table');
                queriesData.data.slice(0, 5).forEach(q => {
                    const row = `
                        <tr>
                            <td>${q.name}</td>
                            <td>${q.subject}</td>
                            <td>${formatDate(q.created_at)}</td>
                            <td>${getStatusBadge(q.status)}</td>
                        </tr>
                    `;
                    table.innerHTML += row;
                });
            }
        }

       
        if (document.getElementById('bookings-tbody') && !document.getElementById('stat-revenue')) {
         
            const res = await fetch('api_bookings.php');
            const result = await res.json();
            
            if (result.success) {
                const tbody = document.getElementById('bookings-tbody');
                result.data.forEach(b => {
                    const row = `
                        <tr>
                            <td>#${b.id}</td>
                            <td>
                                <strong>${b.customer_name}</strong><br>
                                <span class="admin-customer-email-dark">${b.customer_email}</span>
                            </td>
                            <td>${b.package_id}</td>
                            <td>${formatDate(b.travel_date)}</td>
                            <td>${b.travelers}</td>
                            <td>$${b.total_price}</td>
                            <td>${getStatusBadge(b.status)}</td>
                            <td>
                                <button class="btn-action admin-btn-action-edit"><i class="fas fa-edit"></i></button>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            }
        }

       
        if (document.getElementById('queries-container')) {
            const res = await fetch('api_queries.php');
            const result = await res.json();
            
            if (result.success) {
                const container = document.getElementById('queries-container');
                if (result.data.length === 0) {
                    container.innerHTML = '<p class="admin-no-queries">No inquiries found.</p>';
                }
                result.data.forEach(q => {
                    const card = `
                        <div class="query-card">
                            <div class="query-header">
                                <div>
                                    <h4 class="admin-query-name">${q.name} <span class="query-badge">${q.status}</span></h4>
                                    <small class="admin-query-email">${q.email}</small>
                                </div>
                                <span class="admin-query-date">${formatDate(q.created_at)}</span>
                            </div>
                            <p class="admin-query-subject">Subject: ${q.subject}</p>
                            <div class="query-message">${q.message}</div>
                        </div>
                    `;
                    container.innerHTML += card;
                });
            }
        }
    }, 500);
});
