var Gallery = {
    DP : null,
    Uploader: function () {
        this.DP = $("#dp-UploadImage").dropzone({
            paramName:"Image",
            acceptedFiles: "",
            addRemoveLinks: true,
            params: {
                type: "upload"
            },
            url: window.location.href
        })
    },
    init: function () {
        this.Uploader();
    }
}