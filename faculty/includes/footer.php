        </main>

        <!-- Portal Footer -->
        <footer class="mt-auto px-10 py-8 border-t border-slate-100 bg-white/50 backdrop-blur-sm">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 bg-slate-900 rounded-lg flex items-center justify-center text-white text-[10px] font-black italic">MIT</div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">© <?= date('Y') ?> MIT Vasmat College • Internal Faculty Management</p>
                </div>
                <div class="flex items-center gap-8">
                    <a href="#" class="text-[10px] font-black text-slate-400 hover:text-primary-600 uppercase tracking-widest transition-colors">Documentation</a>
                    <a href="#" class="text-[10px] font-black text-slate-400 hover:text-primary-600 uppercase tracking-widest transition-colors">Security Policy</a>
                    <div class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-emerald-100 flex items-center gap-2">
                        <span class="w-1 h-1 bg-emerald-500 rounded-full animate-pulse"></span>
                        System Stable
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Global Scripts -->
    <script src="/vasmat/assets/js/notifications.js"></script>
    <script>
        // Sidebar Toggle Logic
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const toggleBtn = document.getElementById('sidebar-toggle-btn');

        if(toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            });
        }

        if(overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });
        }
    </script>
</body>
</html>
