<?php include 'includes/header.php'; ?>

<section id="contact" class="py-24 px-6 bg-slate-50 relative overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-16">
            <span class="text-amber-600 text-[10px] font-black tracking-[0.5em] uppercase mb-4 block">Get In
                Touch</span>
            <h2 class="text-4xl md:text-6xl font-black text-slate-900 uppercase tracking-tighter leading-none mb-6">
                CONTACT <span class="text-amber-600">US</span>
            </h2>
            <p class="text-slate-500 font-medium max-w-2xl mx-auto">
                Have questions about admissions, our programs, or campus safety? Our dedicated team is here to assist
                you with detailed information and support.
            </p>
        </div>

        <div class="grid lg:grid-cols-12 gap-12 items-start">

            <!-- Left Side: Contact Information Cards -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Primary Address Card -->
                <div class="p-5 bg-white rounded-[40px] border border-slate-200  group hover:border-amber-500/30 transition-all duration-500">
                    <div class="flex gap-6">

                        <div>
                            <h4
                                class="text-[13px] flex items-center gap-2 font-black text-slate-800 uppercase tracking-widest mb-2">
                                <i class="fas fa-map-marker-alt text-base"></i>
                                Campus Address
                            </h4>
                            <p class="text-slate-700 text-[12px] font-bold leading-relaxed">
                                Society Market, Basmath <br>
                                Tq. Basmath Dist. Hingoli, Maharashtra <br>
                                PIN : 431512
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Contact Details Grid -->
                <div class="grid sm:grid-cols-2 gap-6">
                    <div class="p-6 bg-white rounded-[32px] border border-slate-200">

                        <h4
                            class="text-[13px] flex items-center gap-2 font-black text-slate-800 uppercase tracking-widest mb-2">
                            <i class="fas fa-phone text-base"></i>
                            Direct Call
                        </h4>
                        <p class="text-slate-700 font-black text-[12px]">+91 93091 47752</p>

                    </div>
                    <div class="p-6 bg-white rounded-[32px] border border-slate-200">
                        <h4
                            class="text-[13px] flex items-center gap-2 font-black text-slate-800 uppercase tracking-widest mb-2">
                            <i class="fas fa-envelope text-base"></i>
                            Official Email
                        </h4>
                      
                        <p class="text-slate-700 font-black text-[12px] lowercase truncate">mitcollege.basmath@gmail.com</p>
                    </div>
                </div>

                <!-- Office Hours Card -->
                <div class="p-8 bg-slate-50 text-white rounded-[40px] border border-slate-200 relative overflow-hidden group">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-amber-500/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700">
                    </div>
                    <div class="flex gap-6 relative z-10">
                        <div
                            class="w-14 h-14 bg-white border-slate-200 rounded-2xl flex items-center justify-center text-slate-700 shrink-0 border border-white/5">
                            <i class="fas fa-clock text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-[10px] font-black text-amber-500 uppercase tracking-widest mb-2">Office
                                Hours</h4>
                            <p class="text-slate-600 font-bold mb-1">Monday — Saturday</p>
                            <p class="text-slate-500 text-sm font-medium">9:30 AM to 1:30 PM</p>
                            <p class="text-rose-500/80 text-[9px] font-black uppercase mt-3 tracking-widest">Sunday:
                                Closed</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Contact Form -->
            <div class="lg:col-span-7">
                <div class="bg-white rounded-3xl p-8 border border-slate-200  relative ">
                   <div class="absolute -top-10 -right-10 w-32 h-32 bg-amber-500/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700">
                    </div>
                    <!-- Contact Form -->
                    <div class="mb-12 relative z-10">
                        <h3 class="text-2xl font-black text-slate-800 uppercase tracking-tight mb-1">Send an Inquiry
                        </h3>
                        <p class="text-slate-600 text-sm font-medium">Fill out the form below and we'll route your
                            request to the appropriate department.
                        </p>
                    </div>

                    <form method="POST" action="send_email.php" class="space-y-4 relative z-10" id="contactForm">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <!-- Name Field -->
                            <div class="group space-y-1.5">
                                <label
                                    class="flex items-center gap-2 text-[10px] font-black text-slate-600 uppercase tracking-widest ml-2">
                                    <i class="fas fa-user text-slate-600 text-sm"></i> Full Name
                                </label>
                                <input type="text" name="name"
                                    class="w-full px-8 py-3 m-0 bg-slate-50 border-2 border-slate-200 rounded-3xl text-[12px] font-semibold text-slate-600 outline-none focus:border-amber-500/60 placeholder:text-slate-500 transition ease-in-out duration-300"
                                    placeholder="Ex: Rajesh Kumar" />
                            </div>

                            <!-- Email Field -->
                            <div class="group space-y-1.5">
                                <label
                                    class="flex items-center gap-2 text-[10px] font-black text-slate-600 uppercase tracking-widest ml-2">
                                    <i class="fas fa-at text-slate-600 text-sm"></i> Email Address
                                </label>
                                <input type="email" name="email"
                                    class="w-full px-8 py-3 m-0 bg-slate-50 border-2 border-slate-200 rounded-3xl text-[12px] font-semibold text-slate-600 outline-none focus:border-amber-500/60 placeholder:text-slate-500 transition ease-in-out duration-300"
                                    placeholder="Ex: rajesh@example.com" />
                            </div>
                        </div>

                        <!-- CUSTOM DROPDOWN FIELD (CSS Only) -->
                        <div class="group space-y-1.5">
                            <label
                                class="flex items-center gap-2 text-[10px] font-black text-slate-600 uppercase tracking-widest ml-2">
                                <i class="fas fa-question-circle text-slate-600 text-sm"></i> Inquiry Subject
                            </label>

                            <div class="relative custom-dropdown">
                                <div
                                    class="dropdown-toggle w-full px-8 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-3xl text-[12px] font-semibold text-slate-600 flex items-center justify-between cursor-pointer transition-all hover:border-amber-500/60">
                                    <span class="selected-value">General Inquiry</span>
                                    <i class="fas fa-chevron-down text-slate-400 transition-transform"></i>
                                </div>

                                <!-- Hidden Input for Form Submission -->
                                <input type="hidden" name="subject" value="General Inquiry" class="subject-input">

                                <!-- Dropdown Options -->
                                <div
                                    class="dropdown-options absolute top-full left-0 w-full mt-2 bg-white border border-slate-100 shadow-2xl rounded-[32px] overflow-hidden z-50 opacity-0 scale-95 pointer-events-none transition-all duration-300 origin-top">
                                    <div class="p-3 space-y-1 max-h-60 overflow-y-auto">
                                        <div class="dropdown-option px-6 py-4 rounded-2xl text-xs font-bold text-slate-500 cursor-pointer transition-all hover:bg-slate-50 hover:text-amber-600"
                                            data-value="General Inquiry">
                                            General Inquiry
                                        </div>
                                        <div class="dropdown-option px-6 py-4 rounded-2xl text-xs font-bold text-slate-500 cursor-pointer transition-all hover:bg-slate-50 hover:text-amber-600"
                                            data-value="Admission Support">
                                            Admission Support
                                        </div>
                                        <div class="dropdown-option px-6 py-4 rounded-2xl text-xs font-bold text-slate-500 cursor-pointer transition-all hover:bg-slate-50 hover:text-amber-600"
                                            data-value="Document Verification">
                                            Document Verification
                                        </div>
                                        <div class="dropdown-option px-6 py-4 rounded-2xl text-xs font-bold text-slate-500 cursor-pointer transition-all hover:bg-slate-50 hover:text-amber-600"
                                            data-value="Anti-Ragging Support">
                                            Anti-Ragging Support
                                        </div>
                                        <div class="dropdown-option px-6 py-4 rounded-2xl text-xs font-bold text-slate-500 cursor-pointer transition-all hover:bg-slate-50 hover:text-amber-600"
                                            data-value="Feedback/Grievance">
                                            Feedback/Grievance
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Message Field -->
                        <div class="group space-y-1.5">
                            <label
                                class="flex items-center gap-2 text-[10px] font-black text-slate-600 uppercase tracking-widest ml-2">
                                <i class="fas fa-comment-alt text-slate-600 text-sm"></i> Your Message
                            </label>
                            <textarea name="message" rows="2"
                                class="w-full px-8 py-3 m-0 bg-slate-50 border-2 border-slate-200 rounded-3xl text-[12px] font-semibold text-slate-600 outline-none focus:border-amber-500/60 placeholder:text-slate-500 transition ease-in-out duration-300"
                                placeholder="Please describe your query in detail..."></textarea>
                        </div>

                        <button type="submit"
                            class="w-full py-3 bg-gradient-to-br from-amber-400 to-amber-600 text-white font-black rounded-xl transition-all  flex items-center justify-center gap-4 text-xl tracking-tight uppercase">
                            Send Message
                            <i class="fas fa-paper-plane text-xl"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CSS for Custom Dropdown -->
<style>
/* Custom Dropdown Styles */
.custom-dropdown {
    position: relative;
}

.custom-dropdown.open .dropdown-toggle {
    border-color: #f59e0b;
    background-color: white;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.custom-dropdown.open .dropdown-toggle i {
    transform: rotate(180deg);
    color: #f59e0b;
}

.custom-dropdown.open .dropdown-options {
    opacity: 1;
    transform: scale(1);
    pointer-events: auto;
}

.dropdown-options {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}

.dropdown-options::-webkit-scrollbar {
    width: 6px;
}

.dropdown-options::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.dropdown-options::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.dropdown-option.active {
    background-color: #f59e0b;
    color: white;
}

/* Smooth transitions */
.dropdown-toggle,
.dropdown-options,
.dropdown-option {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>


<?php include 'includes/footer.php'; ?>