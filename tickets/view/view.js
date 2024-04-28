document.getElementById('advanced-search-btn').addEventListener('click', function () {
    document.getElementById('advenced-search').style.display = 'block';
});

document.querySelector('.close').addEventListener('click', function () {
    document.getElementById('advenced-search').style.display = 'none';
});

var ticketId = "<?php echo $_GET['ticket']; ?>";
var pdfViewer = document.getElementById('pdf-viewer');
var pdfPath = "view/content/" + ticketId + ".pdf";
pdfViewer.setAttribute('src', pdfPath);

var http = new XMLHttpRequest();
http.open('HEAD', pdfPath, false);
http.send();
if (http.status === 404) {
    alert("Le fichier recherché n'est pas trouvé. Redirection en cours...");
    setTimeout(function () {
        window.location.href = "view";
    }, 2000);
}
