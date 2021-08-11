{foreach $aResourceFiles.arura.js as $file}
    <script src="{$file}"></script>
{/foreach}
{if isset($aResourceFiles.page.js)}
    {foreach $aResourceFiles.page.js as $file}
        <script src="{$file}"></script>
    {/foreach}
{/if}
<script>
    // Loader.Start();
    $(".sidebar-link").each(function (i, el){
        if ($(el).attr("href") === location.pathname){
            $(el).parents(".sidebar-item").addClass("active");
        }
    });
    (function () {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>

{block JsPage}
    {/block}