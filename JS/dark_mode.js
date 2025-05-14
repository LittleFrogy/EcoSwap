
document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.getElementById('theme-toggle');
    const currentTheme = localStorage.getItem('theme') || 'light';

    // Apply the saved theme on load
        if (currentTheme === 'dark') {
         document.documentElement.setAttribute('data-theme', 'dark');
            themeToggle.textContent = 'Light Mode';
            document.querySelector('link[href="css/style.css"]').href = 'css/dark.css';
    }
    themeToggle.addEventListener('click', () => {
            const newTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);

        // Update styles
            if (newTheme === 'dark') {
            themeToggle.textContent = 'Light Mode';
                document.querySelector('link[href="css/style.css"]').href = 'css/dark.css';
        } else {
            themeToggle.textContent = 'Dark Mode';
                document.querySelector('link[href="css/style.css"]').href = 'css/style.css';
        }
    });
});
