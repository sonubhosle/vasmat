
const scrollBox = document.getElementById("announcementScroll");
let scrollAmount = 0;
let scrollInterval;

// Start auto-scroll
function startScroll() {
  scrollInterval = setInterval(() => {
    scrollAmount++;
    scrollBox.scrollTop = scrollAmount;

    if (scrollAmount >= scrollBox.scrollHeight - scrollBox.clientHeight) {
      scrollAmount = 0;
    }
  }, 40);
}

// Pause on hover
scrollBox.addEventListener("mouseenter", () => clearInterval(scrollInterval));
scrollBox.addEventListener("mouseleave", startScroll);

startScroll();

