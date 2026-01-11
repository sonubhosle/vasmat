
// MOBILE NAVIGATION MENU
const openMobile = document.getElementById("openMobile");
const closeMobile = document.getElementById("closeMobile");
const mobileOverlay = document.getElementById("mobileOverlay");
const mobileMenu = document.getElementById("mobileMenu");
const mobileBg = document.getElementById("mobileBg");

openMobile.onclick = () => {
    mobileOverlay.classList.remove("opacity-0", "pointer-events-none");
    mobileMenu.classList.remove("-translate-x-full");
};

closeMobile.onclick = closeSidebar;
mobileBg.onclick = closeSidebar;

function closeSidebar() {
    mobileOverlay.classList.add("opacity-0", "pointer-events-none");
    mobileMenu.classList.add("-translate-x-full");
}

function toggleAccordion(id) {
    const content = document.getElementById(id);
    const icon = document.getElementById("icon-" + id);
    const dot = document.getElementById("dot-" + id);

    // Check if it's currently open
    const isOpen = content.classList.contains("max-h-[500px]") || content.classList.contains("opacity-100");

    // Close all accordions first
    document.querySelectorAll('[id^="mob"]').forEach(el => {
        if (el.id.includes('Courses') || el.id.includes('Facilities') || el.id.includes('Student')) {
            el.classList.remove("max-h-[500px]", "opacity-100");
            el.classList.add("max-h-0", "opacity-0");
        }
    });
    
    // Reset all icons
    document.querySelectorAll('[id^="icon-mob"]').forEach(el => {
        el.classList.remove("rotate-180", "text-amber-600");
    });
    
    // Reset all dots
    document.querySelectorAll('[id^="dot-mob"]').forEach(el => {
        el.classList.replace("bg-amber-600", "bg-slate-200");
    });

    // If the clicked accordion was closed, open it
    if (!isOpen) {
        content.classList.remove("max-h-0", "opacity-0");
        content.classList.add("max-h-[500px]", "opacity-100");
        if (icon) icon.classList.add("rotate-180", "text-amber-600");
        if (dot) dot.classList.replace("bg-slate-200", "bg-amber-600");
    }
}



const bigHeader = document.getElementById("bigHeader");
const navBar = document.getElementById("navBar");
const brandMiniDesktop = document.getElementById("brandMiniDesktop");
const brandMiniMobile = document.getElementById("brandMiniMobile");
const navInner = document.getElementById("navInner");
let isCollapsed = false;

window.addEventListener("scroll", () => {
  if (window.scrollY > 120 && !isCollapsed) {
    isCollapsed = true;
    bigHeader.classList.remove("max-h-96", "pt-6", "pb-3", "opacity-100");
    bigHeader.classList.add("max-h-0", "opacity-0", "pb-0", "pt-0");
    navBar.classList.add("py-3", "shadow-lg", "border-b", "border-slate-100");
    
    // Desktop changes
    brandMiniDesktop.classList.remove("opacity-0", "-translate-x-10", "pointer-events-none");
    navInner.classList.remove("justify-center");
    navInner.classList.add("justify-end");
    
    // Mobile changes
    brandMiniMobile.classList.remove("opacity-0", "-translate-x-10", "pointer-events-none");
    mobileRightButton.classList.remove("opacity-0", "pointer-events-none");
    
  } else if (window.scrollY <= 0 && isCollapsed) {
    isCollapsed = false;
    bigHeader.classList.remove("max-h-0", "opacity-0", "pb-0", "pt-0");
    bigHeader.classList.add("max-h-96", "pt-6", "pb-3", "opacity-100");
    navBar.classList.remove("py-3", "shadow-lg", "border-b", "border-slate-100");
    
    // Desktop changes
    brandMiniDesktop.classList.add("opacity-0", "-translate-x-10", "pointer-events-none");
    navInner.classList.remove("justify-end");
    navInner.classList.add("justify-center");
    
    // Mobile changes
    brandMiniMobile.classList.add("opacity-0", "-translate-x-10", "pointer-events-none");
  }
});



