        </main>
        
        <footer class="mt-auto px-8 py-6 border-t border-slate-100 bg-white/50 backdrop-blur-sm">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                    &copy; <?= date('Y') ?> MIT COLLEGE • SYSTEM ARCHITECTURE v2.0
                </p>
                <div class="flex items-center gap-6">
                    <span class="flex items-center gap-2 text-[10px] font-bold text-emerald-500 uppercase tracking-widest">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                        Network Secure
                    </span>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        Node: <?= $_SERVER['SERVER_ADDR'] ?? '127.0.0.1' ?>
                    </span>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script>
        // Sidebar Toggle for Mobile
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const toggle = document.getElementById('sidebar-toggle');

        if(toggle) {
            toggle.addEventListener('click', () => {
                sidebar.classList.toggle('sidebar-hidden');
                sidebar.classList.toggle('sidebar-visible');
                overlay.classList.toggle('opacity-0');
                overlay.classList.toggle('pointer-events-none');
            });
        }

        if(overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.add('sidebar-hidden');
                sidebar.classList.remove('sidebar-visible');
                overlay.classList.add('opacity-0');
                overlay.classList.add('pointer-events-none');
            });
        }

        // Active Link Highlighting (Extra Polish)
        document.querySelectorAll('nav a').forEach(link => {
            if (link.href === window.location.href) {
                link.classList.add('nav-link-active', 'text-white');
                link.classList.remove('text-slate-400');
            }
        });
    </script>
</body>
</html>
