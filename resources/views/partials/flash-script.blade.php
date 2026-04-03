<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-auto-dismiss]').forEach((element) => {
            const timeout = Number(element.dataset.autoDismiss || 3000);

            window.setTimeout(() => {
                element.classList.add('is-dismissing');

                window.setTimeout(() => {
                    element.remove();
                }, 350);
            }, timeout);
        });
    });
</script>
