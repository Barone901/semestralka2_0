document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById("menuBtn");
    const nav = document.getElementById("mobileNav");
    if (!btn || !nav) return;

    btn.addEventListener("click", () => {
        nav.classList.toggle("hidden");
    });
});
