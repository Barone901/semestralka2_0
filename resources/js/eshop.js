/**
 * Eshop - inicializácia modulov
 */
import Cart from "./modules/cart";
import Search from "./modules/search";
import QuickView from "./modules/quickview";
import FormValidator from "./modules/form-validator";
import Toast from "./modules/toast";
import Services from "./services";

const Eshop = {
    init() {
        Cart.init();
        Search.init();
        QuickView.init();
        FormValidator.init();
    },
};

document.addEventListener("DOMContentLoaded", () => {
    Eshop.init();
});

// export do window len ak naozaj potrebuješ inline onclick (ty používaš v cart page)
window.Eshop = {
    Toast,
    Cart,
    Search,
    QuickView,
    FormValidator,
    Services,
};

export default Eshop;
