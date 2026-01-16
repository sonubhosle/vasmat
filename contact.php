<?php 
include 'includes/header.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
try {
    include __DIR__ . "/admin/includes/db.php";
    
    // Test database connection
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
} catch (Exception $e) {
    // Log error but don't break the page
    error_log("Database error: " . $e->getMessage());
    $conn = null; // Set to null to prevent errors
}

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/admin/includes/phpmailer/src/Exception.php';
require __DIR__ . '/admin/includes/phpmailer/src/PHPMailer.php';
require __DIR__ . '/admin/includes/phpmailer/src/SMTP.php';

// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    
    $response = ['success' => false, 'message' => ''];
    
    try {
        if(isset($_POST['full_name']) && isset($_POST['email']) && isset($_POST['subject']) && isset($_POST['message'])) {
            $name = $_POST['full_name'];
            $email = $_POST['email'];
            $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
            $subject = $_POST['subject'];
            $message_content = $_POST['message'];
            
            $db_success = false;
            $email_sent = false;
            
            // Save to DB if connection exists
            if ($conn) {
                $name_clean = mysqli_real_escape_string($conn, $name);
                $email_clean = mysqli_real_escape_string($conn, $email);
                $phone_clean = mysqli_real_escape_string($conn, $phone);
                $subject_clean = mysqli_real_escape_string($conn, $subject);
                $message_clean = mysqli_real_escape_string($conn, $message_content);
                
                $sql = "INSERT INTO contact_messages (full_name, email, phone, subject, message)
                        VALUES ('$name_clean','$email_clean','$phone_clean','$subject_clean','$message_clean')";
                
                if($conn->query($sql)) {
                    $db_success = true;
                } else {
                    error_log("DB Error: " . $conn->error);
                }
            }
            
            // Try to send email (even if DB fails)
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'sbhosle1011@gmail.com';
                $mail->Password   = 'gvfcsniugbcriqpg';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
        
                // Disable SSL verification for local testing (remove in production)
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
                
                $mail->setFrom('sbhosle1011@gmail.com', 'MIT College Website');
                $mail->addAddress('sbhosle1011@gmail.com');
                if (!empty($email)) {
                    $mail->addReplyTo($email, $name);
                }
                
                $mail->isHTML(true);
                $mail->Subject = "$subject";
               $mail->Body = "
    <div class=\"bg-emerald-50 p-5 rounded-lg\">
        <h2 class=\"text-xl font-bold text-slate-700\">Message Received from $name</h2>

        <div class=\"mb-4\">
            <p class=\"flex items-center gap-2 text-slate-600\">
                <i class='bx bx-mail-send'></i> $email
            </p>
            <p class=\"flex items-center gap-2 text-slate-600\">
                <i class='bx bxs-phone'></i> $phone
            </p>
        </div>

        <p class=\"flex items-center gap-2 text-slate-600\">
            <i class='bx bx-list-plus'></i> $subject
        </p>

        <p><strong>Message:</strong><br>" . nl2br($message_content) . "</p>
    </div>
";

                
                $mail->AltBody = "Name: $name\nEmail: $email\nPhone: $phone\nSubject: $subject\nMessage: $message_content";
                
                if($mail->send()) {
                    $email_sent = true;
                }
            } catch (Exception $e) {
                error_log("Email Error: " . $mail->ErrorInfo);
            }
            
            // Determine response message
            if ($db_success || $email_sent) {
                $response['success'] = true;
                
                if ($db_success && $email_sent) {
                    $response['message'] = "✅ Message sent successfully! We'll get back to you soon.";
                } elseif ($db_success && !$email_sent) {
                    $response['message'] = "✅ Message saved successfully! We'll contact you soon. (Email notification failed)";
                } elseif (!$db_success && $email_sent) {
                    $response['message'] = "✅ Email sent successfully! We'll get back to you soon.";
                }
            } else {
                $response['message'] = "❌ Unable to process your request. Please try again or contact us directly.";
            }
            
        } else {
            $response['message'] = "❌ Please fill in all required fields.";
        }
    } catch (Exception $e) {
        $response['message'] = "❌ Server error occurred. Please try again.";
        error_log("Contact Form Exception: " . $e->getMessage());
    }
    
    echo json_encode($response);
    exit;
}
?>

