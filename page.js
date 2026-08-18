document.addEventListener('DOMContentLoaded', function() {
    const sideMenu = document.querySelector("aside");
    const menuBtn = document.querySelector("#menu-btn");
    const closeBtn = document.querySelector("#close-btn");

    if (menuBtn && closeBtn && sideMenu) {
        menuBtn.addEventListener('click', () => {
            sideMenu.style.display = 'block';
        });

        closeBtn.addEventListener('click', () => {
            sideMenu.style.display = 'none';
        });
    } else {
        console.error('One or more elements (menuBtn, closeBtn, sideMenu) not found in the DOM');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const body = document.body;
    const themeToggle = document.querySelector(".theme-toggle");

    // Retrieve current theme from localStorage
    const currentTheme = localStorage.getItem("theme");

    // Initial theme setup based on localStorage
    if (currentTheme) {
        body.classList.add(currentTheme);
        if (currentTheme === "dark") {
            themeToggle.querySelector("span:nth-child(1)").classList.remove('active');
            themeToggle.querySelector("span:nth-child(2)").classList.add('active');
        } else {
            themeToggle.querySelector("span:nth-child(1)").classList.add('active');
            themeToggle.querySelector("span:nth-child(2)").classList.remove('active');
        }
    }

    // Toggle theme function
    const toggleTheme = () => {
        body.classList.toggle("dark");
        
        const theme = body.classList.contains("dark") ? "dark" : "light";
        localStorage.setItem("theme", theme);
        
        themeToggle.querySelector("span:nth-child(1)").classList.toggle('active');
        themeToggle.querySelector("span:nth-child(2)").classList.toggle('active');
    };

    // Event listener for theme toggle button
    themeToggle.addEventListener('click', toggleTheme);
});


// Current Time
function updateTime() {
    const now = new Date();
    const options = { timeZone: 'Asia/Manila', hour12: true, hour: 'numeric', minute: 'numeric', second: 'numeric' };
    const currentTime = now.toLocaleTimeString('en-US', options);
    document.getElementById('time').textContent = currentTime;
}
updateTime();
setInterval(updateTime, 1000);


// Current Date
var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
var currentDate = new Date();
var day = currentDate.getDate();
var monthIndex = currentDate.getMonth();
var year = currentDate.getFullYear();
var monthName = months[monthIndex];
var formattedDate = monthName + " " + day + ", " + year;
document.getElementById("currentDate").innerHTML = formattedDate;
