import '../css/app.scss';

import { Dropdown } from 'bootstrap';
//import swal from 'sweetalert';

document.addEventListener('DOMContentLoaded', () => {
    enableDropdown
    //new App();
})

const enableDropdown = () => {
    const dropdownElementList = document.querySelectorAll('.dropdown-toggle')
    const dropdownList = [...dropdownElementList].map(dropdownToggleEl => new bootstrap.Dropdown(dropdownToggleEl))

}
require('@fortawesome/fontawesome-free/css/all.min.css');