<!-- Success/Error Popup Modal -->
<div id="messagePopup" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm transition-all duration-300 hidden">
    <div class="bg-white rounded-xl p-8 max-w-md w-[90%] mx-4 transform transition-all duration-500 scale-95 opacity-0">
        <div id="popupIcon" class="text-6xl text-center mb-4"></div>
        <h3 id="popupTitle" class="text-2xl font-black text-slate-900 text-center mb-2"></h3>
        <p id="popupMessage" class="text-slate-600 text-center mb-6"></p>
        <button onclick="closePopup()" 
                class="w-full py-3 bg-gradient-to-br from-amber-400 to-amber-600 text-white font-black rounded-xl text-lg tracking-tight uppercase hover:scale-[1.02] transition-transform">
            Close
        </button>
    </div>
</div>

<section id="contact" class="py-24 px-6 bg-slate-50 relative overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-16">
            <span class="text-amber-600 text-[10px] font-black tracking-[0.5em] uppercase mb-4 block">Get In Touch</span>
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
                <div class="p-5 bg-white rounded-[40px] border border-slate-200 group hover:border-amber-500/30 transition-all duration-500">
                    <div class="flex gap-6">
                        <div>
                            <h4 class="text-[13px] flex items-center gap-2 font-black text-slate-800 uppercase tracking-widest mb-2">
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
                        <h4 class="text-[13px] flex items-center gap-2 font-black text-slate-800 uppercase tracking-widest mb-2">
                            <i class="fas fa-phone text-base"></i>
                            Direct Call
                        </h4>
                        <p class="text-slate-700 font-black text-[12px]">+91 93091 47752</p>
                    </div>
                    <div class="p-6 bg-white rounded-[32px] border border-slate-200">
                        <h4 class="text-[13px] flex items-center gap-2 font-black text-slate-800 uppercase tracking-widest mb-2">
                            <i class="fas fa-envelope text-base"></i>
                            Official Email
                        </h4>
                        <p class="text-slate-700 font-black text-[12px] lowercase truncate">mitcollege.basmath@gmail.com</p>
                    </div>
                </div>

                <!-- Office Hours Card -->
                <div class="p-8 bg-slate-50 text-white rounded-[40px] border border-slate-200 relative overflow-hidden group">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-amber-500/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="flex gap-6 relative z-10">
                        <div class="w-14 h-14 bg-white border-slate-200 rounded-2xl flex items-center justify-center text-slate-700 shrink-0 border border-white/5">
                            <i class="fas fa-clock text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-[10px] font-black text-amber-500 uppercase tracking-widest mb-2">Office Hours</h4>
                            <p class="text-slate-600 font-bold mb-1">Monday — Saturday</p>
                            <p class="text-slate-500 text-sm font-medium">9:30 AM to 1:30 PM</p>
                            <p class="text-rose-500/80 text-[9px] font-black uppercase mt-3 tracking-widest">Sunday: Closed</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Contact Form -->
            <div class="lg:col-span-7">
                <div class="bg-white rounded-xl p-8 border border-slate-200 relative">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-amber-500/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                    
                    <!-- Contact Form -->
                    <div class="mb-12 relative z-10">
                        <h3 class="text-2xl font-black text-slate-800 uppercase tracking-tight mb-1">Send an Inquiry</h3>
                        <p class="text-slate-600 text-sm font-medium">Fill out the form below and we'll route your request to the appropriate department.</p>
                    </div>

                    <form id="contactForm" class="space-y-4 relative z-10">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <!-- Name Field -->
                            <div class="group space-y-1.5">
                                <label class="flex items-center gap-2 text-[10px] font-black text-slate-600 uppercase tracking-widest ml-2">
                                    <i class="fas fa-user text-slate-600 text-sm"></i> Full Name
                                </label>
                                <input type="text" name="full_name" id="full_name" required
                                    class="w-full px-8 py-3 m-0 bg-slate-50 border-2 border-slate-200 rounded-xl text-[12px] font-semibold text-slate-600 outline-none focus:border-amber-500/60 placeholder:text-slate-500 focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition ease-in-out duration-300"
                                    placeholder="Ex: Rajesh Kumar" />
                            </div>

                            <!-- Email Field -->
                            <div class="group space-y-1.5">
                                <label class="flex items-center gap-2 text-[10px] font-black text-slate-600 uppercase tracking-widest ml-2">
                                    <i class="fas fa-at text-slate-600 text-sm"></i> Email Address
                                </label>
                                <input type="email" name="email" id="email" required
                                    class="w-full px-8 py-3 m-0 bg-slate-50 border-2 border-slate-200 rounded-xl text-[12px] font-semibold text-slate-600 outline-none focus:border-amber-500/60 placeholder:text-slate-500 focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition ease-in-out duration-300"
                                    placeholder="Ex: rajesh@example.com" />
                            </div>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <!-- Phone Field -->
                            <div class="group space-y-1.5">
                                <label class="flex items-center gap-2 text-[10px] font-black text-slate-600 uppercase tracking-widest ml-2">
                                    <i class="fas fa-phone text-slate-600 text-sm"></i> Phone Number
                                </label>
                                <input type="text" name="phone" id="phone"
                                    class="w-full px-8 py-3 m-0 bg-slate-50 border-2 border-slate-200 rounded-xl text-[12px] font-semibold text-slate-600 outline-none focus:border-amber-500/60 placeholder:text-slate-500 focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition ease-in-out duration-300"
                                    placeholder="Ex: +91 98765 43210" />
                            </div>

                            <!-- CUSTOM DROPDOWN FIELD -->
                            <div class="group space-y-1.5">
                                <label class="flex items-center gap-2 text-[10px] font-black text-slate-600 uppercase tracking-widest ml-2">
                                    <i class="fas fa-question-circle text-slate-600 text-sm"></i> Inquiry Subject
                                </label>
                                
                                <input type="hidden" name="subject" id="selectedSubject" value="" required>
                                
                                <div class="custom-dropdown relative">
                                    <div class="dropdown-trigger w-full px-8 py-3 m-0 bg-slate-50 border-2 border-slate-200 rounded-xl text-[12px] font-semibold text-slate-600 outline-none focus:border-amber-500/60 placeholder:text-slate-500 transition ease-in-out duration-300 cursor-pointer flex justify-between items-center">
                                        <span id="selectedValue" class="text-slate-400">Select a subject</span>
                                        <i class="fas fa-chevron-down text-slate-400 transition-transform duration-300"></i>
                                    </div>
                                    
                                    <div class="dropdown-options absolute top-full left-0 right-0 mt-2 bg-white border-2 border-slate-200 rounded-xl overflow-hidden shadow-xl z-50 max-h-0 opacity-0 transition-all duration-300 ease-in-out">
                                        <div class="max-h-64 overflow-y-auto py-2">
                                            <div class="dropdown-option px-8 py-3 cursor-pointer hover:bg-amber-50 text-[12px] font-semibold text-slate-600 transition-colors" data-value="General Inquiry">
                                                <i class="fas fa-info-circle mr-3 text-amber-500"></i> General Inquiry
                                            </div>
                                            <div class="dropdown-option px-8 py-3 cursor-pointer hover:bg-amber-50 text-[12px] font-semibold text-slate-600 transition-colors" data-value="Admission Support">
                                                <i class="fas fa-graduation-cap mr-3 text-amber-500"></i> Admission Support
                                            </div>
                                            <div class="dropdown-option px-8 py-3 cursor-pointer hover:bg-amber-50 text-[12px] font-semibold text-slate-600 transition-colors" data-value="Document Verification">
                                                <i class="fas fa-file-check mr-3 text-amber-500"></i> Document Verification
                                            </div>
                                            <div class="dropdown-option px-8 py-3 cursor-pointer hover:bg-amber-50 text-[12px] font-semibold text-slate-600 transition-colors" data-value="Anti-Ragging Support">
                                                <i class="fas fa-shield-alt mr-3 text-amber-500"></i> Anti-Ragging Support
                                            </div>
                                            <div class="dropdown-option px-8 py-3 cursor-pointer hover:bg-amber-50 text-[12px] font-semibold text-slate-600 transition-colors" data-value="Feedback/Grievance">
                                                <i class="fas fa-comment-dots mr-3 text-amber-500"></i> Feedback/Grievance
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Message Field -->
                        <div class="group space-y-1.5">
                            <label class="flex items-center gap-2 text-[10px] font-black text-slate-600 uppercase tracking-widest ml-2">
                                <i class="fas fa-comment-alt text-slate-600 text-sm"></i> Your Message
                            </label>
                            <textarea name="message" id="message" rows="4" required
                                class="w-full px-8 py-3 m-0 bg-slate-50 border-2 border-slate-200 rounded-xl text-[12px] font-semibold text-slate-600 outline-none focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 focus:border-amber-500/60 placeholder:text-slate-500 transition ease-in-out duration-300"
                                placeholder="Please describe your query in detail..."></textarea>
                        </div>

                        <button type="submit" id="submitBtn"
                            class="w-full py-3 bg-gradient-to-br from-amber-400 to-amber-600 text-white font-black rounded-xl transition-all flex items-center justify-center gap-4 text-xl tracking-tight uppercase hover:scale-[1.02] transform duration-300 relative overflow-hidden group">
                            <span class="relative z-10">Send Message</span>
                            <i class="fas fa-paper-plane text-xl relative z-10"></i>
                            <div class="absolute inset-0 bg-gradient-to-br from-amber-500 to-amber-700 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* CUSTOM DROPDOWN STYLES */
