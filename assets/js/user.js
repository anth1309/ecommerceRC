import Swal from 'sweetalert2/dist/sweetalert2.js'

let supprimers = document.querySelectorAll('.deleteUser ')
console.log(supprimers);
for (const supprimer of supprimers) {
    supprimer.addEventListener('click', (e) => {
        e.preventDefault()
        Swal.fire({
            title: "Vous allez supprimer cet utilisateur.",
            html: 'Cette action est irréversible. <br> Souhaitez vous continuer ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#39B55C',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, je supprime',
            cancelButtonText: 'Non, je retourne sur le site'
        }).then((result) => {
            if (result.isConfirmed) {
                document.location.href = supprimer.href
            }
        })
    })
}