var Modals = (function() {
    /**
     * Main function.
     * Setting set and event listeners are added
     * @param options
     * @constructor
     */
    function Modal( options ) {
        $('.modal-backdrop').remove();
        $('.Modals-Modal').remove();
        var settings = $.extend({
            Title : null,
            Message: null,
            //lg || sm
            Size : null,
            Theme: 'light',
            Buttons:
                [
                    Buttons.confirm,
                    Buttons.cancel
                ],
            onConfirm : function () {},
            onDeny: function(){},
            onInit: function () {},
        }, options );

        //settings validation
        if (!settings.Title){
            throw new Error('Modal title not given');
        }
        if (!settings.Message){
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



        modal = Build_Modal();
        //Set Bootsrap modal settings
        modal.modal({
            backdrop: 'static',
            keyboard: true
        });
        modal.find('.modal-dialog').addClass(modal_size);
        //Set titel en message field
        modal.find('.modal-title').text(settings.Title);
        modal.find('.modal-body').html(settings.Message);

        switch (settings.Theme) {
            case 'dark' :
                modal.find('.modal-header').css('background-color', '#1a2035');
                modal.find('.modal-footer').css('background-color', '#1a2035');
                modal.find('.modal-content').css('background-color', '#1a2035');
                modal.find('.modal-content').css('color', '#fff');
                break;
            case 'light':
                modal.find('.modal-content').css('background-color', '#f8f9fa');
                break;
        }

        //Add buttons to modal footer
        $.each(settings.Buttons, function (iKey, oButton) {
            modal.find('.modal-footer').append(oButton);
        });
        //Before init & modal show
        settings.onInit.call(this , modal, settings);

        //Show elements
        modal.modal('show');

        //Binding events
        $(modal.find('.modal-confirm')).on('click', function () {
            if (!$(this).hasClass('disabled')){
                settings.onConfirm.call(this , modal);
                $('.modal-confirm').unbind();
                modal.modal('hide');
                $('.modal-backdrop').remove();
                $('.Modals-Modal').remove();
            }
        });

        $(modal.find('.modal-denied')).on('click', function () {
            settings.onDeny.call(this, modal);
            $('.modal-denied').unbind();
            modal.modal('hide');
            $('.modal-backdrop').remove();
            $('.Modals-Modal').remove();
        });


    }

    /**
     * Building Modal DOM structure.
     * @return {jQuery|HTMLElement}
     * @constructor
     */
    var Build_Modal = function (){
        var oElement = $(Elements.Modal.dialog);
        oElement.find('.modal-body').before(Elements.Modal.header);
        oElement.find('.modal-body').after(Elements.Modal.footer);
        oElement.find('.modal-title').after(Elements.Modal.close);
        return oElement;
    };

    /**
     * Buttons for modal footer
     * @type {{confirm: string, allow: string, deny: string, cancel: string}}
     */
    var Buttons = {
        confirm: '<button class="btn modal-confirm">Oke</button>',
        allow : '<button class="btn modal-confirm">Ja</button>',
        deny: '<button class="btn btn-secondary modal-denied">Nee</button>',
        cancel: '<button class="btn btn-secondary modal-denied">Annuleren</button>',
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

            close : "<button type='button' class='modal-denied close' aria-hidden='true'>&times;</button>",

            custom_body: "<div class='row'><div class='modal-icon col-4'></div><div class='modal-message col-8'></div></div>"
        },
        Icons :{
            info : '<i class="material-icons">info</i>',
            error : '<i class="material-icons">error_outline</i>',
            warning: '<i class="material-icons">warning</i>',
            success: '<i class="material-icons">check</i>'
        }

    };

    /**
     * Color types for diffrent templates
     * @type {{info: string, error: string, warning: string, success: string}}
     */
    var Colors = {
        info : '#17a2b8',
        error: '#dc3545',
        warning: '#ffc107',
        success: '#28a745'
    };
    /**
     * Template builder & constructor
     * @param sIcon
     * @param sColor
     * @param oSettings
     */
    var template = function (sIcon, sColor, oSettings) {
        var settings = $.extend({
            onInit: function (oModal, oSettings) {
                $(oModal).find('.modal-header').css('background-color', sColor);
                $(oModal).find('.modal-content').css('border', '2px solid' + sColor);
                $(oModal).find('.modal-body').html(null);
                $(oModal).find('.modal-body').append(Elements.Modal.custom_body);
                $(oModal).find('.modal-icon').append(sIcon).css('color', sColor);
                $(oModal).find('.modal-message').append(oSettings.Message);
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
                    $(Buttons.allow).css('background-color', Colors.info).text('Oké'),
                ],
        }, options);
        template(Elements.Icons.info, Colors.info, settings);
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
                    $(Buttons.confirm).css('background-color', Colors.error),
                ],
        }, options);
        template(Elements.Icons.error, Colors.error, settings);
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
                    $(Buttons.allow).css('background-color', Colors.warning),
                    $(Buttons.deny),
                ],
        }, options);
        template(Elements.Icons.warning, Colors.warning, settings);
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
                    $(Buttons.confirm).css('background-color', Colors.success)
                ],
        }, options);
        template(Elements.Icons.success, Colors.success, settings);
    };

    var edit = function ( options ) {
        var setting = $.extend({
            Size : 'large'
        }, options);
        Modal(setting);
    };

    return {
        /*Custom call*/
        Custom: Modal,
        /*Template calls*/
        Inform: Inform,
        Error: Error,
        Warning: Warning,
        Success: Success,
        Edit : edit,

        Buttons: Buttons
    };

})();