.custom-dropdown {
    position: relative;
}

.dropdown-trigger {
    position: relative;
    user-select: none;
}

.dropdown-trigger.active {
    border-color: rgba(245, 158, 11, 0.6);
    background-color: #fef3c7;
    box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);

}

.dropdown-trigger.active i {
    transform: rotate(180deg);
    color: #f59e0b;
}

.dropdown-options {
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    transform-origin: top center;
    transform: scaleY(0.95);
    visibility: hidden;
}

.dropdown-options.open {
    max-height: 320px;
    opacity: 1;
    visibility: visible;
    transform: scaleY(1);
}

.dropdown-option {
    position: relative;
    transition: all 0.2s ease;
    border-bottom: 1px solid #f1f5f9;
}

.dropdown-option:last-child {
    border-bottom: none;
}

.dropdown-option:hover {
    background-color: #fffbeb;
    padding-left: 3.5rem;
}

.dropdown-option.selected {
    background-color: #fef3c7;
    color: #92400e;
    font-weight: bold;
}

.dropdown-option.selected:before {
    content: '✓';
    position: absolute;
    left: 2rem;
    color: #f59e0b;
    font-weight: bold;
}

/* Scrollbar styling */
.dropdown-options::-webkit-scrollbar {
    width: 6px;
}

