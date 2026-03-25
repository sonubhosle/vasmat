<?php
include 'admin/includes/db.php';
include 'admin/includes/functions.php';

$photos = $conn->query("SELECT * FROM gallery ORDER BY id DESC");

include 'includes/header.php';
?>

<div class="px-6 py-4 sm:py-6 lg:py-8 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-12 animate-fade-in-up">
        <div class="flex items-center gap-4 mb-4">
            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-amber-500">Visual Journey</span>
        </div>
        <h2 class="text-2xl md:text-3xl font-black text-slate-900 mb-6">
            Campus <span class="italic font-serif underline decoration-amber-400/30">Gallery</span>
        </h2>
        <p class="text-slate-500 text-lg max-w-2xl">Relive the vibrant student life, academic achievements, and cultural celebrations at MIT College.</p>
    </div>

    <?php if ($photos->num_rows == 0): ?>
        <div class="text-center py-20 glass-card rounded-[3rem] border-2 border-dashed border-slate-200 animate-fade-in-up">
            <div class="w-24 h-24 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8 text-slate-300">
                <i class="fas fa-camera text-4xl"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-800 mb-3">No Photos Yet</h3>
            <p class="text-slate-500 max-w-md mx-auto line-relaxed">We're currently developing our visual archives. Please check back soon for updates.</p>
        </div>
    <?php else: ?>
        <div class="columns-1 md:columns-2 lg:columns-3 gap-6 space-y-6 animate-fade-in-up" id="galleryGrid">
            <?php while($photo = $photos->fetch_assoc()): ?>
                <div class="break-inside-avoid glass-card rounded-3xl overflow-hidden group relative cursor-pointer shadow-lg shadow-slate-200/50 hover:shadow-2xl hover:shadow-amber-500/10 transition-all duration-500 hover:-translate-y-2 border-0" 
                     onclick="openLightbox('upload/gallery/<?= htmlspecialchars($photo['image']) ?>', '<?= htmlspecialchars($photo['caption'] ?: 'Campus View') ?>')">
                    
                    <img src="upload/gallery/<?= htmlspecialchars($photo['image']) ?>" 
                         class="w-full h-auto object-cover transition-transform duration-700 group-hover:scale-110" 
                         alt="Campus Photo">
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-8">
                        <div class="translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                            <h4 class="text-white font-black text-lg mb-1"><?= htmlspecialchars($photo['caption'] ?: 'Campus View') ?></h4>
                            <div class="flex items-center gap-2 text-amber-400 text-[10px] font-black uppercase tracking-widest">
                                <i class="fas fa-calendar-alt"></i>
                                <?= date('F d, Y', strtotime($photo['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Simple Lightbox -->
<div id="lightbox" class="hidden fixed inset-0 z-[1000] p-6 flex items-center justify-center transition-all duration-500 opacity-0 bg-slate-950/95 backdrop-blur-xl">
    <button onclick="closeLightbox()" class="absolute top-8 right-8 w-14 h-14 bg-white/10 text-white rounded-2xl flex items-center justify-center hover:bg-white/20 transition-all active:scale-95 group">
        <i class="fas fa-times text-2xl group-hover:rotate-90 transition-transform"></i>
    </button>
    
    <div class="max-w-6xl w-full h-full flex flex-col items-center justify-center gap-8">
        <div class="relative w-full h-full flex items-center justify-center">
            <img id="lightboxImg" src="" class="max-w-full max-h-[80vh] rounded-[2rem] shadow-2xl animate-in zoom-in-95 duration-500 object-contain border-4 border-white/5 bg-slate-900/50">
        </div>
        <div class="text-center">
            <h4 id="lightboxCaption" class="text-3xl font-black text-white mb-2 tracking-tight"></h4>
            <span class="px-4 py-1.5 bg-amber-500 text-slate-900 text-[11px] font-black uppercase tracking-widest rounded-full">MIT Experience</span>
        </div>
    </div>
</div>

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.7s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(40px); }
        to { opacity: 1; transform: translateY(0); }
    }
    #galleryGrid > div {
        transition-delay: calc(var(--i, 0) * 0.1s);
    }
</style>

<script>
    function openLightbox(src, caption) {
        const lightbox = document.getElementById('lightbox');
        const img = document.getElementById('lightboxImg');
        const cap = document.getElementById('lightboxCaption');
        
        img.src = src;
        cap.textContent = caption;
        
        lightbox.classList.remove('hidden');
        setTimeout(() => {
            lightbox.classList.add('opacity-100');
        }, 10);
    }

    function closeLightbox() {
        const lightbox = document.getElementById('lightbox');
        lightbox.classList.remove('opacity-100');
        setTimeout(() => {
            lightbox.classList.add('hidden');
        }, 500);
    }

    // Close on escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeLightbox();
    });

    // Staggered animation indices
    document.querySelectorAll('#galleryGrid > div').forEach((card, index) => {
        card.style.setProperty('--i', index);
    });
</script>

<?php include 'includes/footer.php'; ?>
