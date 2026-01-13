// assets/js/password.js
function initPasswordStrength(inputId, strengthId){
  const input = document.getElementById(inputId);
  const strength = document.getElementById(strengthId);

  input.addEventListener("input", function(){
    const val = input.value;
    let score = 0;
    const messages = [];
    
    if(val.length >= 8) score++;
    if(val.length >= 12) score++;
    if(/[A-Z]/.test(val)) score++;
    if(/[a-z]/.test(val)) score++;
    if(/[0-9]/.test(val)) score++;
    if(/[\W_]/.test(val)) score++;
    
    let text = "", color = "", width = "0%";
    
    if(val.length === 0) {
        text = "";
        width = "0%";
    } else if(score < 3) {
        text = "Weak";
        color = "#ef4444";
        width = "25%";
    } else if(score < 5) {
        text = "Fair";
        color = "#f97316";
        width = "50%";
    } else if(score < 7) {
        text = "Good";
        color = "#3b82f6";
        width = "75%";
    } else {
        text = "Strong";
        color = "#10b981";
        width = "100%";
    }
    
    strength.innerHTML = `
        <div class="flex items-center justify-between mb-1">
            <span>${text}</span>
            <span>${val.length}/12</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="h-2 rounded-full transition-all duration-300" style="width: ${width}; background-color: ${color};"></div>
        </div>
    `;
  });
}