.dropdown-options::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.dropdown-options::-webkit-scrollbar-thumb {
    background: #fbbf24;
    border-radius: 3px;
}

/* Popup Animation */
#messagePopup > div {
    animation: popupIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

#messagePopup.hidden > div {
    animation: popupOut 0.4s ease forwards;
}

@keyframes popupIn {
    0% { opacity: 0; transform: scale(0.8) translateY(20px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
}

@keyframes popupOut {
    0% { opacity: 1; transform: scale(1) translateY(0); }
    100% { opacity: 0; transform: scale(0.8) translateY(20px); }
}

@keyframes sendingPulse {
    0%, 100% { opacity: 0.7; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.05); }
}

.sending-pulse {
    animation: sendingPulse 1.5s ease-in-out infinite;
}

@keyframes iconBounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
}

.icon-bounce {
    animation: iconBounce 0.6s;
}

button[type="submit"]:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

/* Success/Error colors */
.text-success { color: #10b981; }
.text-error { color: #ef4444; }
</style>

<script>
// DROPDOWN FUNCTIONALITY
document.addEventListener('DOMContentLoaded', function() {
    const dropdownTrigger = document.querySelector('.dropdown-trigger');
    const dropdownOptions = document.querySelector('.dropdown-options');
    const dropdownIcon = dropdownTrigger.querySelector('i');
    const selectedValueSpan = document.getElementById('selectedValue');
    const hiddenInput = document.getElementById('selectedSubject');
    const dropdownOptionElements = document.querySelectorAll('.dropdown-option');
    
    let isDropdownOpen = false;
    
    // Toggle dropdown when trigger is clicked
    dropdownTrigger.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleDropdown();
    });
    
    function toggleDropdown() {
        if (isDropdownOpen) {
            dropdownOptions.classList.remove('open');
            dropdownTrigger.classList.remove('active');
            dropdownIcon.style.transform = 'rotate(0deg)';
        } else {
            dropdownOptions.classList.add('open');
            dropdownTrigger.classList.add('active');
            dropdownIcon.style.transform = 'rotate(180deg)';
        }
        isDropdownOpen = !isDropdownOpen;
    }
    
    // Handle option selection
    dropdownOptionElements.forEach(option => {
        option.addEventListener('click', function(e) {
            e.stopPropagation();
            const value = this.getAttribute('data-value');
            
            selectedValueSpan.textContent = value;
            selectedValueSpan.style.color = '#1e293b';
            hiddenInput.value = value;
            dropdownTrigger.style.borderColor = '#f59e0b';
            dropdownTrigger.style.backgroundColor = '#fffbeb';
            
            dropdownOptionElements.forEach(opt => {
                opt.classList.remove('selected');
            });
            this.classList.add('selected');
            
            toggleDropdown();
        });
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (isDropdownOpen && !dropdownTrigger.contains(e.target) && !dropdownOptions.contains(e.target)) {
            toggleDropdown();
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isDropdownOpen) {
            toggleDropdown();
        }
    });
});

