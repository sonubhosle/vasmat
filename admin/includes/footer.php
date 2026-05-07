        </main>
        
        <!-- Footer Info -->
        <footer class="px-10 py-6 border-t border-slate-200/50 text-center">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">&copy; <?= date('Y') ?> MIT Basmath | Developed with <i class="fas fa-heart text-rose-500"></i> for Excellence</p>
        </footer>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('admin-sidebar');
            const toggle = document.getElementById('sidebar-toggle');
            const overlay = document.getElementById('sidebar-overlay');
            const main = document.querySelector('main');

            function toggleSidebar() {
                sidebar.classList.toggle('sidebar-hidden');
                sidebar.classList.toggle('sidebar-visible');
                overlay.classList.toggle('opacity-0');
                overlay.classList.toggle('opacity-100');
                overlay.classList.toggle('pointer-events-none');
                document.body.classList.toggle('overflow-hidden');
            }

            if(toggle) {
                toggle.addEventListener('click', toggleSidebar);
            }

            if(overlay) {
                overlay.addEventListener('click', toggleSidebar);
            }

            // Page transition effect
            if(main) {
                main.style.opacity = '0';
                main.style.transition = 'opacity 0.5s ease-in-out';
                setTimeout(() => {
                    main.style.opacity = '1';
                }, 100);
            }
        });

        // Initialize Lucide icons if any
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
</body>
</html>
