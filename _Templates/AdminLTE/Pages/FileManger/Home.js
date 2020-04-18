Modals.Warning({
   Title:"Waarschuwing",
   Message: "Bestanden die geupload worden zijn in te zien voor het hele internet",
   Buttons: [Modals.Buttons.confirm]
});
$(".jstree-anchor").on("click", function () {

});
Filemanger.oFileThree.on('select_node.jstree', function(e) {
   oThree = Filemanger.oFileThree;
   var nodes = oThree.jstree('get_selected',true);
   aNode = nodes[0];
   oElement = "<i class=\"fas fa-file\"></i>";
   $(".file").html("");
   switch (aNode.original.type) {
      case "img":
         oElement = "<img src='/files/" + aNode.original.dir+ "' style='max-width: 100%'>";
         break;
      default:
         oElement = "<i class=\"fas fa-file\"></i>";
         break
   }
   $(".url").html("<a target='_blank' href='"+document.location.origin+"/files/" + aNode.original.dir+ "'>"+document.location.origin+"/files/" + aNode.original.dir+ "</a>");
   $(".name").text(aNode.original.text);
   $(".type").text(aNode.original.type);
   $(".file").append(oElement)
});
Filemanger.loadDirThree();