// POPUP FUNCTIONS
function showSendingPopup() {
    const popup = document.getElementById('messagePopup');
    const popupIcon = document.getElementById('popupIcon');
    const popupTitle = document.getElementById('popupTitle');
    const popupMessage = document.getElementById('popupMessage');
    
    popupIcon.innerHTML = '<i class="fas fa-paper-plane sending-pulse"></i>';
    popupIcon.className = 'text-6xl text-center mb-4 text-amber-500';
    popupTitle.textContent = 'Sending Message...';
    popupMessage.textContent = 'Please wait while we send your message.';
    
    popup.classList.remove('hidden');
    setTimeout(() => {
        popup.querySelector('div').classList.remove('scale-95', 'opacity-0');
    }, 10);
}

function showResultPopup(title, message, isSuccess) {
    const popup = document.getElementById('messagePopup');
    const popupIcon = document.getElementById('popupIcon');
    const popupTitle = document.getElementById('popupTitle');
    const popupMessage = document.getElementById('popupMessage');
    
    if (isSuccess) {
        popupIcon.innerHTML = '<i class="fas fa-check-circle"></i>';
        popupIcon.className = 'text-6xl text-center mb-4 text-green-500 icon-bounce';
        popupTitle.className = 'text-2xl font-black text-green-600 text-center mb-2';
    } else {
        popupIcon.innerHTML = '<i class="fas fa-exclamation-circle"></i>';
        popupIcon.className = 'text-6xl text-center mb-4 text-red-500 icon-bounce';
        popupTitle.className = 'text-2xl font-black text-red-600 text-center mb-2';
    }
    
    popupTitle.textContent = title;
    popupMessage.textContent = message;
    
    popup.classList.remove('hidden');
    setTimeout(() => {
        popup.querySelector('div').classList.remove('scale-95', 'opacity-0');
    }, 10);
}

