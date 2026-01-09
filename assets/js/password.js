function initPasswordStrength(inputId, strengthId){
  const input = document.getElementById(inputId);
  const strength = document.getElementById(strengthId);

  input.addEventListener("input", function(){
    const val = input.value;
    let score = 0;
    if(val.length >= 6) score++;
    if(/[A-Z]/.test(val)) score++;
    if(/[0-9]/.test(val)) score++;
    if(/[\W]/.test(val)) score++;

    let text="", color="";
    switch(score){
      case 0: text=""; break;
      case 1: text="Weak"; color="red"; break;
      case 2: text="Fair"; color="orange"; break;
      case 3: text="Good"; color="blue"; break;
      case 4: text="Strong"; color="green"; break;
    }
    strength.innerText = text;
    strength.style.color = color;
  });
}
