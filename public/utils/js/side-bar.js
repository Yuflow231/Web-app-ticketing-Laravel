/**
 * SideBar navigation
 */

let hamburger = document.querySelector(".hamburger");
let sidebar = document.querySelector(".side-nav");

hamburger.addEventListener("click", () => {
    toggle();
})

function toggle() {
    sidebar.classList.toggle('collapsed');
    updateSidebarWidth();
}

function updateSidebarWidth() {
    const width = sidebar.classList.contains('collapsed') ? 'var(--side-nav-collapsed-width)' : 'var(--side-nav-opened-width)';
    document.documentElement.style.setProperty('--side-nav-width', width);
}


window.addEventListener("load", () => {
    let w = window.innerWidth;
    if(w < 768) {
        sidebar.classList.add("collapsed");
        updateSidebarWidth();
    }
})