function closePopup() {
    const popup = document.getElementById('messagePopup');
    popup.querySelector('div').classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        popup.classList.add('hidden');
        
        // Reset form if success was shown
        const successIcon = document.getElementById('popupIcon');
        if (successIcon.innerHTML.includes('fa-check-circle')) {
            document.getElementById('contactForm').reset();
            document.getElementById('selectedValue').textContent = 'Select a subject';
            document.getElementById('selectedValue').style.color = '#94a3b8';
            document.getElementById('selectedSubject').value = '';
            document.querySelector('.dropdown-trigger').style.borderColor = '#e2e8f0';
            document.querySelector('.dropdown-trigger').style.backgroundColor = '#f8fafc';
            document.querySelectorAll('.dropdown-option').forEach(opt => {
                opt.classList.remove('selected');
            });
        }
    }, 400);
}

// Close popup when clicking outside
document.getElementById('messagePopup').addEventListener('click', function(e) {
    if (e.target === this) {
        closePopup();
    }
});

// Close popup with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('messagePopup').classList.contains('hidden')) {
        closePopup();
    }
});

// AJAX FORM SUBMISSION
document.getElementById('contactForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Validate dropdown selection
    const selectedValue = document.getElementById('selectedSubject').value;
    if (!selectedValue) {
        showResultPopup('Error', 'Please select an inquiry subject', false);
        document.querySelector('.dropdown-trigger').style.borderColor = '#ef4444';
        return;
    }
    
    // Show sending popup
    showSendingPopup();
    
    // Disable submit button
    const submitBtn = document.getElementById('submitBtn');
    const originalBtnHTML = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="relative z-10">Sending...</span><i class="fas fa-spinner fa-spin text-xl relative z-10"></i>';
    
    try {
        // Get form data
        const formData = new FormData(this);
        formData.append('ajax', 'true');
        
        // Send AJAX request
        const response = await fetch(window.location.href, {
            method: 'POST',
            body: formData
        });
        
        // Check if response is OK
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        
        // Try to parse JSON response
        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (parseError) {
            console.error('JSON Parse Error:', parseError, 'Response:', text);
            
            // If JSON parsing fails but we got some response, assume partial success
            if (text.includes('success') || text.includes('Message')) {
                // Message might have been sent despite JSON error
                setTimeout(() => {
                    closePopup();
                    setTimeout(() => {
                        showResultPopup('Success!', '✅ Your message has been received! We\'ll contact you soon.', true);
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHTML;
                    }, 500);
                }, 1000);
                return;
            }
            throw new Error('Invalid server response');
        }
        
        // Close sending popup and show result
        setTimeout(() => {
            closePopup();
            setTimeout(() => {
                if (data.success) {
                    showResultPopup('Success!', data.message, true);
                } else {
                    showResultPopup('Error', data.message, false);
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHTML;
            }, 500);
        }, 1000);
        
    } catch (error) {
        console.error('Submission Error:', error);
        
        // Close sending popup
        setTimeout(() => {
            closePopup();
            setTimeout(() => {
                // Check error type
                if (error.message.includes('NetworkError') || error.message.includes('Failed to fetch')) {
                    showResultPopup('Error', '❌ Network error. Please check your connection.', false);
                } else if (error.message.includes('JSON') || error.message.includes('parse')) {
                    showResultPopup('Partial Success', '✅ Your message was submitted but there was a server response issue. We\'ll contact you if received.', false);
                } else {
                    showResultPopup('Error', '❌ Something went wrong. Please try again.', false);
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHTML;
            }, 500);
        }, 1000);
    }
});
</script>

<?php include 'includes/footer.php'; ?>