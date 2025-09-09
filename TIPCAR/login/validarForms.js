    document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");

    if (form) {
        form.addEventListener("submit", function (e) {
            const email = form.querySelector("input[name='email']").value.trim();
            const senha = form.querySelector("input[name='senha']").value.trim();
            
            if (!email || !senha) {
                e.preventDefault();
                Swal.fire({
                    icon: "warning",
                    title: "Campos obrigatórios",
                    text: "Preencha todos os campos antes de continuar.",
                    confirmButtonColor: "#2575fc"
                });
                return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                Swal.fire({
                    icon: "error",
                    title: "E-mail inválido",
                    text: "Digite um e-mail válido.",
                    confirmButtonColor: "#2575fc"
                });
                return;
            }

        });
    }
});
