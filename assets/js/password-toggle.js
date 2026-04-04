document.addEventListener("DOMContentLoaded", function () {
    const passwordFields = document.querySelectorAll(".passwordFieldWrap");

    passwordFields.forEach(function (field) {
        const input = field.querySelector("input[type='password'], input[type='text']");
        const toggle = field.querySelector(".passwordToggle");

        if (!input || !toggle) {
            return;
        }

        const showLabel = toggle.getAttribute("data-show-label") || "Show";
        const hideLabel = toggle.getAttribute("data-hide-label") || "Hide";

        function syncState() {
            const isVisible = input.type === "text";
            toggle.textContent = isVisible ? hideLabel : showLabel;
            toggle.setAttribute("aria-label", isVisible ? hideLabel : showLabel);
            toggle.setAttribute("aria-pressed", isVisible ? "true" : "false");
        }

        toggle.addEventListener("click", function () {
            input.type = input.type === "password" ? "text" : "password";
            syncState();
            input.focus({ preventScroll: true });
        });

        syncState();
    });
});
