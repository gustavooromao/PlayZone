(function () {
    "use strict";

    animarCards();
    configurarBusca();
    configurarModalExclusao();
    configurarToasts();

    /** Cada card entra com fade + slide-up, atrasado em 60ms em relação ao anterior. */
    function animarCards() {
        var cards = document.querySelectorAll(".card");
        cards.forEach(function (card, indice) {
            window.setTimeout(function () {
                card.classList.add("is-visible");
            }, indice * 60);
        });
    }

    /** Filtra a grade em tempo real por título, plataforma ou status (data-search). */
    function configurarBusca() {
        var campo = document.getElementById("busca");
        var grade = document.getElementById("grade-jogos");
        var vazio = document.getElementById("sem-resultado");
        var contador = document.getElementById("contador");

        if (!campo || !grade) {
            return;
        }

        campo.addEventListener("input", function () {
            var termo = campo.value.trim().toLowerCase();
            var visiveis = 0;

            grade.querySelectorAll(".card").forEach(function (card) {
                var texto = card.getAttribute("data-search") || "";
                var bate = termo === "" || texto.indexOf(termo) !== -1;
                card.classList.toggle("is-hidden", !bate);
                if (bate) {
                    visiveis += 1;
                }
            });

            if (vazio) {
                vazio.classList.toggle("is-hidden", visiveis !== 0 || termo === "");
            }

            if (contador) {
                contador.textContent = visiveis + " jogo(s) na PlayZone";
            }
        });
    }

    /** Substitui o confirm() nativo: abre o modal e só navega para deletar.php se confirmar. */
    function configurarModalExclusao() {
        var modal = document.getElementById("modal-excluir");
        var confirmar = document.getElementById("modal-confirmar");
        var texto = document.getElementById("modal-texto");

        if (!modal || !confirmar) {
            return;
        }

        document.querySelectorAll("[data-confirm-delete]").forEach(function (link) {
            link.addEventListener("click", function (evento) {
                evento.preventDefault();
                var titulo = link.getAttribute("data-titulo") || "este jogo";
                if (texto) {
                    texto.textContent = "Excluir \"" + titulo + "\"? Essa ação não pode ser desfeita.";
                }
                confirmar.setAttribute("href", link.getAttribute("href"));
                modal.classList.add("is-open");
            });
        });

        modal.querySelectorAll("[data-modal-close]").forEach(function (el) {
            el.addEventListener("click", function () {
                modal.classList.remove("is-open");
            });
        });

        document.addEventListener("keydown", function (evento) {
            if (evento.key === "Escape") {
                modal.classList.remove("is-open");
            }
        });
    }

    /** Toasts da sessão deslizam na entrada e saem sozinhos depois de 4s. $*/
    function configurarToasts() {
        document.querySelectorAll(".toast[data-autohide]").forEach(function (toast) {
            window.setTimeout(function () {
                toast.classList.remove("is-active");
            }, 4000);
        });
    }
})();
