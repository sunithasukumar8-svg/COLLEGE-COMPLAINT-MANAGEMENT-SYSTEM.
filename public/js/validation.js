document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll("form").forEach(function (form) {

        form.addEventListener("submit", function (event) {

            const errors = [];

            form.querySelectorAll("[required]").forEach(function (field) {
                if (field.value.trim() === "") {
                    field.classList.add("input-error");
                    errors.push("Please fill in all required fields.");
                } else {
                    field.classList.remove("input-error");
                }
            });

            const password = form.querySelector("#password");
            const confirmPassword = form.querySelector("#confirm_password");

            if (password && confirmPassword) {
                if (password.value.length < 6) {
                    password.classList.add("input-error");
                    errors.push("Password must be at least 6 characters.");
                }
                if (password.value !== confirmPassword.value) {
                    confirmPassword.classList.add("input-error");
                    errors.push("Passwords do not match.");
                }
            }

            let box = form.querySelector(".js-error-box");

            if (errors.length > 0) {
                event.preventDefault();
                if (!box) {
                    box = document.createElement("div");
                    box.className = "form-message error js-error-box";
                    form.prepend(box);
                }
                box.textContent = [...new Set(errors)].join(" ");
                box.scrollIntoView({ behavior: "smooth", block: "center" });
            } else if (box) {
                box.remove();
            }
        });

        form.querySelectorAll("input, select, textarea").forEach(function (field) {
            field.addEventListener("input", function () {
                field.classList.remove("input-error");
            });
        });
    });
});