
<div id="welcomePopup" class="fixed inset-0  z-[99999] hidden items-center justify-center p-5 bg-black/80 backdrop-blur-sm">
    <div class="bg-white text-center rounded-3xl p-8 md:p-10 max-w-md w-full relative shadow-2xl shadow-black/30 transform transition-all duration-300 scale-95 opacity-0"
         id="popupContent">
        
        <!-- Close Button -->
        <button onclick="closeWelcomePopup()" 
                class="absolute top-4 right-4 w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:text-gray-800 hover:bg-gray-200 transition-all duration-200"
                title="Close">
        <i class='bx bx-x text-[25px]'></i>
        </button>
        
       <h1 class="text-3xl font-black text-slate-900 leading-[0.95] tracking-tighter mb-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
          WELCOME IN <br />
          <span class="bg-gradient-to-r from-amber-400 to-amber-600 bg-clip-text text-transparent relative inline-block text-glow">
            MIT COLLEGE.
            <svg class="absolute -bottom-2 left-0 w-full" viewBox="0 0 358 8" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M1 5.5C118.5 2 237.5 1.5 357 7" stroke="#efc144ff" strokeWidth="2" strokeLinecap="round"/>
            </svg>
          </span>
        </h1>
        <p class="text-gray-500 text-sm mb-4">
            Computer Science & Information Technology
        </p>
        
        <!-- Enter Button -->
        <button onclick="closeWelcomePopup()" 
                class="w-full py-3.5 bg-gradient-to-br from-amber-400 to-amber-600 hover:from-amber-500 hover:to-amber-700 text-white font-semibold rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/30 hover:-translate-y-0.5 active:translate-y-0">
            Enter Website
        </button>
        
        <!-- Don't Show Again Option -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <label class="flex items-center justify-center cursor-pointer text-gray-600 hover:text-gray-800 transition-colors">
                <input type="checkbox" id="dontShowAgain" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-2">
                <span class="text-sm">Don't show this again</span>
            </label>
        </div>
    </div>
</div>

