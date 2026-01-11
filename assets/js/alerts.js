
function showAlert(type, message) {
  const alert = document.createElement("div");
  alert.className = `fixed top-5 right-5 z-50 px-6 py-4 rounded-xl shadow-xl text-white transform transition-all duration-500 ${
    type === "success" ? "bg-emerald-500" : "bg-amber-500"
  }`;

  alert.style.opacity = "0";
  alert.style.transform = "translateY(-20px) scale(0.95)";
  alert.innerHTML = message;

  document.body.appendChild(alert);

  setTimeout(() => {
    alert.style.opacity = "1";
    alert.style.transform = "translateY(0) scale(1)";
  }, 100);

  setTimeout(() => {
    alert.style.opacity = "0";
    alert.style.transform = "translateY(-20px) scale(0.95)";
    setTimeout(() => alert.remove(), 500);
  }, 3000);
}
