document.getElementById('add-user-btn').addEventListener('click', function () {
    document.getElementById('add-user').style.display = 'block';
});

document.querySelector('.close').addEventListener('click', function () {
    document.getElementById('add-user').style.display = 'none';
});

function userDelete(user, admin_id) {
    const formData = new FormData();
    formData.append('userCuid', user);

    if (user == admin_id) {
        alert("Vous ne pouvez pas vous supprimer vous même")
    } else {
        var confirmation = confirm("Etes vous sur de vouloir supprimer cet utilisateur ?");
        if (confirmation) {
            fetch('/adminPanel/deleteuser.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors de l\'appel à PHP');
                    }
                    return response.text();
                })
                .then(data => {
                    if (data == 'success') {
                        alert("Utilisateur " + user + " supprimé");
                        window.location.reload();
                    } else {
                        alert("Une erreur s'est produite, l'utilisateur n'a pas été supprimé")
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                });
        }
    }
}

function modifyProfil(cuid) {
    const formData = new FormData();
    formData.append('userCuid', cuid);
    fetch('modifyProfil.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur lors de la requête.');
            }
            window.location.href = '../profil/';
        })
        .catch(error => {
            console.error('Erreur :', error);
        });
}