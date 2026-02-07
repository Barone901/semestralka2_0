// resources/js/modules/Menu.js
/**
 * Menu modul
 * - mobilne menu
 * - account dropdown (podporuje viac instancii: top aj bottom)
 * - mobile profile dropdown
 * - koordinacia s cart dropdown (zatvori ine dropdowny)
 */
const Menu = {
    init() {
        this.initMobileMenu();
        this.initAccountDropdowns();
        this.initMobileProfileDropdown();
        this.initGlobalOutsideClose();
    },

    initMobileMenu() {
        const btn = document.getElementById("menuBtn");
        const nav = document.getElementById("mobileNav");
        if (!btn || !nav) return;

        btn.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();

            this.closeCartDropdown();
            this.closeMobileProfileDropdown();
            this.closeAccountDropdownById("account-dropdown-bottom");
            this.closeAccountDropdownById("account-dropdown");

            nav.classList.toggle("hidden");
            btn.setAttribute("aria-expanded", nav.classList.contains("hidden") ? "false" : "true");
        });
    },

    initAccountDropdowns() {
        const configs = [
            {
                btnId: "account-btn",
                dropdownId: "account-dropdown",
                arrowId: "account-arrow",
                wrapperId: "account-wrapper",
            },
            {
                btnId: "account-btn-bottom",
                dropdownId: "account-dropdown-bottom",
                arrowId: "account-arrow-bottom",
                wrapperId: "account-wrapper-bottom",
            },
        ];

        configs.forEach((cfg) => this.initAccountDropdown(cfg));
    },

    initAccountDropdown({ btnId, dropdownId, arrowId, wrapperId }) {
        const btn = document.getElementById(btnId);
        const dropdown = document.getElementById(dropdownId);
        const arrow = document.getElementById(arrowId);
        const wrapper = document.getElementById(wrapperId);

        if (!btn || !dropdown || !wrapper) return;

        let isOpen = false;

        const open = () => {
            this.closeCartDropdown();
            this.closeMobileProfileDropdown();
            this.closeMobileNav();

            isOpen = true;
            btn.setAttribute("aria-expanded", "true");

            dropdown.classList.remove("hidden");
            setTimeout(() => {
                dropdown.classList.remove("opacity-0", "translate-y-2");
                dropdown.classList.add("opacity-100", "translate-y-0");
            }, 10);

            arrow?.classList.add("rotate-180");
        };

        const close = () => {
            isOpen = false;
            btn.setAttribute("aria-expanded", "false");

            dropdown.classList.add("opacity-0", "translate-y-2");
            dropdown.classList.remove("opacity-100", "translate-y-0");

            setTimeout(() => dropdown.classList.add("hidden"), 200);

            arrow?.classList.remove("rotate-180");
        };

        btn.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            isOpen ? close() : open();
        });

        dropdown.__close = close;

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && isOpen) close();
        });
    },

    initMobileProfileDropdown() {
        const btn = document.getElementById("mobile-profile-btn");
        const dropdown = document.getElementById("mobile-profile-dropdown");
        const arrow = document.getElementById("mobile-profile-arrow");

        if (!btn || !dropdown) return;

        const open = () => {
            this.closeCartDropdown();
            this.closeMobileNav();
            this.closeAccountDropdownById("account-dropdown-bottom");
            this.closeAccountDropdownById("account-dropdown");

            btn.setAttribute("aria-expanded", "true");
            arrow?.classList.add("rotate-180");

            dropdown.classList.remove("hidden");
            setTimeout(() => {
                dropdown.classList.remove("opacity-0", "translate-y-2");
                dropdown.classList.add("opacity-100", "translate-y-0");
            }, 10);
        };

        const close = () => {
            btn.setAttribute("aria-expanded", "false");
            arrow?.classList.remove("rotate-180");

            dropdown.classList.add("opacity-0", "translate-y-2");
            dropdown.classList.remove("opacity-100", "translate-y-0");
            setTimeout(() => dropdown.classList.add("hidden"), 200);
        };

        btn.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();

            const isOpen = btn.getAttribute("aria-expanded") === "true";
            if (isOpen) close();
            else open();
        });

        dropdown.__close = close;

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") close();
        });
    },

    initGlobalOutsideClose() {
        document.addEventListener("click", (e) => {
            const mobileProfileWrapper = document.getElementById("mobile-profile-wrapper");
            const cartWrapper = document.getElementById("cart-wrapper");
            const mobileNav = document.getElementById("mobileNav");
            const menuBtn = document.getElementById("menuBtn");

            if (mobileProfileWrapper && !mobileProfileWrapper.contains(e.target)) {
                this.closeMobileProfileDropdown();
            }

            if (cartWrapper && !cartWrapper.contains(e.target)) {
                this.closeCartDropdown();
            }

            if (
                mobileNav &&
                !mobileNav.classList.contains("hidden") &&
                !mobileNav.contains(e.target) &&
                (!menuBtn || !menuBtn.contains(e.target))
            ) {
                mobileNav.classList.add("hidden");
                menuBtn?.setAttribute("aria-expanded", "false");
            }
        });
    },

    closeMobileNav() {
        const nav = document.getElementById("mobileNav");
        const btn = document.getElementById("menuBtn");
        if (!nav) return;
        nav.classList.add("hidden");
        btn?.setAttribute("aria-expanded", "false");
    },

    closeMobileProfileDropdown() {
        const dropdown = document.getElementById("mobile-profile-dropdown");
        dropdown?.__close?.();
    },

    closeCartDropdown() {
        const dropdown = document.getElementById("cart-dropdown");
        const btn = document.getElementById("cart-btn");

        if (!dropdown) return;

        dropdown.classList.add("opacity-0", "translate-y-2");
        dropdown.classList.remove("opacity-100", "translate-y-0");
        setTimeout(() => dropdown.classList.add("hidden"), 200);

        if (btn) btn.setAttribute("aria-expanded", "false");
    },

    closeAccountDropdownById(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        dropdown?.__close?.();
    },
};

export default Menu;
