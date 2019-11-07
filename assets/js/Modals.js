var Modals = (function() {
    /**
     * Main function.
     * Setting set and event are added
     * @param options
     * @constructor
     */
    function Modal( options ) {
        aSelectors = {
            Modal: '.Modals-Modal',
            Modal_Footer: '.modal-footer',
            Modal_Title: '.modal-title',
            Modal_Body: '.modal-body',
            Modal_Dialog: '.modal-dialog',
            Modal_Content: '.modal-content',
        };

        $(aSelectors.Modal).remove();
        var settings = $.extend({
            Title : null,
            Message: null,
            Size : null,
            Buttons:
                [
                    Buttons.confirm,
                    Buttons.cancel
                ],
            isForm: false,
            onConfirm : function () {},
            onDeny: function(){},
            onInit: function () {},
        }, options );

        //settings validation
        if (!settings.Title){
            throw new Error('Modal title not given');
        }
        if (!settings.Message) {
            throw  new Error('Modal message not given');
        }

        switch (settings.Size) {
            case 'large':
                var modal_size = 'modal-lg';
                break;
            case 'small':
                var modal_size = 'modal-sm';
                break;
            default:
                var modal_size = '';
                break;
        }



        modal = Build_Modal(settings.isForm);
        modal.find(aSelectors.Modal_Dialog).addClass(modal_size);
        //Set Bootsrap modal settings
        modal.modal({
            backdrop: 'static',
            keyboard: false
        });
        //Set titel en message field
        modal.find(aSelectors.Modal_Title).text(settings.Title);
        modal.find(aSelectors.Modal_Body).html(settings.Message);


        //Add buttons to modal footer
        $.each(settings.Buttons, function (iKey, oButton) {
            modal.find(aSelectors.Modal_Footer).append(oButton);
        });
        //Before init & modal show
        settings.onInit.call(this , modal, settings);

        //Show elements
        $(document.body).append(modal);
        modal.modal('show');

        //Binding events
        $(modal.find('.modal-confirm')).on('click', function () {
            if (settings.isForm){
                settings.onConfirm.call(this , modal, modal.find('form').serializeArray());
            } else{
                settings.onConfirm.call(this , modal, false);
            }
            modal.modal('hide');
            $(aSelectors.Modal).remove();
        });

        $(modal.find('.modal-denied')).on('click', function () {
            settings.onDeny.call(this, modal);
            modal.modal('hide');
            $(aSelectors.Modal).remove();
        });


    }

    /**
     * Building Modal DOM structure.
     * @return {jQuery|HTMLElement}
     * @constructor
     */
    var Build_Modal = function (isForm = false){
        var oElement = $(Elements.Modal.dialog);
        oElement.find(aSelectors.Modal_Body).before(Elements.Modal.header);
        oElement.find(aSelectors.Modal_Body).after(Elements.Modal.footer);
        if (isForm){
            oConetent = oElement.find(aSelectors.Modal_Content).html();
            oElement.find(aSelectors.Modal_Content).html(null);
            oForm = $('<form>').append(oConetent);
            oForm.validator();
            oForm.submit(function (e) {
                e.preventDefault();
            });
            oElement.find(aSelectors.Modal_Content).append(oForm);
        }

        oElement.find(aSelectors.Modal_Title).after(Elements.Modal.close);
        return oElement;
    };

    /**
     * Buttons for modal footer
     * @type {{confirm: string, allow: string, deny: string, cancel: string}}
     */
    var Buttons = {
        confirm: '<button class="btn modal-confirm" type="submit">Oke</button>',
        allow : '<button class="btn modal-confirm" type="submit">Ja</button>',
        deny: '<button class="btn modal-denied" type="reset">Nee</button>',
        cancel: '<button class="btn modal-denied" type="reset">Annuleren</button>',
    };
    /**
     * Dom Elements needed for Modal build up.
     * @type {{Modal: {dialog: string, header: string, footer: string, close: string}}}
     */
    var Elements = {
        Modal : {
            dialog:
                "<div class='modal Modals-Modal' tabindex='-1' role='dialog' aria-hidden='true'>" +
                "<div class='modal-dialog'>" +
                "<div class='modal-content'>" +
                "<div class='modal-body'>" +
                "</div>" +
                "</div>" +
                "</div>" +
                "</div>",
            header:
                "<div class='modal-header'>" +
                "<h4 class='modal-title'></h4>" +
                "</div>",
            footer: "<div class='modal-footer'></div>",

            close : "<button type='reset' class='modal-denied close' type='reset' aria-hidden='true'>&times;</button>",
            custom_body: "<div class='row'><div class='modal-icon col-4'></div><div class='modal-message col-8'></div></div>"
        },

    };

    /**
     * Template builder & constructor
     * @param oSettings
     * @param sTheme
     */
    var template = function (oSettings, sTheme = null) {
        var settings = $.extend({
            onInit: function (oModal, oSettings) {
                $(oModal).find(aSelectors.Modal_Body).html(null);
                $(oModal).addClass(sTheme);
                $(oModal).find(aSelectors.Modal_Body).append(Elements.Modal.custom_body);
                $(oModal).find('.modal-message').html(oSettings.Message);
            }
        }, oSettings );

        Modal( settings );
    };

    /**
     * Inform template call
     * @param options
     * @constructor
     */
    var Inform = function ( options ) {
        var settings = $.extend({
            Buttons:
                [
                    $(Buttons.allow),
                ],
        }, options);
        template(settings, 'Modal-Info');
    };

    /**
     * Error template call
     * @param options
     * @constructor
     */
    var Error = function ( options ) {
        var settings = $.extend({
            Buttons:
                [
                    $(Buttons.confirm),
                ],
        }, options);
        template(settings, 'Modal-Error');
    };

    /**
     * Warning template call
     * @param options
     * @constructor
     */
    var Warning = function ( options ) {
        var settings = $.extend({
            Buttons:
                [
                    $(Buttons.allow),
                    $(Buttons.deny)
                ],
        }, options);
        template(settings, 'Modal-Warning');
    };

    /**
     * Success template call
     * @param options
     * @constructor
     */
    var Success = function ( options ) {
        var settings = $.extend({
            Buttons:
                [
                    $(Buttons.confirm)
                ],
        }, options);
        template(settings, 'Modal-Success');
    };



    return {
        /*Custom call*/
        Custom: Modal,
        /*Template calls*/
        Inform: Inform,
        Error: Error,
        Warning: Warning,
        Success: Success,

        Buttons: Buttons
    };

})();
