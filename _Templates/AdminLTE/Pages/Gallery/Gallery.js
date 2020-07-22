var Gallery = {
    DP : null,
    Uploader: function () {
        $("#dp-UploadImage").dropzone({
            paramName:"Image",
            acceptedFiles: "image/*",
            addRemoveLinks: true,
            params: {
                type: "upload"
            },
            autoProcessQueue: false,
            parallelUploads: 200,
            url: window.location.href,
            timeout: 900000,

            //Messages
            dictDefaultMessage: "Klik hier of sleep je afbeeldingen hier naar toe.",
            dictFileTooBig: "Dit bestand is {{filesize}} groot, Het mag niet groter zijn dan {{maxFilesize}}",
            dictInvalidFileType: "Dit typen bestand wordt niet ondersteund",
            dictCancelUpload: "Upload annuleren",
            dictRemoveFile: "Bestand verwijderen",
            init: function() {
                var ImageDropzone = this;
                $(".upload-images").on("click", function () {
                    for (var i = 0; i < ImageDropzone.getAcceptedFiles().length; i++) {
                        setTimeout(ImageDropzone.processFile(ImageDropzone.getAcceptedFiles()[i]), 1000);
                    }
                });
                this.on("success", function(file, responseText) {
                    if(ImageDropzone.getQueuedFiles().length === 0 && ImageDropzone.getUploadingFiles().length === 0){
                        $(".image-alert").remove();
                        ImageDropzone.removeAllFiles(true) ;
                    }
                    console.log(file, responseText);
                    response = JSON.parse(responseText);
                    $(".images").append(response.data);
                });
                this.on("sending", function(file, xhr, formData) {
                    /*Called just before each file is sent*/
                    xhr.ontimeout = (() => {
                        /*Execute on case of timeout only*/
                        console.log('Server Timeout')
                    });
                });
                this.on("complete", function(file) {
                    setTimeout(function () {
                        Dashboard.System.Alerts.Success(file.name + " toegevoegd");
                        ImageDropzone.removeFile(file);
                    }, 1000)
                });
                this.on("queuecomplete", function(file) {
                    $("#uploadImage").modal("hide");
                });
            }
        })
    },
    init: function () {
        this.Uploader();
        this.Sortable.init();
    },
    Sortable: {
        init: function () {
            $(".images").sortable({
                containment: ".content-wrapper",
                cursor: "move",
                handle: ".handle",
                placeholder: "sortable-placeholder",
                revert: true,
                start: function (event, ui) {
                    $(ui.placeholder).css("height", ($(ui.helper).height()-15)).css("width", $(ui.helper).width())
                },
                update: function (event, ui) {
                    item = $(ui.item);
                    Gallery.Sortable.updateOrder(item.data("image-id"), item.index());
                }
            })
        },
        updatePublic: function(Image_Id){
            Dashboard.Xhr({
                data: {
                    type: "public",
                    Image_Id: Image_Id,
                },
                success: function (response) {
                    item = $("[data-image-id="+Image_Id+"]")
                    position = item.index();
                    item.remove();
                    if(position === 0) {
                        $(".images").prepend(response.data);
                    } else {
                        $(".images > div:nth-child(" + (position) + ")").after(response.data);
                    }
                    Dashboard.System.PageLoad.End();
                    Dashboard.System.Alerts.Success("Opgeslagen")
                }
            })
        },
        updateCover: function(Image_Id){
            Dashboard.Xhr({
                data: {
                    type: "cover",
                    Image_Id: Image_Id,
                },
                success: function (response) {
                    item = $("[data-image-id="+Image_Id+"]")
                    position = item.index();
                    item.remove();
                    if(position === 0) {
                        $(".images").prepend(response.data);
                    } else {
                        $(".images > div:nth-child(" + (position) + ")").after(response.data);
                    }
                    Dashboard.System.PageLoad.End();
                    Dashboard.System.Alerts.Success("Opgeslagen")
                }
            })
        },
        updateOrder: function (Image_Id, Image_Order) {
            Dashboard.Xhr({
                data: {
                    type: "order",
                    Image_Id: Image_Id,
                    Image_Order: Image_Order
                },
                success: function () {
                    Dashboard.System.PageLoad.End();
                    Dashboard.System.Alerts.Success("Positie gewijzigd")
                }
            })
        }
    }
}