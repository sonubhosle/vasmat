
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        // MOBILE NAVIGATION MENU
        const openMobile = document.getElementById("openMobile");
        const closeMobile = document.getElementById("closeMobile");
        const mobileOverlay = document.getElementById("mobileOverlay");
        const mobileMenu = document.getElementById("mobileMenu");
        const mobileBg = document.getElementById("mobileBg");

        if (openMobile) {
            openMobile.onclick = () => {
                if (mobileOverlay) mobileOverlay.classList.remove("opacity-0", "pointer-events-none");
                if (mobileMenu) mobileMenu.classList.remove("-translate-x-full");
                document.body.style.overflow = "hidden";
            };
        }

        if (closeMobile) {
            closeMobile.onclick = closeSidebar;
        }

        if (mobileBg) {
            mobileBg.onclick = closeSidebar;
        }

        function closeSidebar() {
            if (mobileOverlay) mobileOverlay.classList.add("opacity-0", "pointer-events-none");
            if (mobileMenu) mobileMenu.classList.add("-translate-x-full");
            document.body.style.overflow = "";
        }

        window.toggleAccordion = function (id) {
            const content = document.getElementById(id);
            const icon = document.getElementById("icon-" + id);
            const dot = document.getElementById("dot-" + id);

            if (!content) {
                return;
            }

            const isCurrentlyOpen = content.getAttribute('data-open') === 'true';

            if (isCurrentlyOpen) {
                // Close the accordion
                content.style.maxHeight = '0px';
                content.style.opacity = '0';
                content.setAttribute('data-open', 'false');
                if (icon) icon.classList.remove("rotate-180", "text-amber-600");
                if (dot) {
                    dot.classList.remove("bg-amber-600");
                    dot.classList.add("bg-slate-200");
                }
                return;
            }

            // Close all other accordions
            const accordionIds = ['mobDepts', 'mobCourses', 'mobFacilities', 'mobStudent', 'mobAuth'];
            accordionIds.forEach(accId => {
                if (accId !== id) {
                    const acc = document.getElementById(accId);
                    if (acc) {
                        acc.style.maxHeight = '0px';
                        acc.style.opacity = '0';
                        acc.setAttribute('data-open', 'false');

                        const accIcon = document.getElementById("icon-" + accId);
                        if (accIcon) accIcon.classList.remove("rotate-180", "text-amber-600");

                        const accDot = document.getElementById("dot-" + accId);
                        if (accDot) {
                            accDot.classList.remove("bg-amber-600");
                            accDot.classList.add("bg-slate-200");
                        }
                    }
                }
            });

            // Open the target accordion
            setTimeout(() => {
                // Set to auto to measure height
                content.style.maxHeight = 'none';

                // Force reflow to apply the change
                const height = content.scrollHeight;

                // Set actual max-height for animation
                content.style.maxHeight = height + 'px';
                content.style.opacity = '1';
                content.setAttribute('data-open', 'true');

                if (icon) icon.classList.add("rotate-180", "text-amber-600");
                if (dot) {
                    dot.classList.remove("bg-slate-200");
                    dot.classList.add("bg-amber-600");
                }
            }, 10);
        };

        // DESKTOP HEADER COLLAPSE ON SCROLL
        const bigHeader = document.getElementById("bigHeader");
        const navBar = document.getElementById("navBar");
        const brandMiniDesktop = document.getElementById("brandMiniDesktop");
        const brandMiniMobile = document.getElementById("brandMiniMobile");
        const navInner = document.getElementById("navInner");
        let isCollapsed = !bigHeader; // If no bigHeader, treat as already collapsed

        // On non-index pages, show the mini brand immediately 
        if (!bigHeader) {
            if (brandMiniDesktop) {
                brandMiniDesktop.classList.remove("opacity-0", "-translate-x-10", "pointer-events-none");
            }
            if (navInner) {
                navInner.classList.remove("justify-center");
                navInner.classList.add("justify-end");
            }
        }

        window.addEventListener("scroll", () => {
            if (!bigHeader) return; // Skip scroll logic on non-index pages

            if (window.scrollY > 120 && !isCollapsed) {
                isCollapsed = true;
                bigHeader.classList.remove("max-h-96", "pt-6", "pb-3", "opacity-100");
                bigHeader.classList.add("max-h-0", "opacity-0", "pb-0", "pt-0");
                if (navBar) navBar.classList.add("py-3", "shadow-lg", "border-b", "border-slate-100");

                // Desktop changes
                if (brandMiniDesktop) brandMiniDesktop.classList.remove("opacity-0", "-translate-x-10", "pointer-events-none");
                if (navInner) {
                    navInner.classList.remove("justify-center");
                    navInner.classList.add("justify-end");
                }

                // Mobile changes
                if (brandMiniMobile) brandMiniMobile.classList.remove("opacity-0", "-translate-x-10", "pointer-events-none");

            } else if (window.scrollY <= 0 && isCollapsed) {
                isCollapsed = false;
                bigHeader.classList.remove("max-h-0", "opacity-0", "pb-0", "pt-0");
                bigHeader.classList.add("max-h-96", "pt-6", "pb-3", "opacity-100");
                if (navBar) navBar.classList.remove("py-3", "shadow-lg", "border-b", "border-slate-100");

                // Desktop changes
                if (brandMiniDesktop) brandMiniDesktop.classList.add("opacity-0", "-translate-x-10", "pointer-events-none");
                if (navInner) {
                    navInner.classList.remove("justify-end");
                    navInner.classList.add("justify-center");
                }

                // Mobile changes
                if (brandMiniMobile) brandMiniMobile.classList.add("opacity-0", "-translate-x-10", "pointer-events-none");
            }
        });
    });
})();



