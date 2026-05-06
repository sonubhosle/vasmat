        </main>
    </div>
    <script>
        // Global fade-in effect for a premium feel
        document.addEventListener('DOMContentLoaded', () => {
            const main = document.querySelector('main');
            if(main) {
                main.classList.add('opacity-0', 'transition-opacity', 'duration-700');
                setTimeout(() => {
                    main.classList.remove('opacity-0');
                    main.classList.add('opacity-100');
                }, 50);
            }
        });
    </script>
</body>
</html>