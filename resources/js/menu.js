/**
 * Menu modul
 * - mobilne menu
 * - account dropdown (podporuje viac instancii: top aj bottom)
 */
const Menu = {
    init() {
        this.initMobileMenu();
        this.initAccountDropdowns();
    },

    initMobileMenu() {
        const btn = document.getElementById("menuBtn");
        const nav = document.getElementById("mobileNav");

        if (!btn || !nav) return;

        btn.addEventListener("click", () => {
            nav.classList.toggle("hidden");
        });
    },

    /**
     * Najde vsetky account dropdown instancie (top aj bottom) a zapne ich.
     * Funguje pre:
     * - account-btn / account-dropdown / account-arrow / account-wrapper
     * - account-btn-bottom / account-dropdown-bottom / account-arrow-bottom / account-wrapper-bottom
     */
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

    /**
     * Zapne jednu instanciu dropdownu podla ID configu.
     */
    initAccountDropdown({ btnId, dropdownId, arrowId, wrapperId }) {
        const btn = document.getElementById(btnId);
        const dropdown = document.getElementById(dropdownId);
        const arrow = document.getElementById(arrowId);
        const wrapper = document.getElementById(wrapperId);

        if (!btn || !dropdown || !wrapper) return;

        let isOpen = false;

        const onOutsideClickCapture = (e) => {
            // ked je otvorene a kliknes mimo wrapper -> zavri a "zozer" klik (nic pod tym sa neaktivuje)
            if (isOpen && !wrapper.contains(e.target)) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                close();
            }
        };

        const open = () => {
            isOpen = true;

            dropdown.classList.remove("hidden");

            // animacia (opacity + translate)
            setTimeout(() => {
                dropdown.classList.remove("opacity-0", "translate-y-2");
                dropdown.classList.add("opacity-100", "translate-y-0");
            }, 10);

            arrow?.classList.add("rotate-180");

            // modalita: prvy klik mimo iba zavrie dropdown a neklikne nic pod tym
            document.addEventListener("click", onOutsideClickCapture, true);
        };

        const close = () => {
            isOpen = false;

            dropdown.classList.add("opacity-0", "translate-y-2");
            dropdown.classList.remove("opacity-100", "translate-y-0");

            setTimeout(() => dropdown.classList.add("hidden"), 200);

            arrow?.classList.remove("rotate-180");

            document.removeEventListener("click", onOutsideClickCapture, true);
        };

        btn.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            isOpen ? close() : open();
        });

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && isOpen) close();
        });
    },
};

export default Menu;
