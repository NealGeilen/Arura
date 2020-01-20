var Scanner = {
    StartCamera : function () {
        let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
        scanner.addListener('scan', function (content) {
            Scanner.VerifyTicket(content);
            $("#scan-message").text("QR Code gescand");
        });
        Instascan.Camera.getCameras().then(function (cameras) {
            startPageLoad();
            console.log(cameras);
            if (cameras.length > 0) {
                endPageLoad();
                scanner.start(cameras[0]);
            } else {
                endPageLoad();
                addErrorMessage("Geen camera gevonden.");
                console.error('No cameras found.');
            }
        }).catch(function (e) {
            console.error(e);
        });
    },
    VerifyTicket: function (sHash) {
        startPageLoad();
        $.ajax({
            url: ARURA_API_DIR + '/shop/event.php?type=verify-ticket',
            type: 'post',
            dataType: 'json',
            data: {
              Hash: sHash
            },
            statusCode: {
                404: function() {
                    Modals.Error({
                        Title : "Niet juist",
                        Message: "Ticket is niet legitiem"
                    })
                }
            },
            success:function(response){
                console.log(response);
                endPageLoad();
            },
            error: function () {
                endPageLoad();
            }
        });
    }
};

$(document).ready(function () {
Scanner.StartCamera();
});