// Authentication check for protected pages
document.addEventListener('DOMContentLoaded', function() {
    const isLoggedIn = localStorage.getItem('loggedIn');
    const userRole = localStorage.getItem('userRole');
    const currentPage = window.location.pathname;
    
    // Redirect to login if not authenticated
    if (!isLoggedIn) {
        window.location.href = 'login.php';
        return;
    }
    
    // Role-based access control
    if (currentPage.includes('admin.html') && userRole !== 'admin') {
        alert('Access Denied! Admin access required.');
        window.location.href = 'login.php';
        return;
    }
    
    if (currentPage.includes('index2.html') && userRole !== 'restaurant') {
        alert('Access Denied! Restaurant access required.');
        window.location.href = 'login.php';
        return;
    }
    
    // Display user info
    const username = localStorage.getItem('username');
    if (username) {
        const userElement = document.querySelector('.user-info span');
        if (userElement) {
            userElement.textContent = username.charAt(0).toUpperCase() + username.slice(1);
        }
    }
});